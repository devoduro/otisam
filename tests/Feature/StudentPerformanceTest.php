<?php

namespace Tests\Feature;

use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuizAttempt;
use App\Models\User;
use Database\Seeders\CurriculumSubjectsSeeder;
use Database\Seeders\DemoQuestionSeeder;
use Tests\TestCase;

class StudentPerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
        DemoQuestionSeeder::$only = ['jhs-computing'];
        $this->seed(DemoQuestionSeeder::class);
        DemoQuestionSeeder::$only = null;
    }

    public function test_answers_are_saved_and_shown_on_the_student_and_attempt_pages(): void
    {
        $student = User::factory()->student()->create(['name' => 'Efua Asante']);

        $this->actingAs($student)->get(route('practice.start', ['subject' => 'jhs-computing', 'count' => 5, 'types' => ['mcq']]))->assertOk();
        $ids = session('practice.set')['ids'];
        $answers = Question::whereIn('id', $ids)->get()->mapWithKeys(fn ($q) => [$q->ulid => 'A'])->all();
        $this->post(route('practice.mark'), ['answers' => $answers])->assertOk();

        $attempt = QuizAttempt::where('user_id', $student->id)->firstOrFail();
        $this->assertSame(5, $attempt->answers()->count());
        $this->assertSame($attempt->score, $attempt->answers()->where('is_correct', true)->count());

        $admin = User::factory()->staff('Administrator')->create();
        $this->actingAs($admin)->get(route('admin.students.show', $student))->assertOk()
            ->assertSee('Practice and exam performance')->assertSee('All attempts')->assertSee('Question trials')
            ->assertSee(route('admin.attempts.show', $attempt), false);
        $this->get(route('admin.attempts.show', $attempt))->assertOk()->assertSee('Question by question');
    }

    public function test_an_attempt_without_saved_answers_still_opens(): void
    {
        $student = User::factory()->student()->create();
        $attempt = QuizAttempt::recordFor($student, ['subject_id' => Question::value('subject_id'), 'kind' => 'practice', 'score' => 2, 'total' => 5, 'seconds' => 60, 'late' => false]);

        $admin = User::factory()->staff('Administrator')->create();
        $this->actingAs($admin)->get(route('admin.students.show', $student))->assertOk()->assertSee('Question-level results are saved for attempts made from now on');
        $this->get(route('admin.attempts.show', $attempt))->assertOk()->assertSee('only its score is available');

        $empty = User::factory()->student()->create();
        $this->get(route('admin.students.show', $empty))->assertOk()->assertSee('has not saved any practice sets');
    }
}
