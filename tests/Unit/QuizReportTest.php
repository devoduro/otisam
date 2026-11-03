<?php

namespace Tests\Unit;

use App\Enums\TeacherVerificationStatus;
use App\Models\Classroom\Assignment;
use App\Models\Classroom\Classroom;
use App\Models\Classroom\Submission;
use App\Models\Curriculum\Subject;
use App\Models\User;
use App\Services\Classroom\AssignmentService;
use App\Services\Classroom\QuizReport;
use Database\Seeders\CurriculumSubjectsSeeder;
use Tests\Concerns\CreatesTopicQuestions;
use Tests\TestCase;

class QuizReportTest extends TestCase
{
    use CreatesTopicQuestions;

    private Assignment $quiz;

    /** @var array<string, User> */
    private array $students = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);

        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $classroom = new Classroom(['name' => 'JHS 1A']);
        $classroom->teacher_id = $teacher->id;
        $classroom->save();
        foreach (['Ama', 'Kofi', 'Esi', 'Yaw'] as $name) {
            $this->students[$name] = User::factory()->student()->create(['name' => $name]);
            $classroom->students()->attach($this->students[$name]->id, ['joined_at' => now()]);
        }

        $this->quiz = new Assignment(['type' => 'quiz', 'title' => 'Fractions', 'time_limit_minutes' => 10]);
        $this->quiz->forceFill(['classroom_id' => $classroom->id, 'teacher_id' => $teacher->id, 'status' => 'published'])->save();
        $subject = Subject::where('slug', 'jhs-mathematics')->firstOrFail();
        $this->topicWithQuestions($subject, 'Fractions', 4);
        app(AssignmentService::class)->addFromBank($this->quiz, $subject, 4);
        $this->quiz->refresh();
    }

    /** Ama gets every question right, Kofi gets the first one right, Esi only started, Yaw never opened it. */
    private function sit(string $name, array $answers, int $seconds, bool $submit = true, int $leaves = 0): void
    {
        $questions = $this->quiz->questions()->get();
        $started = now()->subMinutes(30);
        $submission = new Submission;
        $submission->forceFill([
            'assignment_id' => $this->quiz->id, 'student_id' => $this->students[$name]->id, 'started_at' => $started, 'tab_leaves' => $leaves,
        ] + ($submit ? [
            'submitted_at' => $started->copy()->addSeconds($seconds), 'graded_at' => now(),
            'answers' => $questions->pluck('ulid')->combine(array_pad($answers, $questions->count(), null))->all(),
            'score' => count(array_filter($answers, fn ($a) => $a === 'B')), 'max_score' => $questions->count(),
        ] : []))->save();
    }

    private function sitTheClass(): void
    {
        $this->sit('Ama', ['B', 'B', 'B', 'B'], 300);
        $this->sit('Kofi', ['B', 'C', 'C', null], 125, leaves: 3);
        $this->sit('Esi', [], 0, submit: false);
    }

    public function test_summary_counts_and_averages(): void
    {
        $this->sitTheClass();
        $summary = app(QuizReport::class)->build($this->quiz)['summary'];

        $this->assertSame(4, $summary['students']);
        $this->assertSame(2, $summary['submitted']);
        $this->assertSame(1, $summary['in_progress']);
        $this->assertSame(1, $summary['not_started']);
        $this->assertSame(63, $summary['average']); // (100 + 25) / 2, rounded
        $this->assertSame(100, $summary['highest']);
        $this->assertSame(25, $summary['lowest']);
        $this->assertSame(213, $summary['average_seconds']); // (300 + 125) / 2, rounded
        $this->assertSame(1, $summary['passed']);
    }

    public function test_pass_mark_changes_who_passed(): void
    {
        $this->sitTheClass();
        $this->assertSame(2, app(QuizReport::class)->build($this->quiz, 25)['summary']['passed']);
        $this->assertSame(1, app(QuizReport::class)->build($this->quiz, 26)['summary']['passed']);
    }

    public function test_student_rows_are_best_first_with_time_and_page_leaves(): void
    {
        $this->sitTheClass();
        $rows = app(QuizReport::class)->build($this->quiz)['students'];

        $this->assertSame(['Ama', 'Kofi', 'Esi', 'Yaw'], array_column($rows, 'name'));
        $this->assertSame([100, 25, null, null], array_column($rows, 'percent'));
        $this->assertSame([300, 125, null, null], array_column($rows, 'seconds'));
        $this->assertSame(['Submitted', 'Submitted', 'Started, not submitted', 'Not started'], array_column($rows, 'status'));
        $this->assertSame([0, 3, 0, 0], array_column($rows, 'tab_leaves'));
        $this->assertSame(1.0, $rows[1]['score']);
        $this->assertSame(4.0, $rows[1]['max']);
    }

    public function test_students_with_the_same_score_are_in_name_order(): void
    {
        $this->sit('Yaw', ['B', 'B', 'B', 'B'], 60);
        $this->sit('Ama', ['B', 'B', 'B', 'B'], 60);
        $rows = app(QuizReport::class)->build($this->quiz)['students'];

        $this->assertSame(['Ama', 'Yaw', 'Esi', 'Kofi'], array_column($rows, 'name'));
    }

    public function test_question_rows_show_how_many_got_each_right_and_the_common_wrong_answer(): void
    {
        $this->sitTheClass();
        $questions = app(QuizReport::class)->build($this->quiz)['questions'];

        $this->assertSame([1, 2, 3, 4], array_column($questions, 'number'));
        $this->assertSame([100, 50, 50, 50], array_column($questions, 'percent'));
        $this->assertSame([2, 2, 2, 1], array_column($questions, 'answered'));
        $this->assertSame([2, 1, 1, 1], array_column($questions, 'right'));
        $this->assertSame('B', $questions[0]['correct']);
        $this->assertSame('right one', $questions[0]['correct_text']);
        $this->assertNull($questions[0]['common_wrong']);
        $this->assertSame('C. wrong two', $questions[1]['common_wrong']);
        $this->assertSame(1, $questions[1]['common_wrong_count']);
        // A blank answer is not counted as a wrong answer.
        $this->assertNull($questions[3]['common_wrong']);
    }

    public function test_report_with_no_submissions_has_no_averages(): void
    {
        $report = app(QuizReport::class)->build($this->quiz);

        $this->assertSame(0, $report['summary']['submitted']);
        $this->assertNull($report['summary']['average']);
        $this->assertNull($report['summary']['highest']);
        $this->assertNull($report['summary']['lowest']);
        $this->assertNull($report['summary']['average_seconds']);
        $this->assertSame([null, null, null, null], array_column($report['questions'], 'percent'));
        $this->assertSame(['Ama', 'Esi', 'Kofi', 'Yaw'], array_column($report['students'], 'name'));
    }

    public function test_duration_is_minutes_and_seconds(): void
    {
        $this->assertSame('0:00', QuizReport::duration(0));
        $this->assertSame('0:09', QuizReport::duration(9));
        $this->assertSame('2:05', QuizReport::duration(125));
        $this->assertSame('75:00', QuizReport::duration(4500));
        $this->assertSame('—', QuizReport::duration(null));
    }

    public function test_questions_are_sorted_hardest_first_with_unanswered_last(): void
    {
        $sorted = QuizReport::questionsSortedByDifficulty([
            ['number' => 1, 'percent' => 80], ['number' => 2, 'percent' => null], ['number' => 3, 'percent' => 10], ['number' => 4, 'percent' => 50],
        ]);

        $this->assertSame([3, 4, 1, 2], array_column($sorted, 'number'));
    }
}
