<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\Subject;
use App\Models\LessonPlans\LessonPlan;
use App\Models\NotificationMessage;
use App\Models\User;
use Database\Seeders\CurriculumSubjectsSeeder;
use Database\Seeders\LessonPlanSeeder;
use Tests\TestCase;

class LessonPlanTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
    }

    private function teacher(): User
    {
        return User::factory()->teacher(TeacherVerificationStatus::Approved)->create(['name' => 'Kofi Mensah']);
    }

    private function planData(array $overrides = []): array
    {
        $maths = Subject::where('slug', 'core-mathematics')->firstOrFail();

        return $overrides + [
            'title' => 'Solving linear equations with a balance',
            'subject_id' => $maths->id,
            'class_level_id' => ClassLevel::where('name', 'SHS 1')->value('id'),
            'term' => 1,
            'week' => 7,
            'strand' => 'Algebra',
            'sub_strand' => 'Linear equations',
            'indicators' => "Solve equations of the form ax + b = c\nCheck a solution by substituting",
            'starter' => 'A see-saw puzzle on the board.',
            'main_activities' => 'Model equations with bottle tops on a drawn balance, then remove the same amount from both sides.',
            'reflection' => 'Learners explain the balance idea to a partner.',
            'assessment' => 'Five equations to solve.',
            'share' => '1',
        ];
    }

    public function test_seeded_sample_plans_are_public_and_can_be_filtered(): void
    {
        $this->seed(LessonPlanSeeder::class);
        $this->assertSame(6, LessonPlan::visible()->count());
        // Running it again adds nothing.
        $this->seed(LessonPlanSeeder::class);
        $this->assertSame(6, LessonPlan::count());

        $this->get(route('lesson-plans.index'))->assertOk()
            ->assertSee('Adding and subtracting fractions with unlike denominators')
            ->assertSee('Roles and responsibilities in the family');

        $this->get(route('lesson-plans.index', ['level' => 'jhs']))->assertOk()
            ->assertSee('Roles and responsibilities in the family')
            ->assertDontSee('Writing a formal letter of complaint');

        $this->get(route('lesson-plans.index', ['subject' => 'core-mathematics', 'week' => 4]))->assertOk()
            ->assertSee('Adding and subtracting fractions')->assertDontSee('Investigating photosynthesis');
        $this->get(route('lesson-plans.index', ['subject' => 'core-mathematics', 'week' => 9]))->assertOk()
            ->assertDontSee('Adding and subtracting fractions');

        $plan = LessonPlan::where('title', 'like', 'Adding and subtracting%')->firstOrFail();
        $this->get(route('lesson-plans.show', $plan))->assertOk()
            ->assertSee('Lesson flow')->assertSee('Starter')->assertSee('Find the lowest common multiple');
        $this->assertSame(1, $plan->fresh()->view_count);
    }

    public function test_print_and_word_download(): void
    {
        $this->seed(LessonPlanSeeder::class);
        $plan = LessonPlan::firstOrFail();

        $this->get(route('lesson-plans.print', $plan))->assertOk()->assertSee('Print or save as PDF')->assertSee($plan->title);

        $response = $this->get(route('lesson-plans.download', $plan))->assertOk();
        $this->assertStringContainsString('application/msword', $response->headers->get('Content-Type'));
        $this->assertStringContainsString($plan->slug.'.doc', $response->headers->get('Content-Disposition'));
        $this->assertStringNotContainsString('Print or save as PDF', $response->getContent());
    }

    public function test_guests_and_students_cannot_author_and_pending_teachers_are_sent_back(): void
    {
        $this->get(route('teach.lesson-plans.create'))->assertRedirect(route('login'));

        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('teach.lesson-plans.index'))->assertRedirect(route('learn.dashboard'));

        $pending = User::factory()->teacher()->create();
        $this->actingAs($pending)->get(route('teach.lesson-plans.index'))->assertRedirect(route('teach.dashboard'));
    }

    public function test_private_draft_is_only_visible_to_its_author(): void
    {
        $teacher = $this->teacher();
        $this->actingAs($teacher)->get(route('teach.lesson-plans.create'))->assertOk()->assertSee('Main activities');
        $this->actingAs($teacher)->post(route('teach.lesson-plans.store'), $this->planData(['share' => '0']))
            ->assertRedirect(route('teach.lesson-plans.index'));

        $plan = LessonPlan::firstOrFail();
        $this->assertSame('draft', $plan->status);
        $this->assertSame($teacher->id, $plan->created_by);

        $this->actingAs($teacher)->get(route('lesson-plans.show', $plan))->assertOk()->assertSee('Only you can see this plan');
        $this->actingAs($teacher)->get(route('lesson-plans.print', $plan))->assertOk();

        $this->actingAs($this->teacher())->get(route('lesson-plans.show', $plan))->assertNotFound();
        $this->actingAs($this->teacher())->get(route('teach.lesson-plans.edit', $plan))->assertNotFound();
        auth()->logout();
        $this->get(route('lesson-plans.show', $plan))->assertNotFound();
        $this->get(route('lesson-plans.download', $plan))->assertNotFound();
        $this->get(route('lesson-plans.index'))->assertDontSee($plan->title);
    }

    public function test_shared_plan_waits_for_review_then_admin_approves_or_sends_back(): void
    {
        $teacher = $this->teacher();
        $this->actingAs($teacher)->post(route('teach.lesson-plans.store'), $this->planData())->assertSessionHasNoErrors();
        $plan = LessonPlan::firstOrFail();
        $this->assertSame('pending', $plan->status);
        auth()->logout();
        $this->get(route('lesson-plans.show', $plan))->assertNotFound();

        // Staff without tutorials.manage cannot review.
        $finance = User::factory()->staff('Finance Officer')->create();
        $this->actingAs($finance)->get(route('admin.lesson-plans.index'))->assertForbidden();
        $this->post(route('admin.lesson-plans.approve', $plan))->assertForbidden();

        $admin = User::factory()->staff('Content Manager')->create();
        $this->actingAs($admin)->get(route('admin.lesson-plans.index'))->assertOk()->assertSee($plan->title);
        $this->actingAs($admin)->get(route('lesson-plans.show', $plan))->assertOk()->assertSee('Staff view');
        $this->actingAs($admin)->post(route('admin.lesson-plans.approve', $plan))->assertRedirect();
        $this->assertSame('published', $plan->fresh()->status);
        $this->assertNotNull($plan->fresh()->published_at);
        $this->assertTrue(NotificationMessage::where('user_id', $teacher->id)->where('event_key', 'lesson-plan.approved')->exists());

        auth()->logout();
        $this->get(route('lesson-plans.index'))->assertSee($plan->title);
        $this->get(route('lesson-plans.show', $plan))->assertOk()->assertSee('Kofi Mensah');

        // Sent back: private again, with the reason shown to the teacher.
        $this->actingAs($admin)->post(route('admin.lesson-plans.reject', $plan), ['reason' => 'Please add an assessment task.'])->assertRedirect();
        $this->assertSame('draft', $plan->fresh()->status);
        $this->actingAs($teacher)->get(route('teach.lesson-plans.index'))->assertSee('Please add an assessment task.');
    }

    public function test_trusted_teacher_publishes_directly_and_editing_resubmits_for_others(): void
    {
        $trusted = $this->teacher();
        $trusted->givePermissionTo('teach.publish-tutorials');
        $this->actingAs($trusted)->post(route('teach.lesson-plans.store'), $this->planData())->assertSessionHasNoErrors();
        $this->assertSame('published', LessonPlan::firstOrFail()->status);

        $teacher = $this->teacher();
        $plan = new LessonPlan(collect($this->planData(['title' => 'Already live']))->except('share')->all());
        $plan->forceFill(['created_by' => $teacher->id, 'status' => 'published', 'published_at' => now()])->save();

        // Saving without changes keeps it live; changing it sends it for review again.
        $this->actingAs($teacher)->put(route('teach.lesson-plans.update', $plan), $this->planData(['title' => 'Already live']))->assertSessionHasNoErrors();
        $this->assertSame('published', $plan->fresh()->status);
        $this->actingAs($teacher)->put(route('teach.lesson-plans.update', $plan), $this->planData(['title' => 'Already live', 'week' => 8]))->assertSessionHasNoErrors();
        $this->assertSame('pending', $plan->fresh()->status);

        $this->actingAs($teacher)->delete(route('teach.lesson-plans.destroy', $plan))->assertRedirect();
        $this->assertSoftDeleted($plan);
    }

    public function test_validation_keeps_year_and_topic_consistent_with_the_subject(): void
    {
        $teacher = $this->teacher();
        $this->actingAs($teacher)->post(route('teach.lesson-plans.store'), $this->planData(['main_activities' => '']))
            ->assertSessionHasErrors('main_activities');

        $this->actingAs($teacher)->post(route('teach.lesson-plans.store'), $this->planData(['class_level_id' => ClassLevel::where('name', 'JHS 1')->value('id')]))
            ->assertSessionHasErrors('class_level_id');

        $biology = Subject::where('slug', 'biology')->firstOrFail();
        $version = CurriculumVersion::create(['education_level_id' => $biology->education_level_id, 'name' => 'Test topics', 'status' => 'active']);
        $node = new CurriculumNode(['node_type' => 'topic', 'title' => 'Cells']);
        $node->forceFill(['curriculum_version_id' => $version->id, 'subject_id' => $biology->id, 'path' => '/'])->save();

        $this->actingAs($teacher)->post(route('teach.lesson-plans.store'), $this->planData(['curriculum_node_id' => $node->id]))
            ->assertSessionHasErrors('curriculum_node_id');
        $this->actingAs($teacher)->post(route('teach.lesson-plans.store'), $this->planData(['subject_id' => $biology->id, 'curriculum_node_id' => $node->id]))
            ->assertSessionHasNoErrors();
        $this->assertSame($node->id, LessonPlan::firstOrFail()->curriculum_node_id);
    }

    public function test_teacher_can_start_from_a_shared_plan(): void
    {
        $this->seed(LessonPlanSeeder::class);
        $source = LessonPlan::where('title', 'like', 'Writing a formal letter%')->firstOrFail();
        $teacher = $this->teacher();

        $this->actingAs($teacher)->get(route('lesson-plans.show', $source))->assertSee('Use as my starting point');
        $this->actingAs($teacher)->get(route('teach.lesson-plans.create', ['from' => $source->slug]))->assertOk()
            ->assertSee('Writing a formal letter of complaint (my copy)')
            ->assertSee('Read both teacher-written letters');
    }
}
