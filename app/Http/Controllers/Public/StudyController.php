<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\LessonPlans\LessonPlan;
use App\Models\Library\Resource;
use App\Models\QuestionBank\ExamPaper;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\Videos\VideoLesson;
use App\Services\Practice\Leaderboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "Study": level → class → subject → one hub page that gathers everything Otisam has for that
 * subject (topics, past papers, practice, papers, videos, lesson plans, downloads), filtered to
 * the chosen class where the content is tagged by class.
 */
class StudyController extends Controller
{
    /** Questions in one quick-practice round. */
    public const QUICK_COUNT = 10;

    public function index(Leaderboard $leaderboard): View
    {
        return view('public.study.index', [
            'levels' => $this->levels()->load('classLevels'),
            'top' => $leaderboard->top(null, now()->startOfMonth(), 3),
        ]);
    }

    public function level(Request $request, string $level): View
    {
        $level = $this->levels()->firstWhere('code', strtoupper($level)) ?? abort(404);
        $level->load('classLevels');
        $filters = $request->validate(['class' => ['nullable', 'integer'], 'category' => ['nullable', 'in:core,elective']]);
        $class = $level->classLevels->firstWhere('id', (int) ($filters['class'] ?? 0));
        $category = $filters['category'] ?? 'core';

        return view('public.study.level', [
            'level' => $level,
            'class' => $class,
            'category' => $category,
            'subjects' => Subject::active()->where('education_level_id', $level->id)->where('category', $category)->orderBy('sort')->orderBy('name')->get(),
            'counts' => Subject::where('education_level_id', $level->id)->selectRaw('category, count(*) as n')->where('is_active', true)->groupBy('category')->pluck('n', 'category'),
        ]);
    }

    public function subject(Request $request, string $level, Subject $subject): View
    {
        $this->ensureSubject($subject, $level);
        $class = $subject->educationLevel->classLevels->firstWhere('id', $request->integer('class'));

        // A tagged class narrows the counts; untagged content counts for every class.
        $forClass = fn ($q, string $column = 'class_level_id') => $q->when($class, fn ($w) => $w->where(fn ($c) => $c->whereNull($column)->orWhere($column, $class->id)));

        $counts = [
            'topics' => CurriculumNode::where('subject_id', $subject->id)->whereHas('version', fn ($v) => $v->published())->count(),
            'questions' => $forClass(Question::where('subject_id', $subject->id)->where('status', 'published')->where('confidentiality', 'practice')->where('type', 'mcq'))->count(),
            'papers' => ExamPaper::visible()->where('subject_id', $subject->id)->count(),
            'past' => Resource::visible()->where('subject_id', $subject->id)->whereHas('category', fn ($c) => $c->where('is_exam_paper', true))->count(),
            'videos' => $forClass(VideoLesson::visible()->where('subject_id', $subject->id))->count(),
            'plans' => $forClass(LessonPlan::visible()->where('subject_id', $subject->id))->count(),
        ];

        // Practice by topic: topics with free practice questions, with how many each has.
        $topics = CurriculumNode::where('subject_id', $subject->id)
            ->whereHas('version', fn ($v) => $v->published())
            ->when($class, fn ($q) => $q->where(fn ($w) => $w->whereNull('class_level_id')->orWhere('class_level_id', $class->id)))
            ->withCount(['questions' => fn ($q) => $this->practiceBank($q, $subject)])
            ->with('classLevel:id,name')
            ->get()->where('questions_count', '>', 0)->sortByDesc('questions_count')->values();

        return view('public.study.subject', [
            'subject' => $subject,
            'level' => $subject->educationLevel,
            'class' => $class,
            'counts' => $counts,
            'topics' => $topics,
        ]);
    }

    /** Quick practice: random questions one at a time, each answer checked straight away. */
    public function quick(Request $request, string $level, Subject $subject): View
    {
        $this->ensureSubject($subject, $level);
        $class = $subject->educationLevel->classLevels->firstWhere('id', $request->integer('class'));
        $topic = $request->filled('topic') ? CurriculumNode::where('subject_id', $subject->id)->find($request->integer('topic')) : null;

        $questions = $this->practiceBank(Question::query(), $subject)
            ->when($class, fn ($q) => $q->where(fn ($w) => $w->whereNull('class_level_id')->orWhere('class_level_id', $class->id)))
            ->when($topic, fn ($q) => $q->whereHas('curriculumNodes', fn ($n) => $n->where('curriculum_nodes.id', $topic->id)))
            ->with('currentRevision')->inRandomOrder()->limit(self::QUICK_COUNT)->get();

        $back = route('study.subject', [strtolower($subject->educationLevel->code), $subject, 'class' => $class?->id]);

        return view('public.papers.take', [
            'mode' => 'study',
            'questions' => $questions,
            'title' => $topic ? $topic->title : "{$subject->name} quick practice",
            'eyebrow' => $subject->name.' · Quick practice · answer shown after each question',
            'backUrl' => $back,
            'backLabel' => $subject->name,
            'checkUrl' => route('study.check', [strtolower($subject->educationLevel->code), $subject]),
            'againUrl' => $request->fullUrl(),
            'examStyle' => $subject->examStyle(),
        ]);
    }

    /** Checks one quick-practice answer: only free published practice questions of this subject. */
    public function check(Request $request, string $level, Subject $subject): JsonResponse
    {
        $this->ensureSubject($subject, $level);
        $data = $request->validate(['question' => ['required', 'string', 'max:40'], 'answer' => ['required', 'string', 'max:2']]);
        $question = $this->practiceBank(Question::query(), $subject)->where('ulid', $data['question'])->with('currentRevision')->firstOrFail();
        $correct = QuestionKey::where('question_revision_id', $question->current_revision_id)->value('key')['correct'] ?? null;

        return response()->json([
            'right' => $correct !== null && $data['answer'] === $correct,
            'correct' => $correct,
            'explanation' => $question->currentRevision?->explanation,
        ]);
    }

    private function ensureSubject(Subject $subject, string $level): void
    {
        $subject->load('educationLevel.classLevels');
        abort_unless($subject->is_active && $subject->educationLevel?->is_active && strtoupper($level) === $subject->educationLevel->code && ! $subject->isProfessional(), 404);
    }

    /** Free, published self-test questions of the subject (all practice types). */
    private function practiceBank($query, Subject $subject)
    {
        return $query->where('questions.subject_id', $subject->id)->where('questions.status', 'published')
            ->where('questions.confidentiality', 'practice')->whereIn('questions.type', Question::PRACTICE_TYPES)
            ->where('questions.access_level', 'free')->whereNotNull('questions.current_revision_id');
    }

    /** JHS and SHS (the hidden professional level is never listed). */
    private function levels()
    {
        return EducationLevel::active()->ordered()->where('code', '!=', EducationLevel::PROFESSIONAL)->get();
    }
}
