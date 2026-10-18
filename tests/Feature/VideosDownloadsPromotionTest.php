<?php

namespace Tests\Feature;

use App\Models\Commerce\Subscription;
use App\Models\Curriculum\Subject;
use App\Models\Library\ResourceCategory;
use App\Models\User;
use App\Models\Videos\VideoLesson;
use App\Services\Library\InspectedUpload;
use App\Services\Library\ResourceService;
use FPDF;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** The Video lessons, Downloads and Promotion menus. */
class VideosDownloadsPromotionTest extends TestCase
{
    public function test_youtube_links_in_common_forms_are_understood(): void
    {
        foreach ([
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://youtu.be/dQw4w9WgXcQ?si=abc',
            'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'https://www.youtube.com/shorts/dQw4w9WgXcQ',
            'https://m.youtube.com/watch?v=dQw4w9WgXcQ&t=30',
            'dQw4w9WgXcQ',
        ] as $link) {
            $this->assertSame('dQw4w9WgXcQ', VideoLesson::parseYoutubeId($link), $link);
        }

        $this->assertNull(VideoLesson::parseYoutubeId('https://evil.example/watch?v=dQw4w9WgXcQ'));
        $this->assertNull(VideoLesson::parseYoutubeId('https://vimeo.com/123'));
        $this->assertNull(VideoLesson::parseYoutubeId('<script>'));
    }

    public function test_staff_add_publish_and_students_watch_video_lessons(): void
    {
        $editor = User::factory()->staff('Content Manager')->create();
        $subject = Subject::where('name', 'Integrated Science')->firstOrFail();

        $this->actingAs($editor)->post(route('admin.videos.store'), [
            'title' => 'Photosynthesis explained', 'youtube' => 'https://youtu.be/dQw4w9WgXcQ',
            'subject_id' => $subject->id, 'access_level' => 'free', 'duration_minutes' => 12,
        ])->assertRedirect();
        $video = VideoLesson::firstOrFail();
        $this->assertSame('dQw4w9WgXcQ', $video->youtube_id);
        $this->assertSame('draft', $video->status);

        $this->post(route('admin.videos.store'), ['title' => 'Bad', 'youtube' => 'https://example.com/x', 'access_level' => 'free'])
            ->assertSessionHasErrors('youtube');

        auth()->logout();
        $this->get(route('videos.show', $video))->assertNotFound();
        $this->get(route('videos.index'))->assertOk()->assertDontSee('Photosynthesis explained');

        $this->actingAs($editor)->post(route('admin.videos.status', $video), ['status' => 'published'])->assertSessionHas('success');
        auth()->logout();

        $this->get(route('videos.index'))->assertOk()->assertSee('Photosynthesis explained');
        $this->get(route('videos.index', ['subject' => $subject->slug]))->assertSee('Photosynthesis explained');
        $this->get(route('videos.show', $video))->assertOk()
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->assertDontSee('Premium video lesson');
        $this->assertSame(1, $video->fresh()->view_count);
    }

    public function test_premium_videos_are_locked_without_a_subscription(): void
    {
        $video = new VideoLesson(['title' => 'Premium lesson', 'youtube_id' => 'dQw4w9WgXcQ', 'access_level' => 'premium']);
        $video->status = 'published';
        $video->published_at = now();
        $video->save();

        $this->get(route('videos.show', $video))->assertOk()->assertSee('Premium video lesson')->assertDontSee('youtube-nocookie.com/embed', false);

        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('videos.show', $video))->assertDontSee('youtube-nocookie.com/embed', false);

        (new Subscription)->forceFill(['user_id' => $student->id, 'source' => 'admin_grant', 'starts_at' => now()->subDay(), 'ends_at' => now()->addMonth(), 'status' => 'active'])->save();
        $this->get(route('videos.show', $video))->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
    }

    public function test_video_management_needs_permission(): void
    {
        $this->actingAs(User::factory()->staff('Finance Officer')->create())->get(route('admin.videos.index'))->assertForbidden();
        $this->post(route('admin.videos.store'), ['title' => 'x', 'youtube' => 'dQw4w9WgXcQ', 'access_level' => 'free'])->assertForbidden();
    }

    public function test_downloads_page_shows_access_state_and_my_downloads(): void
    {
        Storage::fake('library');
        $pdf = new FPDF;
        $pdf->AddPage();
        $pdf->AddPage();
        $tmp = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tmp, $pdf->Output('S'));

        $service = app(ResourceService::class);
        $make = function (string $title, string $access, bool $publish = true) use ($service, $tmp) {
            $resource = $service->create([
                'title' => $title, 'category_id' => ResourceCategory::where('name', 'Revision notes')->value('id'), 'audience' => 'student',
                'subject_id' => Subject::where('name', 'Core Mathematics')->value('id'), 'access_level' => $access, 'copyright_holder' => 'Otisam',
            ], new InspectedUpload($tmp, 'n.pdf', 'pdf', 'application/pdf', filesize($tmp), hash_file('sha256', $tmp)), User::factory()->staff()->create());
            if ($publish) {
                $service->setStatus($resource, 'published');
            }

            return $resource;
        };
        $free = $make('Free algebra notes', 'free');
        $make('Premium geometry solutions', 'premium');
        $make('Unpublished draft notes', 'free', false);

        $this->get(route('downloads.index'))->assertOk()
            ->assertSee('Free algebra notes')->assertSee('Premium geometry solutions')->assertDontSee('Unpublished draft notes')
            ->assertSee(route('login'));

        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('library.download', $free))->assertOk();
        $this->get(route('downloads.index'))->assertOk()->assertSee('My downloads')->assertSee(route('library.download', $free), false);
    }

    public function test_menus_and_pages_are_reachable(): void
    {
        $this->get(route('home'))->assertOk()
            ->assertSee(route('videos.index'))->assertSee(route('downloads.index'))->assertSee(route('promotion'));
        $this->get(route('promotion'))->assertOk()->assertSee('Teacher promotion exams')->assertSee('not official GES promotion examinations');

        $teacher = User::factory()->teacher()->create();
        $this->actingAs($teacher)->get(route('teach.dashboard'))->assertOk()
            ->assertSee(route('videos.index'))->assertSee(route('downloads.index'))->assertSee(route('promotion'));
        $this->get(route('promotion'))->assertSee('Your teacher account');

        $this->actingAs(User::factory()->student()->create())->get(route('learn.dashboard'))->assertOk()
            ->assertSee(route('videos.index'))->assertSee(route('downloads.index'));

        $this->actingAs(User::factory()->staff()->create())->get(route('admin.dashboard'))->assertSee(route('admin.videos.index'));
        $this->get(route('admin.videos.create'))->assertOk();
    }
}
