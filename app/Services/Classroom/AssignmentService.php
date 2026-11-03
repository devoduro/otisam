<?php

namespace App\Services\Classroom;

use App\Models\Classroom\Assignment;
use App\Models\Classroom\Submission;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use App\Models\User;
use Database\Seeders\DemoQuestionSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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

    /**
     * Bank questions a quiz can use: published practice multiple-choice questions of a subject,
     * optionally narrowed to a class year, a topic (with its subtopics), a difficulty range and
     * words in the question. A question tagged to no year counts for every year of its level.
     */
    public function bankQuestions(Subject $subject, ?array $difficulty = null, ?CurriculumNode $topic = null, ?string $search = null, ?ClassLevel $year = null): Builder
    {
        return Question::where('status', 'published')
            ->when($year, fn ($q) => $q->where(fn ($w) => $w->whereNull('class_level_id')->orWhere('class_level_id', $year->id)))
            ->where('confidentiality', 'practice')
            ->where('type', 'mcq')
            ->where('subject_id', $subject->id)
            ->whereNotNull('current_revision_id')
            ->when($difficulty, fn ($q) => $q->whereBetween('difficulty', $difficulty))
            ->when($topic, fn ($q) => $q->whereHas('curriculumNodes', fn ($n) => $n->where('curriculum_nodes.id', $topic->id)->orWhere('curriculum_nodes.path', 'like', $topic->childPath().'%')))
            ->when($search !== null && trim($search) !== '', fn ($q) => $q->whereHas('currentRevision', fn ($r) => $r->where('stem', 'like', '%'.addcslashes(trim($search), '%_\\').'%')));
    }

    /** Adds up to $count random published practice questions from the bank. Returns how many were added. */
    public function addFromBank(Assignment $quiz, Subject $subject, int $count, ?array $difficulty = null, ?CurriculumNode $topic = null): int
    {
        $this->assertEditableQuiz($quiz);
        $room = self::MAX_QUIZ_QUESTIONS - $quiz->questions()->count();

        $questions = $this->bankQuestions($subject, $difficulty, $topic)
            ->whereNotIn('id', $quiz->questions()->pluck('questions.id'))
            ->inRandomOrder()->limit(max(0, min($count, $room)))->get();

        $this->attach($quiz, $questions);

        return $questions->count();
    }

    /**
     * Adds the bank questions the teacher ticked, in the order given. Questions already in the quiz
     * or not usable in a quiz are skipped. Returns how many were added.
     *
     * @param  list<int>  $questionIds
     */
    public function addSelected(Assignment $quiz, array $questionIds): int
    {
        $this->assertEditableQuiz($quiz);
        $existing = $quiz->questions()->pluck('questions.id')->all();
        $wanted = array_values(array_diff(array_unique(array_map('intval', $questionIds)), $existing));
        if (count($existing) + count($wanted) > self::MAX_QUIZ_QUESTIONS) {
            throw ValidationException::withMessages(['question_ids' => 'A quiz can have at most '.self::MAX_QUIZ_QUESTIONS.' questions. It has '.count($existing).' now, so you can add '.max(0, self::MAX_QUIZ_QUESTIONS - count($existing)).' more.']);
        }

        $usable = Question::whereIn('id', $wanted)->where('status', 'published')->where('confidentiality', 'practice')
            ->where('type', 'mcq')->whereNotNull('current_revision_id')->get()->keyBy('id');
        $questions = collect($wanted)->map(fn ($id) => $usable[$id] ?? null)->filter()->values();

        $this->attach($quiz, $questions);

        return $questions->count();
    }

    /** @param  Collection<int, Question>  $questions */
    private function attach(Assignment $quiz, Collection $questions): void
    {
        $sort = (int) $quiz->questions()->max('sort');
        foreach ($questions as $question) {
            $quiz->questions()->attach($question->id, ['question_revision_id' => $question->current_revision_id, 'sort' => ++$sort, 'marks' => 1]);
        }
    }

    /**
     * Topics of a subject that have multiple-choice questions a quiz can use, with how many.
     *
     * @return Collection<int, array{id: int, title: string, year: ?string, count: int}>
     */
    public function bankTopics(Subject $subject, ?ClassLevel $year = null)
    {
        $counts = DB::table('curriculum_node_question')
            ->join('questions', 'questions.id', '=', 'curriculum_node_question.question_id')
            ->when($year, fn ($q) => $q->where(fn ($w) => $w->whereNull('questions.class_level_id')->orWhere('questions.class_level_id', $year->id)))
            ->where('questions.subject_id', $subject->id)->where('questions.status', 'published')
            ->where('questions.confidentiality', 'practice')->where('questions.type', 'mcq')
            ->whereNotNull('questions.current_revision_id')->whereNull('questions.deleted_at')
            ->groupBy('curriculum_node_question.curriculum_node_id')
            ->selectRaw('curriculum_node_question.curriculum_node_id as id, count(*) as total')
            ->pluck('total', 'id');

        return CurriculumNode::where('subject_id', $subject->id)->whereIn('id', $counts->keys())
            ->when($year, fn ($q) => $q->where(fn ($w) => $w->whereNull('class_level_id')->orWhere('class_level_id', $year->id)))
            ->with('classLevel:id,name')->orderBy('path')->orderBy('sort')->orderBy('title')->get(['id', 'title', 'class_level_id'])
            ->map(fn ($n) => ['id' => $n->id, 'title' => $n->title, 'year' => $n->classLevel?->name, 'count' => (int) $counts[$n->id]]);
    }

    /** A four-option question the teacher writes for their own quiz. */
    public function addOwnQuestion(Assignment $quiz, User $teacher, Subject $subject, string $stem, array $options, string $correct, ?string $explanation): Question
    {
        $this->assertEditableQuiz($quiz);
        if ($quiz->questions()->count() >= self::MAX_QUIZ_QUESTIONS) {
            throw ValidationException::withMessages(['stem' => 'A quiz can have at most '.self::MAX_QUIZ_QUESTIONS.' questions.']);
        }

        return DB::transaction(function () use ($quiz, $teacher, $subject, $stem, $options, $correct, $explanation) {
            $question = $this->newTeacherQuestion($teacher, $subject, $quiz->classroom->class_level_id, 3, $stem, $options, $correct, $explanation, 'Written by '.$teacher->name.' for a class quiz');
            $quiz->questions()->attach($question->id, ['question_revision_id' => $question->current_revision_id, 'sort' => (int) $quiz->questions()->max('sort') + 1, 'marks' => 1]);

            return $question;
        });
    }

    /**
     * Changes a question in this quiz: wording, options, correct answer, explanation and marks.
     *
     * The question bank is shared, so editing a bank question (or another teacher's) puts the
     * teacher's own copy into the quiz in the same place; the bank keeps the original. A teacher's
     * own question gets a new revision, which only this quiz switches to: other quizzes that use it
     * keep the version they were built with. Changing only the marks changes nothing else.
     *
     * @param  list<string>  $options
     */
    public function editQuestion(Assignment $quiz, Question $question, User $teacher, string $stem, array $options, string $correct, ?string $explanation, float $marks): Question
    {
        $this->assertEditableQuiz($quiz);
        $current = $quiz->questions()->whereKey($question->id)->first();
        if (! $current) {
            throw ValidationException::withMessages(['stem' => 'That question is not in this quiz.']);
        }
        $pivot = $current->pivot;
        $revision = QuestionRevision::findOrFail($pivot->question_revision_id);
        $key = QuestionKey::where('question_revision_id', $revision->id)->value('key');

        $choices = self::choices($options);
        $explanation = $explanation !== null && trim($explanation) !== '' ? trim($explanation) : null;
        $unchanged = trim($stem) === $revision->stem && $choices === ($revision->payload['options'] ?? null)
            && $correct === ($key['correct'] ?? null) && $explanation === $revision->explanation;

        return DB::transaction(function () use ($quiz, $question, $teacher, $stem, $options, $correct, $explanation, $marks, $pivot, $choices, $unchanged) {
            if ($unchanged) {
                $quiz->questions()->updateExistingPivot($question->id, ['marks' => $marks]);

                return $question;
            }

            if ($question->owner_scope === 'teacher' && $question->author_id === $teacher->id) {
                $revision = new QuestionRevision;
                $revision->forceFill([
                    'question_id' => $question->id,
                    'revision_no' => (int) $question->revisions()->max('revision_no') + 1,
                    'stem' => trim($stem),
                    'payload' => ['options' => $choices],
                    'explanation' => $explanation,
                    'created_by' => $teacher->id,
                ])->save();
                (new QuestionKey)->forceFill(['question_revision_id' => $revision->id, 'key' => ['correct' => $correct]])->save();
                $question->forceFill(['current_revision_id' => $revision->id, 'stem_hash' => DemoQuestionSeeder::stemHash($stem)])->save();
                $quiz->questions()->updateExistingPivot($question->id, ['question_revision_id' => $revision->id, 'marks' => $marks]);

                return $question;
            }

            $copy = $this->newTeacherQuestion($teacher, Subject::findOrFail($question->subject_id), $question->class_level_id ?? $quiz->classroom->class_level_id, (int) $question->difficulty, $stem, $options, $correct, $explanation, 'Edited by '.$teacher->name.' from a question bank question');
            $quiz->questions()->detach($question->id);
            $quiz->questions()->attach($copy->id, ['question_revision_id' => $copy->current_revision_id, 'sort' => $pivot->sort, 'marks' => $marks]);

            return $copy;
        });
    }

    /** @param  list<string>  $options */
    private function newTeacherQuestion(User $teacher, Subject $subject, ?int $classLevelId, int $difficulty, string $stem, array $options, string $correct, ?string $explanation, string $source): Question
    {
        $question = new Question;
        $question->forceFill([
            'type' => 'mcq',
            'education_level_id' => $subject->education_level_id,
            'subject_id' => $subject->id,
            'class_level_id' => $classLevelId,
            'difficulty' => max(1, min(5, $difficulty)),
            'default_marks' => 1,
            'status' => 'published',
            'access_level' => 'free',
            'confidentiality' => 'exam',
            'owner_scope' => 'teacher',
            'author_id' => $teacher->id,
            'source' => $source,
            'stem_hash' => DemoQuestionSeeder::stemHash($stem),
        ])->save();

        $revision = new QuestionRevision;
        $revision->forceFill([
            'question_id' => $question->id,
            'revision_no' => 1,
            'stem' => trim($stem),
            'payload' => ['options' => self::choices($options)],
            'explanation' => $explanation !== null && trim($explanation) !== '' ? trim($explanation) : null,
            'created_by' => $teacher->id,
        ])->save();

        (new QuestionKey)->forceFill(['question_revision_id' => $revision->id, 'key' => ['correct' => $correct]])->save();
        $question->forceFill(['current_revision_id' => $revision->id])->save();

        return $question;
    }

    /**
     * @param  list<string>  $options
     * @return list<array{id: string, text: string}>
     */
    private static function choices(array $options): array
    {
        $choices = [];
        foreach (array_values($options) as $i => $text) {
            $choices[] = ['id' => ['A', 'B', 'C', 'D', 'E'][$i], 'text' => trim($text)];
        }

        return $choices;
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
