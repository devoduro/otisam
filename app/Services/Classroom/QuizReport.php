<?php

namespace App\Services\Classroom;

use App\Models\Classroom\Assignment;
use App\Models\Classroom\Submission;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * The teacher's assessment details for one quiz: a summary, one row per student (score, time taken,
 * times they left the page) and one row per question (how many got it right, the most common
 * wrong answer), so the teacher can see who needs help and which questions the class found hard.
 */
class QuizReport
{
    /**
     * @return array{
     *   summary: array{students: int, submitted: int, in_progress: int, not_started: int, average: ?int, highest: ?int, lowest: ?int, average_seconds: ?int, pass_mark: int, passed: int},
     *   students: list<array{name: string, username: ?string, status: string, score: ?float, max: ?float, percent: ?int, seconds: ?int, submitted_at: ?CarbonInterface, late: bool, tab_leaves: int}>,
     *   questions: list<array{number: int, stem: string, correct: ?string, correct_text: ?string, answered: int, right: int, percent: ?int, common_wrong: ?string, common_wrong_count: int}>
     * }
     */
    public function build(Assignment $quiz, int $passMark = 50): array
    {
        $quiz->loadMissing(['classroom.students', 'questions']);
        $submissions = Submission::where('assignment_id', $quiz->id)->get()->keyBy('student_id');
        $revisions = QuestionRevision::whereIn('id', $quiz->questions->pluck('pivot.question_revision_id'))->get()->keyBy('id');
        $keys = QuestionKey::whereIn('question_revision_id', $revisions->keys())->pluck('key', 'question_revision_id');

        $students = [];
        foreach ($quiz->classroom->students as $student) {
            $s = $submissions[$student->id] ?? null;
            $done = $s?->isSubmitted();
            $students[] = [
                'name' => $student->name,
                'username' => $student->username,
                'status' => $done ? 'Submitted' : ($s?->started_at ? 'Started, not submitted' : 'Not started'),
                'score' => $done ? (float) $s->score : null,
                'max' => $done ? (float) $s->max_score : null,
                'percent' => $done ? $s->percent() : null,
                'seconds' => $done && $s->started_at ? (int) $s->started_at->diffInSeconds($s->submitted_at) : null,
                'submitted_at' => $done ? $s->submitted_at : null,
                'late' => (bool) ($done && $s->is_late),
                'tab_leaves' => (int) ($s?->tab_leaves ?? 0),
            ];
        }
        // Best first; students who have not submitted go to the bottom.
        usort($students, fn ($a, $b) => [$b['percent'] ?? -1, $a['name']] <=> [$a['percent'] ?? -1, $b['name']]);

        $submitted = $submissions->filter->isSubmitted();
        $questions = [];
        foreach ($quiz->questions as $i => $question) {
            $revision = $revisions[$question->pivot->question_revision_id] ?? null;
            $correct = $keys[$question->pivot->question_revision_id]['correct'] ?? null;
            $given = $submitted->map(fn ($s) => $s->answers[$question->ulid] ?? null)->filter();
            $wrong = $given->reject(fn ($g) => $g === $correct)->countBy()->sortDesc();
            $options = collect($revision?->payload['options'] ?? [])->pluck('text', 'id');

            $questions[] = [
                'number' => $i + 1,
                'stem' => (string) $revision?->stem,
                'correct' => $correct,
                'correct_text' => $correct ? $options[$correct] ?? null : null,
                'answered' => $given->count(),
                'right' => $given->filter(fn ($g) => $g === $correct)->count(),
                'percent' => $submitted->count() ? (int) round($given->filter(fn ($g) => $g === $correct)->count() / $submitted->count() * 100) : null,
                'common_wrong' => $wrong->isNotEmpty() ? $wrong->keys()->first().'. '.($options[$wrong->keys()->first()] ?? '') : null,
                'common_wrong_count' => (int) ($wrong->first() ?? 0),
            ];
        }

        $percents = collect($students)->pluck('percent')->filter(fn ($p) => $p !== null);
        $times = collect($students)->pluck('seconds')->filter(fn ($t) => $t !== null);

        return [
            'summary' => [
                'students' => count($students),
                'submitted' => $submitted->count(),
                'in_progress' => collect($students)->where('status', 'Started, not submitted')->count(),
                'not_started' => collect($students)->where('status', 'Not started')->count(),
                'average' => $percents->isNotEmpty() ? (int) round($percents->avg()) : null,
                'highest' => $percents->max(),
                'lowest' => $percents->isNotEmpty() ? $percents->min() : null,
                'average_seconds' => $times->isNotEmpty() ? (int) round($times->avg()) : null,
                'pass_mark' => $passMark,
                'passed' => $percents->filter(fn ($p) => $p >= $passMark)->count(),
            ],
            'students' => $students,
            'questions' => $questions,
        ];
    }

    public static function duration(?int $seconds): string
    {
        return $seconds === null ? '—' : sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);
    }

    /** @param Collection<int, mixed>|array $rows */
    public static function questionsSortedByDifficulty(array $rows): array
    {
        usort($rows, fn ($a, $b) => ($a['percent'] ?? 101) <=> ($b['percent'] ?? 101));

        return $rows;
    }
}
