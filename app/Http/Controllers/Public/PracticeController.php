<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuizAttempt;
use App\Services\Access\Entitlements;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Self-test from the question bank. A practice set is picked from the filters, kept in the
 * session and marked on the server: answer keys are read only when marking and never reach the
 * page before submission. Saved attempts and mock exams come with P6.
 */
class PracticeController extends Controller
{
    public const COUNTS = [5, 10, 20, 40];

    public const DIFFICULTIES = ['easy' => [1, 2], 'medium' => [3, 3], 'hard' => [4, 5]];

    /** Question types a learner can mix into a set, in the order they are offered. */
    public const TYPE_LABELS = ['mcq' => 'Multiple choice', 'true_false' => 'True or false', 'fill_in' => 'Fill in the blank', 'theory' => 'Theory'];

    /** How many recently answered questions per subject are held back so the next set is fresh. */
    public const SEEN_LIMIT = 600;

    /** Minutes allowed per question in timed mode (WASSCE objective papers allow roughly a minute each). */
    public const MINUTES_PER_QUESTION = 1;

    public function index(Request $request, Entitlements $entitlements): View
    {
        $filters = $this->filters($request);
        [$levels, $level, $subject, $year, $topic] = $this->resolve($filters);
        $premium = $entitlements->hasPremium($request->user());

        $counts = $this->bank($premium)->selectRaw('subject_id, count(*) as total')->groupBy('subject_id')->pluck('total', 'subject_id');
        $subjects = Subject::active()->whereIn('id', $counts->keys())
            ->whereHas('educationLevel', fn ($q) => $q->where('is_active', true))
            ->when($level, fn ($q) => $q->where('education_level_id', $level->id))
            ->orderBy('sort')->orderBy('name')->get()->groupBy('education_level_id');

        $topics = $subject
            ? CurriculumNode::where('subject_id', $subject->id)
                ->whereHas('version', fn ($v) => $v->published())
                ->whereIn('id', fn ($q) => $q->select('curriculum_node_id')->from('curriculum_node_question'))
                // With a year chosen, offer that year's topics and the ones for every year.
                ->when($year, fn ($q) => $q->where(fn ($w) => $w->whereNull('class_level_id')->orWhere('class_level_id', $year->id)))
                ->with('classLevel:id,name')
                ->orderBy('path')->orderBy('sort')->get(['id', 'title', 'code', 'path', 'class_level_id'])
            : collect();

        // Per-type counts for the other filters, so the page can total the ticked types itself.
        $typeCounts = $subject
            ? $this->filtered($premium, $subject, ['types' => Question::PRACTICE_TYPES] + $filters, $year, $topic)
                ->selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type')
            : collect();
        $types = self::selectedTypes($filters);
        $available = $subject ? (int) $typeCounts->only($types)->sum() : null;

        return view('public.practice.index', [
            'filters' => $filters,
            'levels' => $levels,
            'level' => $level,
            'subject' => $subject,
            'subjects' => $subjects,
            'topics' => $topics,
            'counts' => $counts,
            'available' => $available,
            'typeCounts' => $typeCounts,
            'types' => $types,
            'recent' => collect($request->session()->get('practice.recent', [])),
        ]);
    }

    /** Old links (/practice/{subject}) start a default set for that subject. */
    public function show(Subject $subject): RedirectResponse
    {
        return redirect()->route('practice.start', ['subject' => $subject->slug]);
    }

    public function start(Request $request, Entitlements $entitlements): View|RedirectResponse
    {
        $filters = $this->filters($request);
        [, , $subject, $year, $topic] = $this->resolve($filters);
        if (! $subject) {
            return redirect()->route('practice.index', $filters)->with('error', 'Choose a subject to practise.');
        }
        // Promotion practice lives under a hidden level and is only for approved teachers (and staff).
        $promotion = $subject->isProfessional();
        if ($promotion) {
            $user = $request->user();
            if (! $user) {
                return redirect()->guest(route('teachers.register'))->with('status', 'Create a free teacher account to take promotion practice papers.');
            }
            if (! ($user->isApprovedTeacher() || $user->isStaff())) {
                return redirect()->route('promotion')->with('error', 'Promotion practice is for approved teacher accounts.');
            }
            abort_unless($subject->is_active, 404);
        } else {
            abort_unless($subject->is_active && $subject->educationLevel?->is_active, 404);
        }

        $count = (int) ($filters['count'] ?? 10);
        $ids = $this->pick($request, $subject, $count,
            $this->filtered($promotion || $entitlements->hasPremium($request->user()), $subject, $filters, $year, $topic, $promotion ? 'promotion' : 'practice')->pluck('id')->all());
        // Objective questions first, theory at the end, each in the shuffled order.
        $questions = Question::whereIn('id', $ids)->with('currentRevision')->get()
            ->sortBy(fn ($q) => [$q->type === 'theory' ? 1 : 0, array_search($q->id, $ids)])->values();

        // Multiple-choice options are shuffled too; true/false stays True then False.
        $orders = $questions->mapWithKeys(function (Question $q) {
            $options = array_column($q->currentRevision->payload['options'] ?? [], 'id');

            return [$q->id => $q->type === 'mcq' ? collect($options)->shuffle()->all() : $options];
        })->all();

        $timed = ($filters['mode'] ?? 'practice') === 'timed' && $questions->isNotEmpty();
        $minutes = $timed ? max(1, $questions->count() * self::MINUTES_PER_QUESTION) : null;

        // The set: which questions, when it started and how long it may take.
        $request->session()->put('practice.set', [
            'subject_id' => $subject->id,
            'ids' => $questions->pluck('id')->all(),
            'started_at' => now()->timestamp,
            'minutes' => $minutes,
            'filters' => $filters,
            'orders' => $orders,
        ]);

        return view('public.practice.show', [
            'subject' => $subject->load('educationLevel'),
            'questions' => $questions,
            'orders' => $orders,
            'minutes' => $minutes,
            'filters' => $filters,
        ]);
    }

