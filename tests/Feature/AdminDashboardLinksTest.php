<?php

namespace Tests\Feature;

use App\Models\NotificationMessage;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuizAttempt;
use App\Models\User;
use Database\Seeders\CurriculumSubjectsSeeder;
use Database\Seeders\DemoQuestionSeeder;
use Tests\TestCase;

class AdminDashboardLinksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
        DemoQuestionSeeder::$only = ['jhs-computing'];
        $this->seed(DemoQuestionSeeder::class);
        DemoQuestionSeeder::$only = null;
    }

    public function test_dashboard_cards_link_to_the_lists_the_role_can_open(): void
    {
        $manager = User::factory()->staff('Content Manager')->create();

        $page = $this->actingAs($manager)->get(route('admin.dashboard'))->assertOk();
        $page->assertSee(route('admin.questions.index', ['use' => 'practice']), false)
            ->assertSee(route('admin.videos.index'), false)
            ->assertSee(route('admin.attempts.index', ['days' => 7]), false)
            ->assertDontSee('data-bs-toggle="modal"', false)
            // No students list for this role, so that card has no link.
            ->assertDontSee(route('admin.students.index'), false);
    }

    public function test_question_bank_list_and_detail(): void
    {
        $manager = User::factory()->staff('Content Manager')->create();
        $question = Question::where('type', 'theory')->latest('id')->firstOrFail();

        $this->actingAs($manager)->get(route('admin.questions.index', ['type' => 'theory']))->assertOk()
            ->assertSee(route('admin.questions.show', $question), false);
        $this->get(route('admin.questions.show', $question))->assertOk()->assertSee('Model answer');

        $finance = User::factory()->staff('Finance Officer')->create();
        $this->actingAs($finance)->get(route('admin.questions.index'))->assertForbidden();
    }

    public function test_message_log_and_attempts_need_their_permissions(): void
    {
        $message = NotificationMessage::create(['event_key' => 'otp.login', 'category' => 'security', 'channel' => 'sms', 'recipient' => '0244000000', 'body' => 'Your code is 123456', 'status' => 'failed']);
        $student = User::factory()->create();
        $attempt = QuizAttempt::recordFor($student, ['subject_id' => Question::value('subject_id'), 'kind' => 'practice', 'score' => 3, 'total' => 5, 'seconds' => 90, 'late' => false]);

        $manager = User::factory()->staff('Content Manager')->create();
        $this->actingAs($manager)->get(route('admin.messages.index'))->assertForbidden();
        $this->get(route('admin.attempts.index'))->assertOk()->assertSee('Learner #'.$student->id);
        $this->get(route('admin.attempts.show', $attempt))->assertOk()->assertSee('3/5');

        $admin = User::factory()->staff('Administrator')->create();
        $this->actingAs($admin)->get(route('admin.messages.index', ['channel' => 'sms']))->assertOk()->assertSee('0244000000');
        $this->get(route('admin.messages.show', $message))->assertOk()->assertSee('Your code is 123456');
        $this->get(route('admin.attempts.index'))->assertOk()->assertSee($student->name);
    }

    public function test_overview_pages_open_for_any_staff_with_shortened_names(): void
    {
        $student = User::factory()->student()->create(['name' => 'Kwame Mensah Boateng']);
        $manager = User::factory()->staff('Content Manager')->create();

        $dash = $this->actingAs($manager)->get(route('admin.dashboard'))->assertOk();
        foreach (['students', 'teachers', 'staff', 'sms'] as $page) {
            $dash->assertSee(route('admin.overview.'.$page), false);
            $this->get(route('admin.overview.'.$page))->assertOk();
        }
        $this->get(route('admin.overview.students'))->assertSee('Kwame B.')->assertDontSee('Kwame Mensah Boateng');

        $admin = User::factory()->staff('Administrator')->create();
        $this->actingAs($admin)->get(route('admin.overview.students'))->assertSee('Kwame Mensah Boateng')->assertSee(route('admin.students.index'), false);
    }
}
