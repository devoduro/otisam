<?php

namespace App\Http\Controllers\Teach;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\PracticeController;
use App\Models\Classroom\Assignment;
use App\Models\Classroom\Classroom;
use App\Models\Classroom\Submission;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use App\Services\Audit;
use App\Services\Classroom\AssignmentService;
use App\Services\Classroom\PrivateFiles;
use App\Services\Notifications\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        ]);

        $added = $this->service->addFromBank($assignment, Subject::findOrFail($data['subject_id']), (int) $data['count'], PracticeController::DIFFICULTIES[$data['difficulty'] ?? ''] ?? null);

        return back()->with($added ? 'success' : 'error', $added ? "{$added} question(s) added from the question bank." : 'No more matching questions in the bank for that subject.');
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
