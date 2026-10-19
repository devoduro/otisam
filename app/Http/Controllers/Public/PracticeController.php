<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
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

        $available = $subject ? $this->filtered($premium, $subject, $filters, $year, $topic)->count() : null;

        return view('public.practice.index', [
            'filters' => $filters,
            'levels' => $levels,
            'level' => $level,
            'subject' => $subject,
            'subjects' => $subjects,
            'topics' => $topics,
            'counts' => $counts,
            'available' => $available,
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
        $questions = $this->filtered($promotion || $entitlements->hasPremium($request->user()), $subject, $filters, $year, $topic, $promotion ? 'promotion' : 'practice')
            ->with('currentRevision')->inRandomOrder()->limit($count)->get();

        $timed = ($filters['mode'] ?? 'practice') === 'timed' && $questions->isNotEmpty();
        $minutes = $timed ? max(1, $questions->count() * self::MINUTES_PER_QUESTION) : null;

        // The set: which questions, when it started and how long it may take.
        $request->session()->put('practice.set', [
            'subject_id' => $subject->id,
            'ids' => $questions->pluck('id')->all(),
            'started_at' => now()->timestamp,
            'minutes' => $minutes,
            'filters' => $filters,
        ]);

        return view('public.practice.show', [
            'subject' => $subject->load('educationLevel'),
            'questions' => $questions,
            'minutes' => $minutes,
            'filters' => $filters,
        ]);
    }

    public function mark(Request $request): View
    {
        $set = $request->session()->pull('practice.set');
        abort_if(! $set || $set['ids'] === [], 419, 'This practice set has expired. Please start a new one.');

        $answers = $request->validate(['answers' => ['array'], 'answers.*' => ['nullable', 'string', 'max:2']])['answers'] ?? [];
        $subject = Subject::with('educationLevel')->findOrFail($set['subject_id']);
        $seconds = now()->timestamp - $set['started_at'];
        // A short grace period covers the auto-submit round trip; later answers still count but are flagged.
        $late = $set['minutes'] && $seconds > $set['minutes'] * 60 + 30;

        $questions = Question::whereIn('id', $set['ids'])->with('currentRevision')->get()
            ->sortBy(fn ($q) => array_search($q->id, $set['ids']))->values();
        $keys = QuestionKey::whereIn('question_revision_id', $questions->pluck('current_revision_id'))->pluck('key', 'question_revision_id');

        $results = $questions->map(function (Question $question) use ($answers, $keys) {
            $correct = $keys[$question->current_revision_id]['correct'] ?? null;
            $given = $answers[$question->ulid] ?? null;

            return ['question' => $question, 'given' => $given, 'correct' => $correct, 'right' => $given !== null && $given === $correct];
        });

        $score = $results->where('right', true)->count();
        $byDifficulty = collect(self::DIFFICULTIES)->map(function ($range) use ($results) {
            $rows = $results->filter(fn ($r) => $r['question']->difficulty >= $range[0] && $r['question']->difficulty <= $range[1]);

            return ['total' => $rows->count(), 'right' => $rows->where('right', true)->count()];
        })->filter(fn ($row) => $row['total'] > 0);

        // Keep the last few scores for the practice home page (this browser session only).
        $recent = collect($request->session()->get('practice.recent', []))
            ->prepend(['subject' => $subject->name, 'slug' => $subject->slug, 'score' => $score, 'total' => $results->count(), 'at' => now()->toIso8601String()])
            ->take(5)->values()->all();
        $request->session()->put('practice.recent', $recent);

        return view('public.practice.result', [
            'subject' => $subject,
            'results' => $results,
            'score' => $score,
            'seconds' => $seconds,
            'late' => $late,
            'byDifficulty' => $byDifficulty,
            'retry' => $set['filters'] ?? ['subject' => $subject->slug],
        ]);
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
            ->where('type', 'mcq')
            ->whereNotNull('current_revision_id')
            ->when(! $premium, fn ($q) => $q->where('access_level', 'free'));
    }
}
