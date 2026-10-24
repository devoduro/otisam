<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\ExamPaper;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuizAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Numbered WASSCE/BECE-style objective papers built from Otisam's own questions.
 * Study mode checks one answer at a time and shows its explanation; exam mode is timed and marked
 * on the server when submitted (keys never reach the page before that). The solutions page lists
 * every answer and explanation for signed-in users.
 */
class PaperController extends Controller
{
    public function index(Request $request): View
    {
        $level = $request->validate(['level' => ['nullable', 'in:jhs,shs']])['level'] ?? null;
        $counts = ExamPaper::visible()->selectRaw('subject_id, count(*) as n')->groupBy('subject_id')->pluck('n', 'subject_id');
        $levels = EducationLevel::active()->ordered()->where('code', '!=', EducationLevel::PROFESSIONAL)
            ->when($level, fn ($q) => $q->where('code', strtoupper($level)))
            ->with(['subjects' => fn ($q) => $q->active()->whereIn('id', $counts->keys())->orderBy('sort')->orderBy('name')])
            ->get()->filter(fn ($l) => $l->subjects->isNotEmpty());

        return view('public.papers.index', ['levels' => $levels, 'counts' => $counts, 'level' => $level]);
    }

    public function subject(Request $request, Subject $subject): View
    {
        $this->ensurePublic($subject);
        $papers = ExamPaper::visible()->where('subject_id', $subject->id)->withCount('questions')->orderBy('number')->get();

        return view('public.papers.subject', [
            'subject' => $subject->load('educationLevel'),
            'papers' => $papers,
            // The signed-in student's best exam-mode score per paper.
            'best' => $request->user()
                ? QuizAttempt::where('user_id', $request->user()->id)->where('kind', 'paper')->whereIn('exam_paper_id', $papers->pluck('id'))
                    ->groupBy('exam_paper_id')->selectRaw('exam_paper_id, max(score) as best')->pluck('best', 'exam_paper_id')
                : collect(),
        ]);
    }

    public function show(Subject $subject, int $number): View
    {
        $paper = $this->paper($subject, $number);

        return view('public.papers.show', ['subject' => $subject, 'paper' => $paper->loadCount('questions')]);
    }

    public function take(Request $request, Subject $subject, int $number): View
    {
        $paper = $this->paper($subject, $number);
        $mode = $request->validate(['mode' => ['nullable', 'in:study,exam']])['mode'] ?? 'study';
        $questions = $paper->questions()->with('currentRevision')->get();

        if ($mode === 'exam') {
            $request->session()->put("papers.attempt.{$paper->id}", ['started_at' => now()->timestamp, 'minutes' => $paper->duration_minutes]);
        }

        return view('public.papers.take', [
            'mode' => $mode,
            'questions' => $questions,
            'title' => $paper->title,
            'eyebrow' => $paper->exam_style.' style · '.($mode === 'exam' ? 'Exam mode' : 'Study mode'),
            'backUrl' => route('papers.show', [$subject, $paper->number]),
            'backLabel' => $paper->title,
            'checkUrl' => route('papers.check', [$subject, $paper->number]),
            'markUrl' => route('papers.mark', [$subject, $paper->number]),
            'examUrl' => route('papers.take', [$subject, $paper->number, 'mode' => 'exam']),
            'seconds' => $paper->duration_minutes * 60,
            'examStyle' => $paper->exam_style,
        ]);
    }

    /** Study mode: whether one answer is right, the right option and the explanation. */
    public function check(Request $request, Subject $subject, int $number): JsonResponse
    {
        $paper = $this->paper($subject, $number);
        $data = $request->validate(['question' => ['required', 'string', 'max:40'], 'answer' => ['required', 'string', 'max:2']]);
        $question = $paper->questions()->where('ulid', $data['question'])->with('currentRevision')->firstOrFail();
        $correct = QuestionKey::where('question_revision_id', $question->current_revision_id)->value('key')['correct'] ?? null;

        return response()->json([
            'right' => $correct !== null && $data['answer'] === $correct,
            'correct' => $correct,
            'explanation' => $question->currentRevision?->explanation,
        ]);
    }

    /** Exam mode: mark the whole paper. */
    public function mark(Request $request, Subject $subject, int $number): View
    {
        $paper = $this->paper($subject, $number);
        $attempt = $request->session()->pull("papers.attempt.{$paper->id}");
        abort_unless($attempt, 419, 'This exam has expired. Please start it again.');

        $answers = $request->validate(['answers' => ['array'], 'answers.*' => ['nullable', 'string', 'max:2']])['answers'] ?? [];
        $seconds = now()->timestamp - $attempt['started_at'];
        // A short grace period covers the auto-submit round trip; later submissions are still marked but flagged.
        $late = $seconds > $attempt['minutes'] * 60 + 30;

        $questions = $paper->questions()->with('currentRevision')->get();
        $keys = QuestionKey::whereIn('question_revision_id', $questions->pluck('current_revision_id'))->pluck('key', 'question_revision_id');
        $results = $questions->map(function ($question) use ($answers, $keys) {
            $correct = $keys[$question->current_revision_id]['correct'] ?? null;
            $given = $answers[$question->ulid] ?? null;

            return ['question' => $question, 'given' => $given, 'correct' => $correct, 'right' => $given !== null && $given === $correct];
        });

        $score = $results->where('right', true)->count();
        QuizAttempt::recordFor($request->user(), [
            'subject_id' => $subject->id, 'kind' => 'paper', 'exam_paper_id' => $paper->id,
            'score' => $score, 'total' => $results->count(), 'seconds' => $seconds, 'late' => $late,
        ])?->recordAnswers($results);

        return view('public.papers.result', [
            'subject' => $subject,
            'paper' => $paper,
            'results' => $results,
            'score' => $score,
            'saved' => (bool) $request->user()?->isStudent(),
            'seconds' => $seconds,
            'late' => $late,
        ]);
    }

    public function solutions(Subject $subject, int $number): View
    {
        $paper = $this->paper($subject, $number);
        $questions = $paper->questions()->with('currentRevision')->get();

        return view('public.papers.solutions', [
            'subject' => $subject,
            'paper' => $paper,
            'questions' => $questions,
            'keys' => QuestionKey::whereIn('question_revision_id', $questions->pluck('current_revision_id'))->pluck('key', 'question_revision_id'),
        ]);
    }

    private function paper(Subject $subject, int $number): ExamPaper
    {
        $this->ensurePublic($subject);

        return ExamPaper::visible()->where('subject_id', $subject->id)->where('number', $number)->firstOrFail();
    }

    private function ensurePublic(Subject $subject): void
    {
        $subject->loadMissing('educationLevel');
        abort_unless($subject->is_active && $subject->educationLevel?->is_active && ! $subject->isProfessional(), 404);
    }
}
