<?php

namespace App\Http\Controllers\Teach;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\PracticeController;
use App\Models\Classroom\Assignment;
use App\Models\Classroom\Classroom;
use App\Models\Classroom\Submission;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use App\Services\Audit;
use App\Services\Classroom\AssignmentService;
use App\Services\Classroom\PrivateFiles;
use App\Services\Classroom\QuizReport;
use App\Services\Notifications\Notifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentController extends Controller
{
    public function __construct(private readonly AssignmentService $service, private readonly PrivateFiles $files) {}

    public function create(Request $request, Classroom $classroom): View
    {
        ClassroomController::authorizeOwner($request, $classroom);
        $type = $request->query('type') === 'quiz' ? 'quiz' : 'assignment';

        return view('teach.assignments.form', [
            'classroom' => $classroom,
            'assignment' => new Assignment(['type' => $type, 'show_answers' => true]),
        ]);
    }

    public function store(Request $request, Classroom $classroom): RedirectResponse
    {
        ClassroomController::authorizeOwner($request, $classroom);
        abort_if($classroom->isArchived(), 403, 'This class is archived.');

        $assignment = new Assignment($this->validated($request));
        $assignment->classroom_id = $classroom->id;
        $assignment->teacher_id = $request->user()->id;
        $assignment->status = 'draft';
        $this->attachHandout($request, $assignment);
        $assignment->save();

        return redirect()->route('teach.assignments.show', $assignment)->with('success', $assignment->isQuiz()
            ? 'Quiz saved as a draft. Add questions, then publish it to the class.'
            : 'Assignment saved as a draft. Check it, then publish it to the class.');
    }

    public function show(Request $request, Assignment $assignment): View
    {
        $this->authorizeOwner($request, $assignment);
        $assignment->load(['classroom.students', 'classroom.subject', 'questions.currentRevision']);

        $submissions = $assignment->submissions()->with('student')->get()->keyBy('student_id');
        $revisions = QuestionRevision::whereIn('id', $assignment->questions->pluck('pivot.question_revision_id'))->get()->keyBy('id');
        $keys = QuestionKey::whereIn('question_revision_id', $revisions->keys())->pluck('key', 'question_revision_id');

        return view('teach.assignments.show', [
            'assignment' => $assignment,
            'submissions' => $submissions,
            'revisions' => $revisions,
            'keys' => $keys,
            'levels' => EducationLevel::active()->ordered()->with(['subjects' => fn ($q) => $q->where('is_active', true)->orderBy('sort')])->get(),
            'locked' => $submissions->contains(fn ($s) => $s->isSubmitted()),
            'bankTopics' => $assignment->classroom->subject ? $this->service->bankTopics($assignment->classroom->subject) : collect(),
        ]);
    }

    public function edit(Request $request, Assignment $assignment): View
    {
        $this->authorizeOwner($request, $assignment);

        return view('teach.assignments.form', ['classroom' => $assignment->classroom, 'assignment' => $assignment]);
    }

    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        $assignment->fill($this->validated($request, $assignment));
        $this->attachHandout($request, $assignment);
        $assignment->save();

        return redirect()->route('teach.assignments.show', $assignment)->with('success', 'Saved.');
    }

    public function publish(Request $request, Assignment $assignment, Notifier $notifier): RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        $first = $assignment->published_at === null;

        try {
            $this->service->publish($assignment);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($first) {
            $classroom = $assignment->classroom()->with('students')->first();
            $due = $assignment->due_at ? ' Due '.$assignment->due_at->format('j M, g:ia').'.' : '';
            foreach ($classroom->students as $student) {
                $kind = strtolower(Assignment::TYPES[$assignment->type]);
                $notifier->toUser($student, 'assignment.published', 'classroom', "New {$kind}: {$assignment->title}",
                    "{$classroom->name}: new {$kind} \"{$assignment->title}\".{$due} Open ".route('learn.assignments.show', $assignment),
                    "assignment.published.{$assignment->id}.{$student->id}");
            }
        }
        Audit::record('classroom', 'assignment.published', "Published {$assignment->type}: {$assignment->title}", $assignment);

        return back()->with('success', 'Published. Students in the class can see it now.');
    }

    public function close(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        $assignment->forceFill(['status' => $assignment->status === 'closed' ? 'published' : 'closed'])->save();

        return back()->with('success', $assignment->status === 'closed' ? 'Closed. Students can no longer hand in work.' : 'Re-opened for submissions.');
    }

    public function destroy(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        $classroom = $assignment->classroom;
        $assignment->delete();

        return redirect()->route('teach.classes.show', $classroom)->with('success', 'Deleted.');
    }

    public function addFromBank(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'count' => ['required', 'integer', 'min:1', 'max:'.AssignmentService::MAX_QUIZ_QUESTIONS],
            'difficulty' => ['nullable', Rule::in(array_keys(PracticeController::DIFFICULTIES))],
            'topic_id' => ['nullable', 'integer'],
        ]);
        $subject = Subject::findOrFail($data['subject_id']);
        $topic = ! empty($data['topic_id']) ? CurriculumNode::where('subject_id', $subject->id)->findOrFail($data['topic_id']) : null;

        $added = $this->service->addFromBank($assignment, $subject, (int) $data['count'], PracticeController::DIFFICULTIES[$data['difficulty'] ?? ''] ?? null, $topic);
        $where = $topic ? "the topic \"{$topic->title}\"" : 'that subject';

        return back()->with($added ? 'success' : 'error', $added ? "{$added} question(s) added from the question bank." : "No more matching questions in the bank for {$where}.");
    }

    /** Assessment details for a quiz: summary, every student's result and how each question went. */
    public function report(Request $request, Assignment $assignment, QuizReport $reports): View
    {
        $this->authorizeOwner($request, $assignment);
        abort_unless($assignment->isQuiz(), 404);
        $passMark = (int) ($request->validate(['pass' => ['nullable', 'integer', 'min:1', 'max:100']])['pass'] ?? 50);

        return view('teach.assignments.report', ['assignment' => $assignment->load('classroom'), 'report' => $reports->build($assignment, $passMark)]);
    }

    public function reportCsv(Request $request, Assignment $assignment, QuizReport $reports): StreamedResponse
    {
        $this->authorizeOwner($request, $assignment);
        abort_unless($assignment->isQuiz(), 404);
        $report = $reports->build($assignment->load('classroom'));

        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');
            fwrite($out, 'ï»¿');
            fputcsv($out, ['Name', 'Username', 'Status', 'Score', 'Out of', 'Percent', 'Time taken (min:sec)', 'Submitted', 'Late', 'Times left the page']);
            foreach ($report['students'] as $r) {
                fputcsv($out, [$r['name'], $r['username'], $r['status'], $r['score'], $r['max'], $r['percent'] !== null ? $r['percent'].'%' : '', $r['seconds'] !== null ? QuizReport::duration($r['seconds']) : '', $r['submitted_at']?->format('Y-m-d H:i'), $r['late'] ? 'Yes' : 'No', $r['tab_leaves']]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Question', 'Correct answer', 'Answered', 'Got it right', 'Percent right', 'Most common wrong answer']);
            foreach ($report['questions'] as $q) {
                fputcsv($out, [$q['number'].'. '.$q['stem'], trim($q['correct'].'. '.$q['correct_text']), $q['answered'], $q['right'], $q['percent'] !== null ? $q['percent'].'%' : '', $q['common_wrong'] ? $q['common_wrong'].' ('.$q['common_wrong_count'].')' : '']);
            }
            fclose($out);
        }, Str::slug($assignment->title).'-results.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store']);
    }

    /**
     * Browse the question bank by level, class year, subject and topic, and tick the questions to
     * put in the quiz. It opens on the class's own subject and year.
     */
    public function bank(Request $request, Assignment $assignment): View|RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        abort_unless($assignment->isQuiz(), 404);
        $filters = $request->validate([
            'level' => ['nullable', 'string', 'max:10'],
            'year' => ['nullable', 'integer'],
            'subject_id' => ['nullable', 'integer'],
            'topic_id' => ['nullable', 'integer'],
            'difficulty' => ['nullable', Rule::in(array_keys(PracticeController::DIFFICULTIES))],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $assignment->load('classroom.subject', 'classroom.classLevel');
        $classroom = $assignment->classroom;
        $levels = EducationLevel::active()->ordered()
            ->with(['subjects' => fn ($q) => $q->where('is_active', true)->orderBy('sort')->orderBy('name'), 'classLevels'])->get();

        // With no filters chosen yet, start from the class's subject and year.
        $fresh = ! $request->hasAny(['level', 'year', 'subject_id']);
        $subject = Subject::find($filters['subject_id'] ?? ($fresh ? $classroom->subject_id : null));
        $level = $levels->firstWhere('code', strtoupper($filters['level'] ?? ''))
            ?? $levels->firstWhere('id', $subject?->education_level_id ?? $classroom->classLevel?->education_level_id)
            ?? $levels->first();
        if ($subject && $subject->education_level_id !== $level?->id) {
            $subject = null;
        }
        $year = $level?->classLevels->firstWhere('id', (int) ($filters['year'] ?? ($fresh ? $classroom->class_level_id : 0)));
        $topic = $subject && ! empty($filters['topic_id']) ? CurriculumNode::where('subject_id', $subject->id)->find($filters['topic_id']) : null;

        $questions = $subject
            ? $this->service->bankQuestions($subject, PracticeController::DIFFICULTIES[$filters['difficulty'] ?? ''] ?? null, $topic, $filters['q'] ?? null, $year)
                ->with('currentRevision', 'classLevel:id,name')->orderBy('difficulty')->orderBy('id')->paginate(20)->withQueryString()
            : null;
        $keys = $questions ? QuestionKey::whereIn('question_revision_id', $questions->pluck('current_revision_id'))->pluck('key', 'question_revision_id') : collect();

        return view('teach.assignments.bank', [
            'assignment' => $assignment,
            'levels' => $levels,
            'level' => $level,
            'year' => $year,
            'subject' => $subject,
            'topic' => $topic,
            'filters' => $filters,
            'questions' => $questions,
            'keys' => $keys,
            'inQuiz' => $assignment->questions()->pluck('questions.id')->flip(),
            'quizCount' => $assignment->questions()->count(),
            'locked' => $assignment->submissions()->whereNotNull('submitted_at')->exists(),
            'topics' => $subject ? $this->service->bankTopics($subject, $year) : collect(),
        ]);
    }

    /** Adds the bank questions the teacher ticked on the browse page. */
    public function addSelected(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        $ids = $request->validate([
            'question_ids' => ['required', 'array', 'max:'.AssignmentService::MAX_QUIZ_QUESTIONS],
            'question_ids.*' => ['integer'],
        ], ['question_ids.required' => 'Tick at least one question to add.'])['question_ids'];

        $added = $this->service->addSelected($assignment, $ids);

        return back()->with($added ? 'success' : 'error', $added ? "{$added} question(s) added to the quiz." : 'Those questions are already in the quiz.');
    }

    /** The form for changing one question in the quiz. */
    public function editQuestion(Request $request, Assignment $assignment, Question $question): View
    {
        $this->authorizeOwner($request, $assignment);
        $current = $assignment->questions()->whereKey($question->id)->firstOrFail();
        $revision = QuestionRevision::findOrFail($current->pivot->question_revision_id);

        return view('teach.assignments.question-form', [
            'assignment' => $assignment,
            'question' => $question,
            'revision' => $revision,
            'correct' => QuestionKey::where('question_revision_id', $revision->id)->value('key')['correct'] ?? null,
            'marks' => (float) $current->pivot->marks,
            'number' => $assignment->questions()->wherePivot('sort', '<=', $current->pivot->sort)->count(),
            'isCopy' => ! ($question->owner_scope === 'teacher' && $question->author_id === $request->user()->id),
            'locked' => $assignment->submissions()->whereNotNull('submitted_at')->exists(),
        ]);
    }

    public function updateQuestion(Request $request, Assignment $assignment, Question $question): RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        $count = count((array) $request->input('options', []));
        $data = $request->validate([
            'stem' => ['required', 'string', 'max:2000'],
            'options' => ['required', 'array', 'min:4', 'max:5'],
            'options.*' => ['required', 'string', 'max:500', 'distinct:ignore_case'],
            'correct' => ['required', Rule::in(array_slice(['A', 'B', 'C', 'D', 'E'], 0, max(4, min(5, $count))))],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'marks' => ['required', 'numeric', 'min:0.5', 'max:20', 'multiple_of:0.5'],
        ], ['options.*.distinct' => 'Two options are the same.'], ['options.*' => 'option']);

        $this->service->editQuestion($assignment, $question, $request->user(), $data['stem'], array_values($data['options']), $data['correct'], $data['explanation'] ?? null, (float) $data['marks']);

        return redirect()->route('teach.assignments.show', $assignment)->with('success', 'Question saved.');
    }

    /** Topics with bank questions for a subject, for the "Add from the question bank" form. */
    public function bankTopics(Request $request): JsonResponse
    {
        $subject = Subject::findOrFail($request->validate(['subject_id' => ['required', 'integer']])['subject_id']);

        return response()->json($this->service->bankTopics($subject)->values());
    }

    public function addOwnQuestion(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'stem' => ['required', 'string', 'max:2000'],
            'options' => ['required', 'array', 'size:4'],
            'options.*' => ['required', 'string', 'max:500'],
            'correct' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
            'explanation' => ['nullable', 'string', 'max:2000'],
        ], [], ['options.*' => 'option']);

        $this->service->addOwnQuestion($assignment, $request->user(), Subject::findOrFail($data['subject_id']), $data['stem'], $data['options'], $data['correct'], $data['explanation'] ?? null);

        return back()->with('success', 'Question added.');
    }

    public function removeQuestion(Request $request, Assignment $assignment, Question $question): RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        $this->service->removeQuestion($assignment, $question);

        return back()->with('success', 'Question removed.');
    }

    public function grade(Request $request, Assignment $assignment, Submission $submission): RedirectResponse
    {
        $this->authorizeOwner($request, $assignment);
        abort_unless($submission->assignment_id === $assignment->id, 404);
        $data = $request->validate([
            'score' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'feedback' => ['nullable', 'string', 'max:3000'],
        ]);

        $this->service->grade($assignment, $submission, isset($data['score']) ? (float) $data['score'] : null, $data['feedback'] ?? null);

        return back()->with('success', "Marked {$submission->student->name}'s work.");
    }

    public function handout(Request $request, Assignment $assignment): StreamedResponse
    {
        $this->authorizeOwner($request, $assignment);
        abort_unless($assignment->file_path, 404);

        return $this->files->download($assignment->file_path, $assignment->file_name, $assignment->file_mime);
    }

    public function submissionFile(Request $request, Assignment $assignment, Submission $submission): StreamedResponse
    {
        $this->authorizeOwner($request, $assignment);
        abort_unless($submission->assignment_id === $assignment->id && $submission->file_path, 404);

        return $this->files->download($submission->file_path, $submission->file_name, $submission->file_mime);
    }

    private function authorizeOwner(Request $request, Assignment $assignment): void
    {
        abort_unless($assignment->teacher_id === $request->user()->id, 404);
    }

    private function validated(Request $request, ?Assignment $existing = null): array
    {
        $data = $request->validate([
            'type' => [$existing ? 'prohibited' : 'required', Rule::in(array_keys(Assignment::TYPES))],
            'title' => ['required', 'string', 'max:150'],
            'instructions' => ['nullable', 'string', 'max:10000'],
            'due_at' => ['nullable', 'date'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:300'],
            'max_marks' => ['nullable', 'numeric', 'min:1', 'max:1000'],
            'show_answers' => ['nullable', 'boolean'],
            'shuffle_options' => ['nullable', 'boolean'],
            'block_copy' => ['nullable', 'boolean'],
            'handout' => ['nullable', 'file', 'max:'.(config('platform.library.max_upload_mb') * 1024)],
        ]);
        $data['show_answers'] = $request->boolean('show_answers');
        $data['shuffle_options'] = $request->boolean('shuffle_options');
        $data['block_copy'] = $request->boolean('block_copy');
        unset($data['handout']);

        return $data;
    }

    private function attachHandout(Request $request, Assignment $assignment): void
    {
        if ($request->hasFile('handout')) {
            $old = $assignment->file_path;
            $assignment->forceFill($this->files->store($request->file('handout'), 'classrooms/handouts', 'handout'));
            $this->files->delete($old);
        }
    }
}