    public function mark(Request $request): View
    {
        $set = $request->session()->pull('practice.set');
        abort_if(! $set || $set['ids'] === [], 419, 'This practice set has expired. Please start a new one.');

        $answers = $request->validate(['answers' => ['array'], 'answers.*' => ['nullable', 'string', 'max:3000']])['answers'] ?? [];
        $subject = Subject::with('educationLevel')->findOrFail($set['subject_id']);
        $seconds = now()->timestamp - $set['started_at'];
        // A short grace period covers the auto-submit round trip; later answers still count but are flagged.
        $late = $set['minutes'] && $seconds > $set['minutes'] * 60 + 30;

        $questions = Question::whereIn('id', $set['ids'])->with('currentRevision')->get()
            ->sortBy(fn ($q) => array_search($q->id, $set['ids']))->values();
        $keys = QuestionKey::whereIn('question_revision_id', $questions->pluck('current_revision_id'))->pluck('key', 'question_revision_id');

        $results = $questions->map(function (Question $question) use ($answers, $keys) {
            $key = $keys[$question->current_revision_id] ?? [];
            $given = $answers[$question->ulid] ?? null;
            $given = is_string($given) ? trim($given) : null;
            $given = $given === '' ? null : $given;

            // Theory is not marked by the computer: the learner compares with the model answer.
            if ($question->type === 'theory') {
                return ['question' => $question, 'given' => $given, 'correct' => null, 'right' => false, 'key' => $key, 'theory' => true];
            }
            $right = $question->type === 'fill_in'
                ? $given !== null && in_array(self::normalise($given), array_map(fn ($a) => self::normalise((string) $a), $key['accepted'] ?? []), true)
                : $given !== null && $given === ($key['correct'] ?? null);

            return ['question' => $question, 'given' => $given, 'correct' => $key['correct'] ?? null, 'right' => $right, 'key' => $key, 'theory' => false];
        });
        $theory = $results->where('theory', true)->values();
        $results = $results->where('theory', false)->values()->concat($theory);
        $marked = $results->where('theory', false);

        $score = $marked->where('right', true)->count();
        $byDifficulty = collect(self::DIFFICULTIES)->map(function ($range) use ($marked) {
            $rows = $marked->filter(fn ($r) => $r['question']->difficulty >= $range[0] && $r['question']->difficulty <= $range[1]);

            return ['total' => $rows->count(), 'right' => $rows->where('right', true)->count()];
        })->filter(fn ($row) => $row['total'] > 0);

        // Signed-in students keep their score (My progress, leaderboard).
        if (! $subject->isProfessional()) {
            QuizAttempt::recordFor($request->user(), [
                'subject_id' => $subject->id, 'kind' => 'practice', 'score' => $score,
                'total' => $marked->count(), 'seconds' => $seconds, 'late' => (bool) $late,
            ])?->recordAnswers($results);
        }

        // Keep the last few scores for the practice home page (this browser session only).
        $recent = collect($request->session()->get('practice.recent', []))
            ->prepend(['subject' => $subject->name, 'slug' => $subject->slug, 'score' => $score, 'total' => $marked->count(), 'at' => now()->toIso8601String()])
            ->take(5)->values()->all();
        $request->session()->put('practice.recent', $recent);

        return view('public.practice.result', [
            'subject' => $subject,
            'results' => $results,
            'markedTotal' => $marked->count(),
            'score' => $score,
            'seconds' => $seconds,
            'late' => $late,
            'byDifficulty' => $byDifficulty,
            'retry' => $set['filters'] ?? ['subject' => $subject->slug],
            'orders' => $set['orders'] ?? [],
        ]);
    }

