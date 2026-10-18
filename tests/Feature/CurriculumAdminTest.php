<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Programme;
use App\Models\Curriculum\Subject;
use App\Models\User;
use App\Services\Curriculum\CurriculumTree;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** Acceptance criterion 17: administrators manage curriculum data without changing code. */
class CurriculumAdminTest extends TestCase
{
    private function shs(): EducationLevel
    {
        return EducationLevel::where('code', 'SHS')->firstOrFail();
    }

    private function version(string $status = 'draft'): CurriculumVersion
    {
        return CurriculumVersion::create([
            'education_level_id' => $this->shs()->id, 'name' => 'SHS Curriculum (test)', 'status' => $status,
            'effective_from' => '2024-09-01', 'source_document' => 'Test document',
        ]);
    }

    private function subject(string $name = 'Core Mathematics'): Subject
    {
        return Subject::where('name', $name)->firstOrFail();
    }

    public function test_starter_structure_is_seeded_without_inventing_curriculum_content(): void
    {
        $this->assertSame(['SHS 1', 'SHS 2', 'SHS 3'], $this->shs()->classLevels->pluck('name')->all());
        $this->assertTrue(Programme::where('name', 'General Science')->firstOrFail()->subjects->contains('name', 'Physics'));
        $this->assertSame(0, CurriculumVersion::count());
        $this->assertSame(0, CurriculumNode::count());
    }

    public function test_admin_adds_subject_and_topic_and_it_appears_publicly_without_code_changes(): void
    {
        $admin = User::factory()->staff('Content Manager')->create();
        $this->actingAs($admin);

        $this->post(route('admin.curriculum.subjects.store'), [
            'education_level_id' => $this->shs()->id, 'name' => 'Computing', 'category' => 'elective', 'is_active' => '1',
        ])->assertRedirect();
        $computing = Subject::where('name', 'Computing')->firstOrFail();
        $this->assertSame('computing', $computing->slug);

        $this->post(route('admin.curriculum.versions.store'), [
            'education_level_id' => $this->shs()->id, 'name' => 'SHS Curriculum 2024', 'status' => 'draft',
        ])->assertRedirect();
        $version = CurriculumVersion::where('name', 'SHS Curriculum 2024')->firstOrFail();

        $this->post(route('admin.curriculum.tree.store', [$version, $computing]), [
            'node_type' => 'strand', 'code' => 'S1', 'title' => 'Computer Systems', 'is_official' => '1', 'source_ref' => 'p. 10',
        ])->assertRedirect();
        $strand = CurriculumNode::where('code', 'S1')->firstOrFail();

        $this->post(route('admin.curriculum.tree.store', [$version, $computing]), [
            'parent_id' => $strand->id, 'node_type' => 'sub_strand', 'title' => 'Hardware', 'class_level_id' => ClassLevel::where('name', 'SHS 1')->value('id'),
        ])->assertRedirect();
        $child = CurriculumNode::where('title', 'Hardware')->firstOrFail();
        $this->assertSame($strand->id, $child->parent_id);
        $this->assertSame("/{$strand->id}/", $child->path);
        $this->assertSame(1, $child->depth);

        // Draft: not visible to learners yet.
        auth()->logout();
        $this->get(route('subjects.show', $computing))->assertOk()->assertDontSee('Computer Systems');
        $this->get(route('subjects.topic', [$computing, $strand]))->assertNotFound();

        // An active version needs its date and official source.
        $this->actingAs($admin)->put(route('admin.curriculum.versions.update', $version), [
            'education_level_id' => $this->shs()->id, 'name' => $version->name, 'status' => 'active',
        ])->assertSessionHasErrors(['effective_from', 'source_document']);

        $this->put(route('admin.curriculum.versions.update', $version), [
            'education_level_id' => $this->shs()->id, 'name' => $version->name, 'status' => 'active',
            'effective_from' => '2024-09-01', 'source_document' => 'NaCCA SHS Computing curriculum (test citation)',
        ])->assertRedirect();
        $this->assertTrue(AuditLog::where('action', 'version.status')->exists());

        auth()->logout();
        $this->get(route('subjects.index'))->assertOk()->assertSee('Computing');
        $this->get(route('subjects.show', $computing))->assertOk()->assertSee('Computer Systems')->assertSee('Hardware');
        $this->get(route('subjects.topic', [$computing, $child]))->assertOk()->assertSee('Hardware')->assertSee('Computer Systems');
    }

