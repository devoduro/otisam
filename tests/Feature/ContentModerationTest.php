<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\AuditLog;
use App\Models\Classroom\TeacherMaterial;
use App\Models\Curriculum\Subject;
use App\Models\NotificationMessage;
use App\Models\User;
use App\Models\Videos\VideoLesson;
use Database\Seeders\CurriculumSubjectsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentModerationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('library');
        $this->seed(CurriculumSubjectsSeeder::class);
    }

    private function teacher(): User
    {
        return User::factory()->teacher(TeacherVerificationStatus::Approved)->create(['name' => 'Ama Owusu']);
    }

    private function pendingVideo(User $teacher, string $youtubeId = 'AAAAAAAAAA1', string $title = 'Cell structure'): VideoLesson
    {
        $this->actingAs($teacher)->post(route('teach.videos.store'), [
            'youtube' => $youtubeId, 'title' => $title, 'subject_id' => Subject::where('slug', 'biology')->value('id'),
        ])->assertSessionHasNoErrors();
        auth()->logout();

        return VideoLesson::where('youtube_id', $youtubeId)->firstOrFail();
    }

    private function publicMaterial(User $teacher): TeacherMaterial
    {
        $this->actingAs($teacher)->post(route('teach.materials.store'), [
            'title' => 'Photosynthesis lesson plan', 'kind' => 'lesson_plan', 'visibility' => 'public',
            'file' => UploadedFile::fake()->image('plan.png', 600, 800),
        ])->assertSessionHasNoErrors();
        auth()->logout();

        return TeacherMaterial::where('visibility', 'public')->firstOrFail();
    }

    public function test_only_staff_with_tutorials_manage_can_moderate(): void
    {
        $teacher = $this->teacher();
        $video = $this->pendingVideo($teacher);
        $material = $this->publicMaterial($teacher);

        $finance = User::factory()->staff('Finance Officer')->create();
        $this->actingAs($finance)->get(route('admin.moderation.index'))->assertForbidden();
        $this->post(route('admin.moderation.videos.approve', $video))->assertForbidden();
        $this->post(route('admin.moderation.videos.reject', $video), ['reason' => 'No'])->assertForbidden();
        $this->get(route('admin.moderation.materials.download', $material))->assertForbidden();
        $this->post(route('admin.moderation.materials.hide', $material), ['reason' => 'No'])->assertForbidden();
        $this->get(route('admin.dashboard'))->assertOk()->assertDontSee('Teacher content');

        // Non-staff accounts are sent back to their own area.
        $this->actingAs($teacher)->get(route('admin.moderation.index'))->assertRedirect();
        $this->post(route('admin.moderation.videos.approve', $video))->assertRedirect();
        $this->assertSame('pending', $video->fresh()->status);
        $this->assertSame('public', $material->fresh()->visibility);

        $manager = User::factory()->staff('Content Manager')->create();
        $this->actingAs($manager)->get(route('admin.moderation.index'))->assertOk()
            ->assertSee('Cell structure')->assertSee('Photosynthesis lesson plan')->assertSee('Ama Owusu')
            ->assertSee('title="Teacher videos waiting for review">1</span>', false);
    }

    public function test_approving_a_teacher_video_publishes_it_audits_and_notifies(): void
    {
        $teacher = $this->teacher();
        $video = $this->pendingVideo($teacher);
        $staffVideo = VideoLesson::forceCreate(['title' => 'Staff draft', 'youtube_id' => 'CCCCCCCCCC3', 'access_level' => 'free', 'status' => 'pending', 'created_by' => User::factory()->staff()->create()->id]);

        $admin = User::factory()->staff('Administrator')->create();
        $this->actingAs($admin)->get(route('admin.moderation.index'))->assertSee('Cell structure')->assertDontSee('Staff draft');

        $this->post(route('admin.moderation.videos.approve', $video))->assertRedirect()->assertSessionHas('success');
        $video->refresh();
        $this->assertSame('published', $video->status);
        $this->assertNotNull($video->published_at);
        $this->assertTrue(AuditLog::where('action', 'video.approved')->where('auditable_id', $video->id)->where('user_id', $admin->id)->exists());
        $this->assertTrue(NotificationMessage::where('user_id', $teacher->id)->where('event_key', 'video.approved')->exists());

        // Already decided, and staff videos are not in this queue.
        $this->post(route('admin.moderation.videos.approve', $video))->assertNotFound();
        $this->post(route('admin.moderation.videos.approve', $staffVideo))->assertNotFound();

        auth()->logout();
        $this->get(route('videos.index'))->assertSee('Cell structure');
    }

    public function test_rejecting_a_teacher_video_needs_a_reason_which_the_teacher_receives(): void
    {
        $teacher = $this->teacher();
        $video = $this->pendingVideo($teacher);
        $admin = User::factory()->staff('Administrator')->create();

        $this->actingAs($admin)->post(route('admin.moderation.videos.reject', $video), ['reason' => ''])->assertSessionHasErrors('reason');
        $this->assertSame('pending', $video->fresh()->status);

        $this->post(route('admin.moderation.videos.reject', $video), ['reason' => 'The audio is too quiet.'])->assertSessionHas('success');
        $this->assertSame('draft', $video->fresh()->status);

        $log = AuditLog::where('action', 'video.rejected')->where('auditable_id', $video->id)->firstOrFail();
        $this->assertSame('The audio is too quiet.', $log->new_values['reason']);
        $message = NotificationMessage::where('user_id', $teacher->id)->where('event_key', 'video.rejected')->firstOrFail();
        $this->assertStringContainsString('The audio is too quiet.', $message->body);
        $this->get(route('admin.moderation.index'))->assertDontSee(route('admin.moderation.videos.reject', $video));

        // Editing the rejected video sends it for review again.
        $this->actingAs($teacher)->put(route('teach.videos.update', $video), [
            'youtube' => 'AAAAAAAAAA1', 'title' => 'Cell structure (louder)', 'subject_id' => $video->subject_id,
        ])->assertSessionHasNoErrors();
        $this->assertSame('pending', $video->fresh()->status);
    }

    public function test_hiding_a_public_material_removes_it_from_downloads(): void
    {
        $teacher = $this->teacher();
        $material = $this->publicMaterial($teacher);
        $admin = User::factory()->staff('Administrator')->create();

        // Reviewing the file does not count as a download.
        $this->actingAs($admin)->get(route('admin.moderation.materials.download', $material))->assertOk();
        $this->assertSame(0, $material->fresh()->download_count);

        $this->post(route('admin.moderation.materials.hide', $material), [])->assertSessionHasErrors('reason');
        $this->post(route('admin.moderation.materials.hide', $material), ['reason' => 'Copyrighted textbook pages.'])->assertSessionHas('success');
        $this->assertSame('teachers', $material->fresh()->visibility);

        $log = AuditLog::where('action', 'material.hidden')->where('auditable_id', $material->id)->firstOrFail();
        $this->assertSame(['visibility' => 'public'], $log->old_values);
        $message = NotificationMessage::where('user_id', $teacher->id)->where('event_key', 'material.hidden')->firstOrFail();
        $this->assertStringContainsString('Copyrighted textbook pages.', $message->body);

        $this->post(route('admin.moderation.materials.hide', $material), ['reason' => 'Again'])->assertSessionHas('error');
        $this->assertSame(1, AuditLog::where('action', 'material.hidden')->count());

        auth()->logout();
        $this->get(route('downloads.index', ['source' => 'teachers']))->assertDontSee('Photosynthesis lesson plan');
        $this->get(route('downloads.material', $material))->assertNotFound();
    }
}
