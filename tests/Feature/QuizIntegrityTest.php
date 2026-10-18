<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\Classroom\Assignment;
use App\Models\Classroom\Classroom;
use App\Models\Classroom\Submission;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\User;
use Database\Seeders\CurriculumSubjectsSeeder;
use Tests\TestCase;

class QuizIntegrityTest extends TestCase
{
    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
        $this->teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
    }

    /** A published one-question quiz ("What is 7 × 8?", correct option B) with the given settings. */
    private function quiz(array $settings = []): Assignment
    {
        $maths = Subject::where('slug', 'core-mathematics')->value('id');
        $this->actingAs($this->teacher)->post(route('teach.classes.store'), ['name' => 'JHS 2 A', 'subject_id' => $maths, 'joining_open' => '1']);
        $this->post(route('teach.assignments.store', Classroom::firstOrFail()), ['type' => 'quiz', 'title' => 'Quiz 1'] + $settings)->assertSessionHasNoErrors();
        $quiz = Assignment::firstOrFail();
        $this->post(route('teach.assignments.questions.store', $quiz), [
            'subject_id' => $maths, 'stem' => 'What is 7 × 8?', 'options' => ['54', '56', '58', '64'], 'correct' => 'B',
        ])->assertSessionHas('success');
        $this->post(route('teach.assignments.publish', $quiz))->assertSessionHas('success');
        auth()->logout();

        return $quiz->fresh();
    }

    private function student(Assignment $quiz): User
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student)->post(route('learn.classes.join'), ['code' => $quiz->classroom->join_code]);

        return $student;
    }

    /** The option ids in the order they appear on the student's paper. */
    private function optionOrder(string $html): string
    {
        preg_match_all('/name="answers\[[^\]]+\]" value="([A-E])"/', $html, $m);

        return implode('', $m[1]);
    }

    public function test_teacher_turns_on_shuffled_options_and_copy_blocking(): void
    {
        $quiz = $this->quiz(['shuffle_options' => '1', 'block_copy' => '1']);
        $this->assertTrue($quiz->shuffle_options);
        $this->assertTrue($quiz->block_copy);

        $orders = [];
        for ($i = 0; $i < 4; $i++) {
            $this->student($quiz);
            $paper = $this->get(route('learn.assignments.take', $quiz))->assertOk()
                ->assertSee('data-block-copy style=', false)->assertSee('Copying the questions is turned off')
                // Letters on screen always run A, B, C, D whatever the order underneath.
                ->assertSeeInOrder(['<strong class="mr-1">A.</strong>', '<strong class="mr-1">B.</strong>', '<strong class="mr-1">C.</strong>', '<strong class="mr-1">D.</strong>'], false);
            $order = $this->optionOrder($paper->getContent());
            $this->assertEqualsCanonicalizing(['A', 'B', 'C', 'D'], str_split($order));

            // Reloading keeps the same order for this student.
            $this->assertSame($order, $this->optionOrder($this->get(route('learn.assignments.take', $quiz))->getContent()));
            $orders[] = $order;
        }
        $this->assertNotSame(['ABCD', 'ABCD', 'ABCD', 'ABCD'], $orders);

        // Marking still uses the real option: "56" is B whatever letter the student saw.
        $question = Question::firstOrFail();
        $this->post(route('learn.assignments.submit-quiz', $quiz), ['answers' => [$question->ulid => 'B']]);
        $this->assertSame(1.0, (float) Submission::latest('id')->first()->score);

        $this->actingAs($this->teacher)->get(route('teach.assignments.show', $quiz))->assertSee('Shuffled')->assertSee('Copying off');
    }

    public function test_quiz_options_stay_in_order_by_default_and_students_are_warned_about_leaving(): void
    {
        $quiz = $this->quiz();
        $this->assertFalse($quiz->shuffle_options);

        $student = $this->student($quiz);
        $paper = $this->get(route('learn.assignments.take', $quiz))->assertOk()
            ->assertSee('Stay on this page until you submit.')->assertSee('your teacher can see it')
            ->assertDontSee('data-block-copy style=', false);
        $this->assertSame('ABCD', $this->optionOrder($paper->getContent()));

        // Each switch away from the page is counted.
        $this->post(route('learn.assignments.left', $quiz))->assertNoContent();
        $this->post(route('learn.assignments.left', $quiz))->assertNoContent();
        $this->assertSame(2, Submission::firstOrFail()->tab_leaves);
        $this->get(route('learn.assignments.take', $quiz))->assertSee('(so far: 2)');

        $this->post(route('learn.assignments.submit-quiz', $quiz), ['answers' => []]);
        $this->post(route('learn.assignments.left', $quiz))->assertNoContent();
        $this->assertSame(2, Submission::firstOrFail()->tab_leaves);

        // Students outside the class cannot report anything.
        $outsider = User::factory()->student()->create();
        $this->actingAs($outsider)->post(route('learn.assignments.left', $quiz))->assertNotFound();

        $this->actingAs($this->teacher)->get(route('teach.assignments.show', $quiz))->assertSee('Left page 2&times;', false)->assertSee('Fixed order');
        $this->assertSame($student->id, Submission::firstOrFail()->student_id);
    }
}