    public function test_official_items_need_a_source_reference(): void
    {
        $this->actingAs(User::factory()->staff('Content Manager')->create());
        $version = $this->version();

        $this->post(route('admin.curriculum.tree.store', [$version, $this->subject()]), [
            'node_type' => 'strand', 'title' => 'Number', 'is_official' => '1',
        ])->assertSessionHasErrors('source_ref');
    }

    public function test_moving_a_node_moves_its_subtree_and_cannot_create_a_cycle(): void
    {
        $tree = app(CurriculumTree::class);
        $version = $this->version();
        $subject = $this->subject();

        $a = $tree->add($version, $subject, null, ['node_type' => 'strand', 'title' => 'A']);
        $b = $tree->add($version, $subject, null, ['node_type' => 'strand', 'title' => 'B']);
        $a1 = $tree->add($version, $subject, $a, ['node_type' => 'sub_strand', 'title' => 'A1']);
        $a11 = $tree->add($version, $subject, $a1, ['node_type' => 'indicator', 'title' => 'A1.1']);

        $this->actingAs(User::factory()->staff('Content Manager')->create());

        // Cannot move A under its own grandchild.
        $this->put(route('admin.curriculum.tree.update', [$version, $subject, $a]), ['node_type' => 'strand', 'title' => 'A', 'parent_id' => $a11->id])
            ->assertSessionHas('error');
        $this->assertNull($a->fresh()->parent_id);

        // Move A1 (with A1.1) under B.
        $this->put(route('admin.curriculum.tree.update', [$version, $subject, $a1]), ['node_type' => 'sub_strand', 'title' => 'A1', 'parent_id' => $b->id])
            ->assertRedirect();
        $this->assertSame($b->id, $a1->fresh()->parent_id);
        $this->assertSame("/{$b->id}/{$a1->id}/", $a11->fresh()->path);
        $this->assertSame(2, $a11->fresh()->depth);

        // Move A1 to the top level.
        $this->put(route('admin.curriculum.tree.update', [$version, $subject, $a1]), ['node_type' => 'sub_strand', 'title' => 'A1', 'parent_id' => '']);
        $this->assertSame("/{$a1->id}/", $a11->fresh()->path);
        $this->assertSame(1, $a11->fresh()->depth);
        $this->assertSame(0, $a1->fresh()->depth);
    }

    public function test_reorder_and_delete_with_children(): void
    {
        $tree = app(CurriculumTree::class);
        $version = $this->version();
        $subject = $this->subject();
        $first = $tree->add($version, $subject, null, ['node_type' => 'strand', 'title' => 'First']);
        $second = $tree->add($version, $subject, null, ['node_type' => 'strand', 'title' => 'Second']);
        $tree->add($version, $subject, $second, ['node_type' => 'sub_strand', 'title' => 'Child']);

        $this->actingAs(User::factory()->staff('Content Manager')->create());
        $this->post(route('admin.curriculum.tree.shift', [$version, $subject, $second]), ['direction' => 'up']);
        $this->assertSame(['Second', 'First'], CurriculumNode::whereNull('parent_id')->orderBy('sort')->pluck('title')->all());

        $this->delete(route('admin.curriculum.tree.destroy', [$version, $subject, $second]))->assertSessionHas('success');
        $this->assertSame(['First'], CurriculumNode::pluck('title')->all());
    }

    public function test_csv_import_dry_run_reports_problems_and_writes_nothing(): void
    {
        $this->actingAs(User::factory()->staff('Content Manager')->create());
        $version = $this->version();
        $subject = $this->subject();

        $csv = "code,parent_code,type,title,statement,class,official,source_ref\n"
            ."N1,,strand,Number,,SHS 1,yes,p. 4\n"
            ."N1.1,N9,sub_strand,Orphan,,,no,\n"     // unknown parent
            ."N1,,strand,Duplicate,,,no,\n"          // duplicate code
            ."X1,,chapter,Bad type,,,no,\n"          // invalid type
            ."X2,,topic,,,,no,\n"                    // missing title
            ."X3,,topic,Wrong class,,SHS 9,no,\n";   // unknown class

        $response = $this->post(route('admin.curriculum.tree.import.preview', [$version, $subject]), [
            'file' => UploadedFile::fake()->createWithContent('bad.csv', $csv),
        ])->assertOk();

        $errors = $response->viewData('preview')['errors'];
        $this->assertEqualsCanonicalizing([3, 4, 5, 6, 7], array_keys($errors));
        $this->assertStringContainsString('parent_code N9', $errors[3]);
        $response->assertDontSee('Import 6 item(s)');
        $this->assertSame(0, CurriculumNode::count());
    }

