<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\Subject;
use App\Models\Library\ExamType;
use App\Models\Library\Resource;
use App\Models\Library\ResourceCategory;
use App\Models\Library\ResourceFile;
use App\Models\Library\TakedownRequest;
use App\Models\User;
use App\Services\Curriculum\CurriculumTree;
use FPDF;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use ZipArchive;

/** Acceptance criteria 1 (library part), 5 and 6, plus upload and download security. */
class ResourceLibraryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('library');
    }

    private function pdf(int $pages = 6): string
    {
        $pdf = new FPDF;
        $pdf->SetFont('Helvetica', '', 14);
        for ($i = 1; $i <= $pages; $i++) {
            $pdf->AddPage();
            $pdf->Cell(0, 10, "Question page {$i}");
        }

        return $pdf->Output('S');
    }

    private function pdfUpload(int $pages = 6, string $name = 'paper.pdf'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, $this->pdf($pages));
    }

    private function pageCount(ResourceFile $file): int
    {
        return (new Fpdi)->setSourceFile(Storage::disk('library')->path($file->path));
    }

    private function editor(string $role = 'Content Manager'): User
    {
        return User::factory()->staff($role)->create();
    }

    /** @param array<string, mixed> $overrides */
    private function details(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Core Mathematics Paper 2: worked solutions',
            'category_id' => ResourceCategory::where('name', 'Solutions and worked answers')->value('id'),
            'audience' => 'student',
            'subject_id' => Subject::where('name', 'Core Mathematics')->value('id'),
            'access_level' => 'premium',
            'copyright_holder' => 'Otisam',
        ], $overrides);
    }

    /** Create through the admin UI and publish; returns the resource. */
    private function publishedResource(array $overrides = [], int $pages = 6): Resource
    {
        $editor = $this->editor();
        $this->actingAs($editor)->post(route('admin.library.resources.store'), $this->details($overrides) + ['file' => $this->pdfUpload($pages)])
            ->assertSessionHasNoErrors();
        $resource = Resource::latest('id')->firstOrFail();
        $this->post(route('admin.library.resources.status', $resource), ['status' => 'published'])->assertSessionHas('success');
        auth()->logout();
        $this->flushSession();

        return $resource->fresh();
    }

    public function test_an_uploaded_past_paper_opens_its_year(): void
    {
        $resource = $this->publishedResource([
            'title' => 'WASSCE 2019 Core Mathematics Paper 1',
            'category_id' => ResourceCategory::where('name', 'WASSCE past questions')->value('id'),
            'exam_type_id' => ExamType::where('name', 'WASSCE (May/June)')->value('id'),
            'exam_year' => 2019,
            'paper_number' => '1',
            'access_level' => 'free',
        ]);

        $this->get(route('library.index', ['subject' => 'core-mathematics']))->assertOk()
            ->assertSee(route('library.index', ['subject' => 'core-mathematics', 'year' => 2019]))
            ->assertSee('WASSCE 2018: not available yet');
        $this->get(route('library.index', ['subject' => 'core-mathematics', 'year' => 2019]))->assertOk()->assertSee($resource->title);
        $this->get(route('library.index'))->assertOk()->assertSee('1 past paper');
    }

    public function test_advanced_filters_narrow_by_series_year_and_paper(): void
    {
        $category = ResourceCategory::where('name', 'WASSCE past questions')->value('id');
        $mayJune = ExamType::where('name', 'WASSCE (May/June)')->value('id');
        $novDec = ExamType::where('name', 'WASSCE (Nov/Dec)')->value('id');
        $school = $this->publishedResource(['title' => 'WASSCE 2019 May/June Core Mathematics Paper 1', 'category_id' => $category, 'exam_type_id' => $mayJune, 'exam_year' => 2019, 'paper_number' => '1', 'access_level' => 'free']);
        $private = $this->publishedResource(['title' => 'WASSCE 2018 Nov/Dec Core Mathematics Paper 2', 'category_id' => $category, 'exam_type_id' => $novDec, 'exam_year' => 2018, 'paper_number' => '2', 'access_level' => 'free']);

        $this->get(route('library.index', ['level' => 'shs']))->assertOk()
            ->assertSee('Advanced filters')->assertSee('WASSCE Nov/Dec (private candidates)')->assertDontSee('>BECE<', false);

        $this->get(route('library.index', ['exam' => $novDec]))->assertOk()->assertSee($private->title)->assertDontSee($school->title);
        $this->get(route('library.index', ['paper' => '1']))->assertOk()->assertSee($school->title)->assertDontSee($private->title);
        $this->get(route('library.index', ['year' => 2018, 'level' => 'shs']))->assertOk()->assertSee($private->title)->assertDontSee($school->title);

        // The year grid follows the chosen series.
        $this->get(route('library.index', ['subject' => 'core-mathematics', 'exam' => $mayJune]))->assertOk()
            ->assertSee('WASSCE 2018: not available yet')->assertDontSee('WASSCE 2019: not available yet');
        $this->get(route('library.index', ['paper' => '3']))->assertSessionHasNoErrors()->assertOk()->assertSee('No documents match');
    }

    public function test_chief_examiners_reports_are_listed_by_subject_and_year(): void
    {
        $report = $this->publishedResource([
            'title' => 'WASSCE 2019 Core Mathematics: examiners report',
            'category_id' => ResourceCategory::where('name', 'Chief Examiners reports')->value('id'),
            'exam_type_id' => ExamType::where('name', 'WASSCE (May/June)')->value('id'),
            'exam_year' => 2019,
            'access_level' => 'free',
        ]);

        $this->get(route('library.index', ['subject' => 'core-mathematics']))->assertOk()
            ->assertSee("Chief Examiners' reports", false)->assertSee('2019 · WASSCE (May/June)')
            ->assertSee(route('library.show', $report))
            // A report is not a past paper: its year stays "not yet" on the paper grid.
            ->assertSee('WASSCE 2019: not available yet');
        $this->get(route('library.index'))->assertOk()->assertDontSee('1 past paper');
        $this->get(route('library.index', ['category' => 'chief-examiners-reports']))->assertOk()->assertSee($report->title);
    }

    public function test_upload_stores_privately_and_generates_a_short_preview(): void
    {
        $resource = $this->publishedResource();
        $main = $resource->mainFile;

        $this->assertSame($resource->status === 'draft' ? null : 'published', $resource->status);
        Storage::disk('library')->assertExists($main->path);
        $this->assertSame(6, $main->page_count);
        $this->assertSame('ready', $main->preview_status);
        $this->assertSame(2, $this->pageCount($resource->previewFile));
        $this->assertStringStartsNotWith(public_path(), config('filesystems.disks.library.root'));
        $this->assertFalse(config('filesystems.disks.library.serve'));
    }

    public function test_preview_is_never_most_of_the_document(): void
    {
        $two = $this->publishedResource(['title' => 'Two pages'], 2);
        $this->assertSame(1, $this->pageCount($two->previewFile));

        $one = $this->publishedResource(['title' => 'One page'], 1);
        $this->assertNull($one->previewFile);
        $this->assertSame('not_applicable', $one->mainFile->preview_status);
    }

    public function test_guest_sees_premium_preview_but_never_the_full_document(): void
    {
        $resource = $this->publishedResource();

        $this->get(route('library.index'))->assertOk()->assertSee($resource->title);
        $this->get(route('library.show', $resource))->assertOk()->assertSee('Premium resource')->assertSee(route('library.preview', $resource));

        $preview = $this->get(route('library.preview', $resource))->assertOk();
        $this->assertSame('application/pdf', $preview->headers->get('Content-Type'));
        $this->assertStringContainsString('no-store', $preview->headers->get('Cache-Control'));

        $this->get(route('library.view', $resource))->assertForbidden();
        $this->get(route('library.download', $resource))->assertRedirect(route('login'));

        // Signed in without premium access: still refused.
        $this->actingAs(User::factory()->student()->create());
        $this->get(route('library.view', $resource))->assertForbidden();
        $this->get(route('library.download', $resource))->assertForbidden();
        $this->assertSame(0, $resource->fresh()->download_count);
    }

    public function test_free_resources_open_for_everyone_and_downloads_need_an_account(): void
    {
        $resource = $this->publishedResource(['access_level' => 'free', 'title' => 'Free revision notes']);

        $this->get(route('library.view', $resource))->assertOk();
        $this->get(route('library.download', $resource))->assertRedirect(route('login'));

        $student = User::factory()->student()->create();
        $download = $this->actingAs($student)->get(route('library.download', $resource))->assertOk();
        $this->assertStringContainsString('attachment', $download->headers->get('Content-Disposition'));
        $this->assertSame(1, $resource->fresh()->download_count);
        $this->assertTrue(DB::table('resource_access_logs')->where('resource_id', $resource->id)->where('user_id', $student->id)->where('action', 'download')->exists());
    }

    public function test_paper_and_linked_solution(): void
    {
        $paper = $this->publishedResource([
            'title' => 'WASSCE 2023 Core Mathematics Paper 2',
            'category_id' => ResourceCategory::where('name', 'WASSCE past questions')->value('id'),
            'exam_type_id' => ExamType::where('code', 'WASSCE-MJ')->value('id'),
            'exam_year' => 2023, 'paper_number' => '2', 'access_level' => 'free', 'copyright_holder' => 'WAEC',
        ]);
        $solution = $this->publishedResource(['title' => 'WASSCE 2023 Core Mathematics Paper 2: worked solutions']);

        $this->actingAs($this->editor())
            ->post(route('admin.library.resources.links.store', $paper), ['linked_resource_id' => $solution->id, 'relation' => 'solution'])
            ->assertSessionHas('success');
        auth()->logout();
        $this->flushSession();

        $this->get(route('library.show', $paper))->assertOk()->assertSee('WASSCE (May/June) · 2023 · Paper 2')->assertSee($solution->title);
        $this->get(route('library.show', $solution))->assertOk()->assertSee('Question paper')->assertSee($paper->title);
        $this->get(route('library.index'))->assertSee('Solution available');
    }

    public function test_waec_papers_cannot_be_premium(): void
    {
        $this->actingAs($this->editor())->post(route('admin.library.resources.store'), $this->details([
            'exam_type_id' => ExamType::where('code', 'BECE')->value('id'), 'access_level' => 'premium', 'copyright_holder' => '',
        ]) + ['file' => $this->pdfUpload()])->assertSessionHasErrors(['access_level', 'copyright_holder']);

        $this->assertSame(0, Resource::count());
    }

    public function test_drafts_and_scheduled_items_stay_hidden_until_due(): void
    {
        $editor = $this->editor();
        $this->actingAs($editor)->post(route('admin.library.resources.store'), $this->details(['access_level' => 'free']) + ['file' => $this->pdfUpload()]);
        $resource = Resource::firstOrFail();
        auth()->logout();

        $this->get(route('library.show', $resource))->assertNotFound();
        $this->get(route('library.preview', $resource))->assertNotFound();
        $this->get(route('library.view', $resource))->assertNotFound();

        $this->actingAs($editor)->post(route('admin.library.resources.status', $resource), ['status' => 'published', 'publish_at' => now()->addDay()->format('Y-m-d H:i')]);
        auth()->logout();
        $this->get(route('library.show', $resource))->assertNotFound();

        $this->travel(2)->days();
        $this->get(route('library.show', $resource))->assertOk();
    }

    public function test_publishing_needs_permission_a_file_and_a_copyright_holder(): void
    {
        Role::create(['name' => 'Uploader', 'guard_name' => 'web'])->givePermissionTo('resources.manage');
        $uploader = User::factory()->staff('Uploader')->create();

        $this->actingAs($uploader)->post(route('admin.library.resources.store'), $this->details(['copyright_holder' => '']));
        $resource = Resource::firstOrFail();

        $this->post(route('admin.library.resources.status', $resource), ['status' => 'published'])->assertForbidden();
        $this->post(route('admin.library.resources.status', $resource), ['status' => 'in_review'])->assertSessionHas('success');

        $this->actingAs($this->editor())->post(route('admin.library.resources.status', $resource), ['status' => 'published'])
            ->assertSessionHas('error', 'Not published: Upload the document first. Record who holds the copyright.');
        $this->assertSame('in_review', $resource->fresh()->status);
    }

    public function test_dangerous_uploads_are_rejected(): void
    {
        $this->actingAs($this->editor());
        $store = fn (UploadedFile $file) => $this->post(route('admin.library.resources.store'), $this->details() + ['file' => $file]);

        $store(UploadedFile::fake()->createWithContent('shell.pdf', '<?php system($_GET["c"]); ?>'))->assertSessionHasErrors('file');
        $store(UploadedFile::fake()->createWithContent('tool.exe', "MZ\x90\x00binary"))->assertSessionHasErrors('file');
        $store(UploadedFile::fake()->createWithContent('page.html', '<script>alert(1)</script>'))->assertSessionHasErrors('file');
        $store(UploadedFile::fake()->createWithContent('script.pdf', "%PDF-1.4\n1 0 obj << /Type /Catalog /OpenAction << /S /JavaScript /JS (app.alert(1)) >> >> endobj\n%%EOF"))
            ->assertSessionHasErrors('file');

        $macro = tempnam(sys_get_temp_dir(), 'docx');
        $zip = new ZipArchive;
        $zip->open($macro, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<Types/>');
        $zip->addFromString('word/document.xml', '<w:document/>');
        $zip->addFromString('word/vbaProject.bin', 'macro');
        $zip->close();
        $store(UploadedFile::fake()->createWithContent('notes.docx', file_get_contents($macro)))->assertSessionHasErrors('file');

        config(['platform.library.max_upload_mb' => 1]);
        $store(UploadedFile::fake()->createWithContent('big.pdf', '%PDF-1.4'.str_repeat('0', 1024 * 1024 + 10)))->assertSessionHasErrors('file');

        $this->assertSame(0, Resource::count());
        $this->assertSame([], Storage::disk('library')->allFiles());
    }

    public function test_clean_office_files_and_images_are_accepted(): void
    {
        $this->actingAs($this->editor());

        $docx = tempnam(sys_get_temp_dir(), 'docx');
        $zip = new ZipArchive;
        $zip->open($docx, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"/>');
        $zip->addFromString('word/document.xml', '<w:document/>');
        $zip->close();
        $this->post(route('admin.library.resources.store'), $this->details(['title' => 'Notes']) + ['file' => UploadedFile::fake()->createWithContent('notes.docx', file_get_contents($docx))])
            ->assertSessionHasNoErrors();

        $image = imagecreatetruecolor(40, 40);
        ob_start();
        imagepng($image);
        $png = ob_get_clean().'HIDDEN-PAYLOAD';
        $this->post(route('admin.library.resources.store'), $this->details(['title' => 'Diagram']) + ['file' => UploadedFile::fake()->createWithContent('diagram.png', $png)])
            ->assertSessionHasNoErrors();

        $stored = Resource::where('title', 'Diagram')->firstOrFail()->mainFile;
        $this->assertStringNotContainsString('HIDDEN-PAYLOAD', Storage::disk('library')->get($stored->path), 'Images are re-encoded.');
        $this->assertSame('not_applicable', $stored->preview_status);
    }

    public function test_replacing_the_document_keeps_history_and_rebuilds_the_preview(): void
    {
        $resource = $this->publishedResource();
        $first = $resource->mainFile;

        $this->actingAs($this->editor())->put(route('admin.library.resources.update', $resource), $this->details() + ['file' => $this->pdfUpload(10, 'v2.pdf')])
            ->assertSessionHasNoErrors();

        $resource->refresh();
        $this->assertFalse($first->fresh()->is_current);
        $this->assertSame(2, $resource->mainFile->version);
        $this->assertSame(10, $resource->mainFile->page_count);
        $this->assertSame(1, ResourceFile::where('resource_id', $resource->id)->where('role', 'preview')->where('is_current', true)->count());
    }

    public function test_teacher_only_material_needs_an_approved_teacher(): void
    {
        $resource = $this->publishedResource([
            'title' => 'SHS 2 lesson plan', 'audience' => 'teacher', 'access_level' => 'free',
            'category_id' => ResourceCategory::where('name', 'Lesson plans')->value('id'),
        ]);

        $this->get(route('library.index'))->assertDontSee('SHS 2 lesson plan');
        $this->actingAs(User::factory()->student()->create())->get(route('library.view', $resource))->assertForbidden();
        $this->actingAs(User::factory()->teacher()->create())->get(route('library.view', $resource))->assertForbidden();
        $this->actingAs(User::factory()->teacher(TeacherVerificationStatus::Approved)->create())->get(route('library.view', $resource))->assertOk();
    }

    public function test_staff_without_library_permissions_are_refused(): void
    {
        $finance = User::factory()->staff('Finance Officer')->create();

        $this->actingAs($finance)->get(route('admin.library.resources.index'))->assertForbidden();
        $this->post(route('admin.library.resources.store'), $this->details())->assertForbidden();
        $this->actingAs(User::factory()->student()->create())->get(route('admin.library.resources.index'))->assertRedirect(route('learn.dashboard'));
    }

    public function test_takedown_request_can_archive_the_item(): void
    {
        $resource = $this->publishedResource(['access_level' => 'free']);

        $this->post(route('copyright.takedown'), [
            'resource_slug' => $resource->slug, 'name' => 'Rights Owner', 'email' => 'owner@example.com',
            'rights_holder' => 'Example Publishers', 'details' => 'This page reproduces chapter 3 of our textbook without permission.', 'declaration' => '1',
        ])->assertRedirect(route('copyright'));

        $request = TakedownRequest::firstOrFail();
        $this->assertSame($resource->id, $request->resource_id);

        $this->actingAs(User::factory()->staff('Administrator')->create())
            ->put(route('admin.library.takedowns.update', $request), ['status' => 'resolved', 'resolution_note' => 'Confirmed; removed.', 'unpublish' => '1'])
            ->assertSessionHas('success');

        $this->assertSame('archived', $resource->fresh()->status);
        auth()->logout();
        $this->get(route('library.show', $resource))->assertNotFound();
    }

    public function test_bulk_upload_creates_drafts_and_skips_unsafe_files(): void
    {
        $this->actingAs($this->editor())->post(route('admin.library.resources.bulk.store'), $this->details() + ['files' => [
            $this->pdfUpload(4, 'wassce_2019_paper_1.pdf'),
            $this->pdfUpload(4, 'wassce-2020-paper-1.pdf'),
            UploadedFile::fake()->createWithContent('evil.pdf', '<?php echo 1;'),
        ]])->assertRedirect()->assertSessionHas('error');

        $this->assertEqualsCanonicalizing(['Wassce 2019 Paper 1', 'Wassce 2020 Paper 1'], Resource::pluck('title')->all());
        $this->assertSame(['draft'], Resource::distinct()->pluck('status')->all());
    }

    public function test_tagged_resources_appear_on_curriculum_topic_pages(): void
    {
        $subject = Subject::where('name', 'Core Mathematics')->firstOrFail();
        $version = CurriculumVersion::create(['education_level_id' => $subject->education_level_id, 'name' => 'Test curriculum', 'status' => 'active', 'effective_from' => '2024-01-01', 'source_document' => 'Test']);
        $tree = app(CurriculumTree::class);
        $strand = $tree->add($version, $subject, null, ['node_type' => 'strand', 'title' => 'Algebra']);
        $topic = $tree->add($version, $subject, $strand, ['node_type' => 'sub_strand', 'title' => 'Quadratics']);

        $resource = $this->publishedResource(['title' => 'Quadratics revision notes', 'access_level' => 'free']);
        $this->actingAs($this->editor())->put(route('admin.library.resources.update', $resource), $this->details(['title' => 'Quadratics revision notes', 'access_level' => 'free'])
            + ['curriculum_nodes_present' => '1', 'curriculum_nodes' => [$topic->id]])->assertSessionHasNoErrors();
        auth()->logout();

        $this->get(route('subjects.topic', [$subject, $topic]))->assertOk()->assertSee('Quadratics revision notes');
        $this->get(route('subjects.topic', [$subject, $strand]))->assertOk()->assertSee('Quadratics revision notes');
        $this->get(route('library.show', $resource))->assertSee('Quadratics');
    }

    public function test_library_admin_pages_load(): void
    {
        $resource = $this->publishedResource();
        TakedownRequest::create(['name' => 'A', 'email' => 'a@example.com', 'rights_holder' => 'A', 'details' => str_repeat('x', 30)]);

        $this->actingAs(User::factory()->staff()->create());
        foreach ([
            route('admin.library.resources.index'),
            route('admin.library.resources.index', ['status' => 'published', 'access' => 'premium']),
            route('admin.library.resources.create'),
            route('admin.library.resources.edit', $resource),
            route('admin.library.resources.bulk'),
            route('admin.library.settings'),
            route('admin.library.takedowns.index'),
            route('copyright'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
