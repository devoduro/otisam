<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\Classroom\TeacherMaterial;
use App\Models\Curriculum\Subject;
use App\Models\User;
use App\Models\Videos\VideoLesson;
use Database\Seeders\CurriculumSubjectsSeeder;
use Database\Seeders\DemoQuestionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicTeachersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('library');
        $this->seed([CurriculumSubjectsSeeder::class, DemoQuestionSeeder::class]);
    }

    private function publicTeacher(array $profile = []): User
    {
        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create(['name' => 'Ama Owusu']);
        $this->actingAs($teacher)->put(route('teach.profile.update'), array_merge([
            'name' => 'Ama Owusu', 'school_name' => 'Wesley Girls\' High School', 'region' => 'Central', 'district' => 'Cape Coast',
            'subjects_taught' => 'Biology, Integrated Science', 'staff_number' => 'GES-998877',
            'is_public' => '1', 'headline' => 'Biology teacher', 'bio' => 'Ten years teaching SHS Biology.', 'years_teaching' => 10,
        ], $profile))->assertSessionHasNoErrors();
        auth()->logout();

        return $teacher->fresh('teacherProfile');
    }

    public function test_library_year_level_and_class_filters(): void
    {
        $page = $this->get(route('library.index'))->assertOk();
        $page->assertSee('<option value="'.now()->year.'"', false)->assertSee('<option value="1990"', false);
        $page->assertSee('<optgroup label="Junior High School">', false)->assertDontSee('· SHS');

        $this->get(route('library.index', ['level' => 'jhs']))->assertOk()
            ->assertSee('<optgroup label="Junior High School">', false)
            ->assertDontSee('<optgroup label="Senior High School">', false);
        $this->get(route('library.index', ['year' => 2019, 'level' => 'shs']))->assertOk();
    }

    public function test_subjects_page_filters(): void
    {
        $this->get(route('subjects.index'))->assertOk()->assertSee('Career Technology')->assertSee('Robotics');
        $this->get(route('subjects.index', ['level' => 'jhs']))->assertSee('Career Technology')->assertDontSee('Robotics');
        $this->get(route('subjects.index', ['category' => 'elective', 'level' => 'shs']))->assertSee('Robotics')->assertDontSee('Integrated Science');
        $this->get(route('subjects.index', ['has' => 'practice']))->assertSee('Core Mathematics')->assertDontSee('Spanish');
        $this->get(route('subjects.index', ['q' => 'engineer']))->assertSee('Manufacturing Engineering')->assertDontSee('Biology');
    }

    public function test_teachers_opt_in_to_a_public_profile(): void
    {
        $teacher = $this->publicTeacher();
        $profile = $teacher->teacherProfile;
        $this->assertTrue($profile->isListed());

        $this->get(route('teachers.index'))->assertOk()->assertSee('Ama Owusu')->assertSee('Wesley Girls');
        $this->get(route('teachers.index', ['region' => 'Volta']))->assertDontSee('Ama Owusu');
        $this->get(route('teachers.index', ['subject' => 'Biology']))->assertSee('Ama Owusu');

        $this->get(route('teachers.show', $profile->slug))->assertOk()
            ->assertSee('Biology teacher')->assertSee('Ten years teaching SHS Biology.')
            ->assertDontSee('GES-998877')->assertDontSee($teacher->phone);

        // Pending teachers and teachers who opt out are not listed.
        $pending = User::factory()->teacher()->create(['name' => 'Kofi Pending']);
        $pending->teacherProfile->forceFill(['is_public' => true, 'slug' => 'kofi-pending-abcd'])->save();
        $this->get(route('teachers.index'))->assertDontSee('Kofi Pending');
        $this->get(route('teachers.show', 'kofi-pending-abcd'))->assertNotFound();

        $this->actingAs($teacher)->put(route('teach.profile.update'), [
            'name' => 'Ama Owusu', 'school_name' => 'Wesley Girls\' High School', 'region' => 'Central', 'subjects_taught' => 'Biology', 'is_public' => '0',
        ]);
        auth()->logout();
        $this->get(route('teachers.show', $profile->slug))->assertNotFound();
    }

    public function test_changing_public_details_does_not_resubmit_for_review(): void
    {
        $teacher = User::factory()->teacher(TeacherVerificationStatus::MoreInfo)->create(['name' => 'Yaw Mensah']);
        $teacher->teacherProfile->update(['school_name' => 'Achimota School', 'region' => 'Greater Accra', 'subjects_taught' => 'Physics']);

        $this->actingAs($teacher)->put(route('teach.profile.update'), [
            'name' => 'Yaw Mensah', 'school_name' => 'Achimota School', 'region' => 'Greater Accra', 'subjects_taught' => 'Physics',
            'headline' => 'Physics teacher', 'photo' => UploadedFile::fake()->image('me.jpg', 300, 300),
        ])->assertSessionHasNoErrors();

        $profile = $teacher->fresh()->teacherProfile;
        $this->assertSame(TeacherVerificationStatus::MoreInfo, $profile->verification_status);
        $this->assertStringStartsWith('uploads/teachers/', $profile->photo_path);
        @unlink(public_path($profile->photo_path));
    }

    public function test_teacher_videos_are_reviewed_unless_the_teacher_is_trusted(): void
    {
        $teacher = $this->publicTeacher();
        $biology = Subject::where('slug', 'biology')->value('id');

        $this->actingAs($teacher)->post(route('teach.videos.store'), ['youtube' => 'https://youtu.be/AAAAAAAAAA1', 'title' => 'Cell structure', 'subject_id' => $biology])
            ->assertRedirect(route('teach.videos.index'));
        $video = VideoLesson::where('youtube_id', 'AAAAAAAAAA1')->firstOrFail();
        $this->assertSame('pending', $video->status);
        $this->assertSame('Ama Owusu', $video->instructor);

        // The same YouTube video cannot be added twice.
        $this->post(route('teach.videos.store'), ['youtube' => 'AAAAAAAAAA1', 'title' => 'Again', 'subject_id' => $biology])->assertSessionHasErrors('youtube');

        auth()->logout();
        $this->get(route('videos.index'))->assertDontSee('Cell structure');

        $admin = User::factory()->staff('Administrator')->create();
        $this->actingAs($admin)->post(route('admin.videos.status', $video), ['status' => 'published']);
        auth()->logout();
        $this->get(route('teachers.show', $teacher->teacherProfile->slug))->assertSee('Cell structure');

        // Trusted teachers publish directly.
        $teacher->givePermissionTo('teach.publish-tutorials');
        $this->actingAs($teacher->fresh())->post(route('teach.videos.store'), ['youtube' => 'BBBBBBBBBB2', 'title' => 'Osmosis', 'subject_id' => $biology]);
        $this->assertSame('published', VideoLesson::where('youtube_id', 'BBBBBBBBBB2')->value('status'));

        // Teachers can only edit their own videos.
        $other = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($other)->get(route('teach.videos.edit', $video))->assertNotFound();
    }

    public function test_public_materials_appear_on_downloads(): void
    {
        $teacher = $this->publicTeacher();
        $this->actingAs($teacher)->post(route('teach.materials.store'), [
            'title' => 'Photosynthesis lesson plan', 'kind' => 'lesson_plan', 'visibility' => 'public',
            'subject_id' => Subject::where('slug', 'biology')->value('id'), 'file' => UploadedFile::fake()->image('plan.png', 600, 800),
        ]);
        $this->post(route('teach.materials.store'), [
            'title' => 'Staff-room notes', 'kind' => 'other', 'visibility' => 'teachers', 'file' => UploadedFile::fake()->image('n.png', 600, 800),
        ]);
        auth()->logout();

        $material = TeacherMaterial::where('visibility', 'public')->firstOrFail();
        $this->get(route('downloads.index', ['source' => 'teachers']))->assertOk()
            ->assertSee('Photosynthesis lesson plan')->assertDontSee('Staff-room notes')->assertSee('Sign in to download');
        $this->get(route('downloads.index', ['source' => 'teachers', 'level' => 'jhs']))->assertDontSee('Photosynthesis lesson plan');
        $this->get(route('teachers.show', $teacher->teacherProfile->slug))->assertSee('Photosynthesis lesson plan');

        $this->get(route('downloads.material', $material))->assertRedirect(route('login'));
        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('downloads.material', $material))->assertOk();
        $this->assertSame(1, $material->fresh()->download_count);

        $teachersOnly = TeacherMaterial::where('visibility', 'teachers')->firstOrFail();
        $this->get(route('downloads.material', $teachersOnly))->assertNotFound();
    }

    public function test_promotion_page_shows_samples_and_asks_guests_to_sign_up(): void
    {
        $page = $this->get(route('promotion'))->assertOk();
        $page->assertSee('General paper: all ranks')->assertSee('Assistant Director I to Deputy Director')
            ->assertSee('Sample questions')->assertSee('Show answer')->assertSee('Sign up to start')
            ->assertSeeText('more questions and timed papers');
        $this->assertSame(5, substr_count($page->getContent(), 'data-sample'));

        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($teacher)->get(route('promotion'))->assertSee('Continue practising')->assertDontSee('Sign up to start');
    }
}