    public function test_csv_import_commits_a_valid_file_and_export_round_trips(): void
    {
        $this->actingAs(User::factory()->staff('Content Manager')->create());
        $version = $this->version();
        $subject = $this->subject();

        $csv = "\u{FEFF}Code,Parent Code,Type,Title,Statement,Class,Official,Source Ref\n"
            ."N1,,Strand,Number,,SHS 1,yes,p. 4\n"
            ."N1.1,N1,sub_strand,Real numbers,,SHS 1,yes,p. 5\n"
            ."N1.1.1,N1.1,indicator,Order real numbers,Learners order real numbers.,SHS 1,yes,p. 5\n"
            ."A1,,strand,Algebra,,,no,\n";

        $preview = $this->post(route('admin.curriculum.tree.import.preview', [$version, $subject]), [
            'file' => UploadedFile::fake()->createWithContent('good.csv', $csv),
        ])->assertOk();
        $this->assertSame([], $preview->viewData('preview')['errors']);

        $this->post(route('admin.curriculum.tree.import.commit', [$version, $subject]), ['token' => $preview->viewData('token')])
            ->assertRedirect(route('admin.curriculum.tree.show', [$version, $subject]))
            ->assertSessionHas('success', 'Imported 4 item(s).');

        $indicator = CurriculumNode::where('code', 'N1.1.1')->firstOrFail();
        $this->assertSame(2, $indicator->depth);
        $this->assertTrue($indicator->is_official);
        $this->assertSame('SHS 1', $indicator->classLevel->name);

        // The same token cannot be committed twice.
        $this->post(route('admin.curriculum.tree.import.commit', [$version, $subject]), ['token' => $preview->viewData('token')])
            ->assertSessionHas('error');
        $this->assertSame(4, CurriculumNode::count());

        $export = $this->get(route('admin.curriculum.tree.export', [$version, $subject]))->assertOk()->getContent();
        $this->assertStringContainsString('N1.1.1,N1.1,indicator,"Order real numbers","Learners order real numbers.","SHS 1",yes,"p. 5"', $export);
    }

    public function test_view_only_and_unrelated_staff_cannot_change_curriculum(): void
    {
        $version = $this->version();

        $finance = User::factory()->staff('Finance Officer')->create();
        $this->actingAs($finance)->get(route('admin.curriculum.subjects.index'))->assertForbidden();
        $this->post(route('admin.curriculum.subjects.store'), ['education_level_id' => $this->shs()->id, 'name' => 'X', 'category' => 'core'])->assertForbidden();

        // Exams Officer can view the curriculum but not edit it.
        $exams = User::factory()->staff('Exams Officer')->create();
        $this->actingAs($exams)->get(route('admin.curriculum.versions.show', $version))->assertOk();
        $this->get(route('admin.curriculum.tree.show', [$version, $this->subject()]))->assertOk()->assertDontSee('Add an item');
        $this->post(route('admin.curriculum.tree.store', [$version, $this->subject()]), ['node_type' => 'strand', 'title' => 'X'])->assertForbidden();
        $this->assertSame(0, CurriculumNode::count());
    }

    public function test_a_node_cannot_be_reached_through_another_subject_or_version(): void
    {
        $tree = app(CurriculumTree::class);
        $version = $this->version();
        $node = $tree->add($version, $this->subject(), null, ['node_type' => 'strand', 'title' => 'Number']);

        $this->actingAs(User::factory()->staff('Content Manager')->create());
        $this->get(route('admin.curriculum.tree.edit', [$version, $this->subject('Physics'), $node]))->assertNotFound();
        $this->delete(route('admin.curriculum.tree.destroy', [$version, $this->subject('Physics'), $node]))->assertNotFound();

        // A parent from another subject's tree is rejected.
        $this->post(route('admin.curriculum.tree.store', [$version, $this->subject('Physics')]), ['node_type' => 'sub_strand', 'title' => 'Sneaky', 'parent_id' => $node->id])
            ->assertSessionHasErrors('parent_id');
    }

