<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Community\Answer;
use App\Models\Community\Question;
use App\Models\Community\Report;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Rules\PlainText;
use App\Services\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Community Q&A ("Q&A" in the menus, so it is not confused with the question bank). Anyone can
 * read; signed-in students and teachers ask, edit and delete their own questions. Answers are in
 * QaAnswerController; staff review reports in Admin\QaModerationController.
 */
class QaController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'level' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:120'],
            'unanswered' => ['nullable', 'boolean'],
            'mine' => ['nullable', 'boolean'],
        ]);
        $user = $request->user();

        $levels = $this->levels();
        $level = $levels->firstWhere('code', strtoupper($filters['level'] ?? ''));
        $subject = ! empty($filters['subject']) ? Subject::where('slug', $filters['subject'])->first() : null;
        $level ??= $subject ? $levels->firstWhere('id', $subject->education_level_id) : null;
        $mine = $user && ! empty($filters['mine']);

        $questions = Question::query()
            // Your own hidden questions still show in "My questions", marked as hidden.
            ->when($mine, fn ($q) => $q->where('user_id', $user->id), fn ($q) => $q->visible())
            ->with(['author', 'educationLevel', 'classLevel', 'subject'])
            ->withCount(['answers as answer_count' => fn ($a) => $a->visible()])
            ->when($level, fn ($q) => $q->where('education_level_id', $level->id))
            ->when($subject, fn ($q) => $q->where('subject_id', $subject->id))
            ->when(! empty($filters['unanswered']), fn ($q) => $q->whereDoesntHave('answers', fn ($a) => $a->visible()))
            ->latest()->latest('id')
            ->paginate(20)->withQueryString();

        return view('public.qa.index', [
            'questions' => $questions,
            'filters' => $filters,
            'levels' => $levels,
            'level' => $level,
            'subject' => $subject,
            'subjects' => $this->subjects($level),
            'canAsk' => $this->canPost($user),
        ]);
    }

    public function show(Request $request, Question $question): View
    {
        $user = $request->user();
        abort_unless($question->canBeViewedBy($user), 404);
        $isModerator = Question::isModerator($user);

        $answers = $question->answers()
            ->when(! $isModerator, fn ($q) => $q->where(fn ($w) => $w->visible()->when($user, fn ($o) => $o->orWhere('user_id', $user->id))))
            ->with('author.teacherProfile')
            ->oldest()->oldest('id')->get()
            // The accepted answer goes first.
            ->sortByDesc(fn (Answer $a) => $a->id === $question->accepted_answer_id)->values();

        return view('public.qa.show', [
            'question' => $question->load(['author', 'educationLevel', 'classLevel', 'subject']),
            'answers' => $answers,
            'isModerator' => $isModerator,
            'canAnswer' => $this->canPost($user) && $question->isVisible(),
            'canAccept' => $user && ($question->isOwnedBy($user) || $isModerator),
            'reasons' => Report::REASONS,
        ]);
    }

    public function create(Request $request): View
    {
        $profile = $request->user()->studentProfile;
        $class = $profile?->class_level_id ? ClassLevel::find($profile->class_level_id) : null;

        return view('public.qa.form', $this->formData(new Question([
            // Students start with their own class filled in.
            'education_level_id' => $class?->education_level_id,
            'class_level_id' => $class?->id,
        ])));
    }

    public function store(Request $request): RedirectResponse
    {
        $question = new Question($this->validated($request));
        $question->user_id = $request->user()->id;
        $question->save();

        return redirect()->route('qa.show', $question)->with('success', 'Your question is posted. You will be told when someone answers.');
    }

    public function edit(Request $request, Question $question): View
    {
        abort_unless($question->isOwnedBy($request->user()), 404);

        return view('public.qa.form', $this->formData($question));
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        abort_unless($question->isOwnedBy($request->user()), 404);
        $question->fill($this->validated($request));
        if ($question->isDirty()) {
            $question->edited_at = now();
            $question->save();
        }

        return redirect()->route('qa.show', $question)->with('success', 'Question updated.');
    }

    public function destroy(Request $request, Question $question): RedirectResponse
    {
        abort_unless($question->isOwnedBy($request->user()), 404);
        $question->delete();

        return redirect()->route('qa.index', ['mine' => 1])->with('success', 'Question deleted.');
    }

    /** The asker (or a moderator) marks one answer as the accepted one; again to undo. */
    public function accept(Request $request, Question $question, Answer $answer): RedirectResponse
    {
        $user = $request->user();
        abort_unless($answer->qa_question_id === $question->id && $answer->isVisible() && $question->isVisible(), 404);
        abort_unless($question->isOwnedBy($user) || Question::isModerator($user), 403);

        $undo = $question->accepted_answer_id === $answer->id;
        $question->accepted_answer_id = $undo ? null : $answer->id;
        $question->save();

        if (! $question->isOwnedBy($user)) {
            Audit::record('content', $undo ? 'qa.unaccepted' : 'qa.accepted', ($undo ? 'Accepted answer cleared: ' : 'Answer accepted by staff: ').$question->title, $question);
        }

        return redirect()->to(route('qa.show', $question).'#answer-'.$answer->id)
            ->with('success', $undo ? 'Answer no longer marked as accepted.' : 'Answer marked as accepted.');
    }

    public function reportQuestion(Request $request, Question $question): RedirectResponse
    {
        return $this->report($request, $question, route('qa.show', $question));
    }

    public function reportAnswer(Request $request, Answer $answer): RedirectResponse
    {
        abort_unless($answer->question?->canBeViewedBy($request->user()), 404);

        return $this->report($request, $answer, route('qa.show', $answer->qa_question_id).'#answer-'.$answer->id);
    }

    /** One report per person per post. Enough open reports hide the post until staff review it. */
    private function report(Request $request, Model $post, string $back): RedirectResponse
    {
        $user = $request->user();
        abort_unless($post->canBeViewedBy($user), 404);
        if ($post->isOwnedBy($user)) {
            return redirect()->to($back)->with('error', 'You cannot report your own post.');
        }
        $data = $request->validate([
            'reason' => ['required', Rule::in(array_keys(Report::REASONS))],
            'note' => ['nullable', 'string', 'max:300', new PlainText],
        ]);

        if ($post->reports()->where('user_id', $user->id)->exists()) {
            return redirect()->to($back)->with('status', 'You have already reported this. Thank you.');
        }
        try {
            $post->reports()->create(['user_id' => $user->id] + $data);
        } catch (UniqueConstraintViolationException) {
            return redirect()->to($back)->with('status', 'You have already reported this. Thank you.');
        }

        if ($post->hideIfReportedEnough()) {
            Audit::record('content', 'qa.auto-hidden', 'Hidden after reports: '.$post->label(), $post, ['status' => 'visible'], ['status' => 'hidden', 'reason' => 'reports']);
        }

        return redirect()->to($back)->with('success', 'Thank you. Our team will review it.');
    }

    /** Asking and answering are for student and teacher accounts. */
    public static function canPost($user): bool
    {
        return $user && ($user->isStudent() || $user->isTeacher());
    }

    private function validated(Request $request): array
    {
        $levelIds = $this->levels()->pluck('id')->all();

        return $request->validate([
            'title' => ['required', 'string', 'min:10', 'max:200', new PlainText],
            'body' => ['required', 'string', 'min:10', 'max:5000', new PlainText],
            'education_level_id' => ['required', 'integer', Rule::in($levelIds)],
            'class_level_id' => ['nullable', 'integer', Rule::exists('class_levels', 'id')->where('education_level_id', $request->integer('education_level_id'))],
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')->where('education_level_id', $request->integer('education_level_id'))->where('is_active', true)],
        ], [
            'class_level_id.exists' => 'Pick a class from the level you chose.',
            'subject_id.exists' => 'Pick a subject from the level you chose.',
        ], [
            'body' => 'details',
            'education_level_id' => 'level',
        ]);
    }

    private function formData(Question $question): array
    {
        $levels = $this->levels();

        return [
            'question' => $question,
            'levels' => $levels,
            'classLevels' => ClassLevel::whereIn('education_level_id', $levels->pluck('id'))->orderBy('sort')->get(['id', 'name', 'education_level_id']),
            'subjects' => $this->subjects(null),
        ];
    }

    private function levels()
    {
        return EducationLevel::active()->ordered()->where('code', '!=', EducationLevel::PROFESSIONAL)->get();
    }

    private function subjects(?EducationLevel $level)
    {
        return Subject::active()->whereIn('education_level_id', $level ? [$level->id] : $this->levels()->pluck('id'))
            ->orderBy('sort')->orderBy('name')->get(['id', 'name', 'slug', 'education_level_id']);
    }
}
