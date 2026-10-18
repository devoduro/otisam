<?php

namespace App\Services\Classroom;

use App\Models\Classroom\Assignment;
use App\Models\Classroom\Submission;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use App\Models\User;
use Database\Seeders\DemoQuestionSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * Builds quizzes, takes students' work in and marks it.
 *
 * Quiz questions are pinned to the revision they were added with. Teacher-written questions are
 * stored in the bank as owner_scope "teacher" and confidentiality "exam", so they never appear in
 * public practice.
 */
class AssignmentService
{
    public const MAX_QUIZ_QUESTIONS = 50;

    /** Adds up to $count random published practice questions from the bank. Returns how many were added. */
    public function addFromBank(Assignment $quiz, Subject $subject, int $count, ?array $difficulty = null): int
    {
        $this->assertEditableQuiz($quiz);
        $room = self::MAX_QUIZ_QUESTIONS - $quiz->questions()->count();
        $existing = $quiz->questions()->pluck('questions.id');

        $questions = Question::where('status', 'published')
            ->where('confidentiality', 'practice')
            ->where('type', 'mcq')
            ->where('subject_id', $subject->id)
            ->whereNotNull('current_revision_id')
            ->whereNotIn('id', $existing)
            ->when($difficulty, fn ($q) => $q->whereBetween('difficulty', $difficulty))
            ->inRandomOrder()->limit(max(0, min($count, $room)))->get();

        $sort = (int) $quiz->questions()->max('sort');
        foreach ($questions as $question) {
            $quiz->questions()->attach($question->id, ['question_revision_id' => $question->current_revision_id, 'sort' => ++$sort, 'marks' => 1]);
        }

        return $questions->count();
    }

    /** A four-option question the teacher writes for their own quiz. */
    public function addOwnQuestion(Assignment $quiz, User $teacher, Subject $subject, string $stem, array $options, string $correct, ?string $explanation): Question
    {
        $this->assertEditableQuiz($quiz);
        if ($quiz->questions()->count() >= self::MAX_QUIZ_QUESTIONS) {
            throw ValidationException::withMessages(['stem' => 'A quiz can have at most '.self::MAX_QUIZ_QUESTIONS.' questions.']);
        }

        $letters = ['A', 'B', 'C', 'D'];
        $choices = [];
        foreach (array_values($options) as $i => $text) {
            $choices[] = ['id' => $letters[$i], 'text' => trim($text)];
        }

        return DB::transaction(function () use ($quiz, $teacher, $subject, $stem, $choices, $correct, $explanation) {
            $question = new Question;
            $question->forceFill([
                'type' => 'mcq',
                'education_level_id' => $subject->education_level_id,
                'subject_id' => $subject->id,
                'class_level_id' => $quiz->classroom->class_level_id,
                'difficulty' => 3,
                'default_marks' => 1,
                'status' => 'published',
                'access_level' => 'free',
                'confidentiality' => 'exam',
                'owner_scope' => 'teacher',
                'author_id' => $teacher->id,
                'source' => 'Written by '.$teacher->name.' for a class quiz',
                'stem_hash' => DemoQuestionSeeder::stemHash($stem),
            ])->save();

            $revision = new QuestionRevision;
            $revision->forceFill([
                'question_id' => $question->id,
                'revision_no' => 1,
                'stem' => trim($stem),
                'payload' => ['options' => $choices],
                'explanation' => $explanation ? trim($explanation) : null,
                'created_by' => $teacher->id,
            ])->save();

            (new QuestionKey)->forceFill(['question_revision_id' => $revision->id, 'key' => ['correct' => $correct]])->save();
            $question->forceFill(['current_revision_id' => $revision->id])->save();

            $quiz->questions()->attach($question->id, ['question_revision_id' => $revision->id, 'sort' => (int) $quiz->questions()->max('sort') + 1, 'marks' => 1]);

            return $question;
        });
    }

    public function removeQuestion(Assignment $quiz, Question $question): void
    {
        $this->assertEditableQuiz($quiz);
        $quiz->questions()->detach($question->id);
    }