    public function test_used_records_are_protected_from_deletion(): void
    {
        $tree = app(CurriculumTree::class);
        $version = $this->version();
        $tree->add($version, $this->subject(), null, ['node_type' => 'strand', 'title' => 'Number']);
        $student = User::factory()->student()->create();

        $this->actingAs(User::factory()->staff('Content Manager')->create());

        $this->delete(route('admin.curriculum.subjects.destroy', $this->subject()))->assertSessionHas('error');
        $this->delete(route('admin.curriculum.versions.destroy', $version))->assertSessionHas('error');
        $this->delete(route('admin.curriculum.classes.destroy', $student->studentProfile->class_level_id))->assertSessionHas('error');
        $this->delete(route('admin.curriculum.programmes.destroy', $student->studentProfile->programme_id))->assertSessionHas('error');

        $this->assertTrue(Subject::where('name', 'Core Mathematics')->exists());
    }

    public function test_programme_subjects_are_saved_with_core_or_elective_role(): void
    {
        $this->actingAs(User::factory()->staff('Content Manager')->create());
        $physics = $this->subject('Physics');
        $english = $this->subject('English Language');

        $this->post(route('admin.curriculum.programmes.store'), [
            'education_level_id' => $this->shs()->id, 'name' => 'Engineering STEM', 'is_active' => '1',
            'subjects' => [$english->id => 'core', $physics->id => 'elective', $this->subject('Biology')->id => ''],
        ])->assertRedirect(route('admin.curriculum.programmes.index'));

        $programme = Programme::where('name', 'Engineering STEM')->firstOrFail();
        $this->assertEqualsCanonicalizing(['English Language', 'Physics'], $programme->subjects->pluck('name')->all());
        $this->assertTrue((bool) $programme->subjects->firstWhere('name', 'English Language')->pivot->is_core);
    }

    public function test_inactive_classes_disappear_from_registration(): void
    {
        $shs3 = ClassLevel::where('name', 'SHS 3')->firstOrFail();
        $this->actingAs(User::factory()->staff('Content Manager')->create())
            ->put(route('admin.curriculum.classes.update', $shs3), ['name' => 'SHS 3', 'sort' => 3, 'is_active' => '0']);
        auth()->logout();
        $this->flushSession(); // drop the admin's "saved" flash message

        $this->get(route('register'))->assertOk()->assertSee('SHS 2')->assertDontSee('SHS 3');
        $this->post(route('register.store'), [
            'name' => 'Kofi', 'phone' => '0244000111', 'class_level_id' => $shs3->id,
            'password' => 'secret123', 'password_confirmation' => 'secret123', 'terms' => '1',
        ])->assertSessionHasErrors('class_level_id');
    }

    public function test_curriculum_admin_pages_load(): void
    {
        $tree = app(CurriculumTree::class);
        $version = $this->version('active');
        $node = $tree->add($version, $this->subject(), null, ['node_type' => 'strand', 'title' => 'Number']);

        $this->actingAs(User::factory()->staff()->create());
        foreach ([
            route('admin.curriculum.levels.index'),
            route('admin.curriculum.programmes.index'),
            route('admin.curriculum.programmes.create'),
            route('admin.curriculum.programmes.edit', Programme::first()),
            route('admin.curriculum.subjects.index'),
            route('admin.curriculum.subjects.create'),
            route('admin.curriculum.subjects.edit', $this->subject()),
            route('admin.curriculum.versions.index'),
            route('admin.curriculum.versions.create'),
            route('admin.curriculum.versions.show', $version),
            route('admin.curriculum.versions.edit', $version),
            route('admin.curriculum.tree.show', [$version, $this->subject()]),
            route('admin.curriculum.tree.edit', [$version, $this->subject(), $node]),
            route('admin.curriculum.tree.import', [$version, $this->subject()]),
            route('admin.curriculum.tree.template'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