    /**
     * Options in the order the learner saw them, each with its display letter. Answers are still
     * submitted and marked by the option's own id.
     *
     * @return list<array{id: string, text: string, label: string}>
     */
    public static function arrangedOptions(Question $question, array $orders): array
    {
        $options = collect($question->currentRevision->payload['options'] ?? [])->keyBy('id');
        $order = $orders[$question->id] ?? $options->keys()->all();
        $letters = range('A', 'Z');

        return collect($order)->filter(fn ($id) => $options->has($id))->values()
            ->map(fn ($id, $i) => $options[$id] + ['label' => $letters[$i]])
            ->all();
    }

    /** The types ticked on the form; the old single "type" field and the default still work. */
    public static function selectedTypes(array $filters): array
    {
        $types = array_values(array_intersect(Question::PRACTICE_TYPES, $filters['types'] ?? []));
        if ($types) {
            return $types;
        }

        return ($filters['type'] ?? null) === 'theory' ? ['theory'] : Question::OBJECTIVE_TYPES;
    }

    /**
     * A fresh random set: questions this learner answered recently in the subject are held back
     * until the matching pool runs out, then the oldest ones come round again.
     *
     * @param  list<int>  $pool
     * @return list<int>
     */
    private function pick(Request $request, Subject $subject, int $count, array $pool): array
    {
        $key = 'practice.seen.'.$subject->id;
        $seen = $request->session()->get($key, []);
        $fresh = array_values(array_diff($pool, $seen));
        shuffle($fresh);
        $ids = array_slice($fresh, 0, $count);

        if (count($ids) < $count) {
            // Top up with the questions seen longest ago.
            $again = array_values(array_intersect($seen, $pool));
            $ids = array_merge($ids, array_slice($again, 0, $count - count($ids)));
            shuffle($ids);
        }

        $request->session()->put($key, array_slice(array_values(array_unique(array_merge(array_diff($seen, $ids), $ids))), -self::SEEN_LIMIT));

        return $ids;
    }

    /** Lower case, single spaces, no surrounding punctuation: "  Mouse. " matches "mouse". */
    public static function normalise(string $answer): string
    {
        return trim(preg_replace('/\s+/', ' ', mb_strtolower($answer)), " 	

.,;:!?\"'");
    }

    private function filters(Request $request): array
    {
        return $request->validate([
            'level' => ['nullable', 'string', 'max:20'],
            'year' => ['nullable', 'integer'],
            'subject' => ['nullable', 'string', 'max:120'],
            'topic' => ['nullable', 'integer'],
            'difficulty' => ['nullable', 'in:easy,medium,hard'],
            'count' => ['nullable', 'integer', 'in:'.implode(',', self::COUNTS)],
            'mode' => ['nullable', 'in:practice,timed'],
            'type' => ['nullable', 'in:objective,theory'],
            'types' => ['nullable', 'array'],
            'types.*' => ['string', 'in:'.implode(',', Question::PRACTICE_TYPES)],
        ]);
    }

    /** @return array{0: Collection, 1: ?EducationLevel, 2: ?Subject, 3: mixed, 4: ?CurriculumNode} */
    private function resolve(array $filters): array
    {
        $levels = EducationLevel::active()->ordered()->with('classLevels')->get();
        $subject = ! empty($filters['subject']) ? Subject::where('slug', $filters['subject'])->first() : null;
        $level = $levels->firstWhere('code', strtoupper($filters['level'] ?? '')) ?? ($subject ? $levels->firstWhere('id', $subject->education_level_id) : null);
        $year = ! empty($filters['year']) ? $levels->flatMap->classLevels->firstWhere('id', (int) $filters['year']) : null;
        $topic = ($subject && ! empty($filters['topic'])) ? CurriculumNode::where('subject_id', $subject->id)->find($filters['topic']) : null;

        return [$levels, $level, $subject, $year, $topic];
    }

    private function filtered(bool $premium, Subject $subject, array $filters, $year, ?CurriculumNode $topic, string $confidentiality = 'practice'): Builder
    {
        $range = self::DIFFICULTIES[$filters['difficulty'] ?? ''] ?? null;

        return $this->bank($premium, $confidentiality)
            ->whereIn('type', self::selectedTypes($filters))
            ->where('subject_id', $subject->id)
            // A question tagged to no year counts for every year of its level.
            ->when($year, fn ($q) => $q->where(fn ($w) => $w->whereNull('class_level_id')->orWhere('class_level_id', $year->id)))
            ->when($range, fn ($q) => $q->whereBetween('difficulty', $range))
            ->when($topic, fn ($q) => $q->whereHas('curriculumNodes', fn ($n) => $n->where('curriculum_nodes.id', $topic->id)->orWhere('curriculum_nodes.path', 'like', $topic->childPath().'%')));
    }

    /** Published self-test questions; premium ones only for subscribers. */
    private function bank(bool $premium, string $confidentiality = 'practice'): Builder
    {
        return Question::query()
            ->where('status', 'published')
            ->where('confidentiality', $confidentiality)
            ->whereNotNull('current_revision_id')
            ->when(! $premium, fn ($q) => $q->where('access_level', 'free'));
    }
}