    public function publish(Assignment $assignment): void
    {
        if ($assignment->isQuiz() && $assignment->questions()->count() === 0) {
            throw new RuntimeException('Add at least one question before publishing the quiz.');
        }
        $assignment->forceFill(['status' => 'published', 'published_at' => $assignment->published_at ?? now()])->save();
    }

    /** The student's submission, created when they first open a quiz (the timer starts then). */
    public function startQuiz(Assignment $quiz, User $student): Submission
    {
        $submission = $this->submissionFor($quiz, $student);
        if (! $submission->exists) {
            $submission->forceFill(['started_at' => now()])->save();
        }

        return $submission;
    }

    /** Seconds left on a timed quiz, or null when it has no time limit. */
    public function secondsLeft(Assignment $quiz, Submission $submission): ?int
    {
        if (! $quiz->time_limit_minutes || ! $submission->started_at) {
            return null;
        }

        return max(0, $quiz->time_limit_minutes * 60 - (int) $submission->started_at->diffInSeconds(now()));
    }

    /** @param array<string, string|null> $answers question ulid => option id */
    public function submitQuiz(Assignment $quiz, Submission $submission, array $answers): Submission
    {
        if ($submission->isSubmitted()) {
            return $submission;
        }

        $questions = $quiz->questions()->get();
        $keys = QuestionKey::whereIn('question_revision_id', $questions->pluck('pivot.question_revision_id'))->pluck('key', 'question_revision_id');

        $score = 0.0;
        $kept = [];
        foreach ($questions as $question) {
            $given = $answers[$question->ulid] ?? null;
            $given = is_string($given) && preg_match('/^[A-E]$/', $given) ? $given : null;
            $kept[$question->ulid] = $given;
            if ($given !== null && $given === ($keys[$question->pivot->question_revision_id]['correct'] ?? null)) {
                $score += (float) $question->pivot->marks;
            }
        }

        // A short grace period covers the auto-submit round trip.
        $timeUp = $quiz->time_limit_minutes && $submission->started_at && $submission->started_at->diffInSeconds(now()) > $quiz->time_limit_minutes * 60 + 60;

        $submission->forceFill([
            'answers' => $kept,
            'submitted_at' => now(),
            'is_late' => $quiz->isOverdue() || $timeUp,
            'score' => $score,
            'max_score' => (float) $questions->sum('pivot.marks'),
            'graded_at' => now(),
        ])->save();

        return $submission;
    }

    /** @param array{body?: ?string, file?: ?array} $work */
    public function submitWork(Assignment $assignment, User $student, array $work): Submission
    {
        $submission = $this->submissionFor($assignment, $student);
        if ($submission->isGraded()) {
            throw ValidationException::withMessages(['body' => 'Your work has already been marked, so it can no longer be changed.']);
        }

        $submission->forceFill(array_merge(
            ['body' => $work['body'] ?? $submission->body, 'submitted_at' => now(), 'is_late' => $assignment->isOverdue(), 'max_score' => $assignment->max_marks],
            $work['file'] ?? [],
        ))->save();

        return $submission;
    }

    public function grade(Assignment $assignment, Submission $submission, ?float $score, ?string $feedback): void
    {
        if ($score !== null && $assignment->max_marks !== null && $score > (float) $assignment->max_marks) {
            throw ValidationException::withMessages(['score' => 'The score cannot be more than '.(float) $assignment->max_marks.'.']);
        }

        $submission->forceFill([
            'score' => $assignment->isQuiz() ? $submission->score : $score,
            'feedback' => $feedback,
            'graded_at' => now(),
        ])->save();
    }

    /** The student's existing submission, or a new unsaved one. */
    private function submissionFor(Assignment $assignment, User $student): Submission
    {
        return Submission::where('assignment_id', $assignment->id)->where('student_id', $student->id)->first()
            ?? (new Submission)->forceFill(['assignment_id' => $assignment->id, 'student_id' => $student->id]);
    }

    private function assertEditableQuiz(Assignment $quiz): void
    {
        if (! $quiz->isQuiz()) {
            throw new RuntimeException('Only quizzes have questions.');
        }
        if ($quiz->submissions()->whereNotNull('submitted_at')->exists()) {
            throw ValidationException::withMessages(['stem' => 'Students have already taken this quiz, so its questions can no longer change.']);
        }
    }
}
