<?php

namespace App\Http\Controllers\Learn;

use App\Http\Controllers\Controller;
use App\Models\Classroom\Assignment;
use App\Models\Classroom\Submission;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use App\Services\Classroom\AssignmentService;
use App\Services\Classroom\PrivateFiles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentController extends Controller
{
    public function __construct(private readonly AssignmentService $service, private readonly PrivateFiles $files) {}

    public function show(Request $request, Assignment $assignment): View
    {
        $this->authorizeStudent($request, $assignment);
        $submission = Submission::where('assignment_id', $assignment->id)->where('student_id', $request->user()->id)->first();
        $assignment->load('classroom.teacher', 'questions');

        // Quiz review after submission: answers only if the teacher allows it.
        $review = null;
        if ($assignment->isQuiz() && $submission?->isSubmitted()) {
            $revisions = QuestionRevision::whereIn('id', $assignment->questions->pluck('pivot.question_revision_id'))->get()->keyBy('id');
            $keys = $assignment->show_answers ? QuestionKey::whereIn('question_revision_id', $revisions->keys())->pluck('key', 'question_revision_id') : collect();
            $review = compact('revisions', 'keys');
        }

        return view('learn.assignments.show', compact('assignment', 'submission', 'review'));
    }

    /** Opens the quiz paper. The timer starts the first time a student opens it. */
    public function take(Request $request, Assignment $assignment): View|RedirectResponse
    {
        $this->authorizeStudent($request, $assignment);
        abort_unless($assignment->isQuiz(), 404);
        if (! $assignment->isOpen()) {
            return redirect()->route('learn.assignments.show', $assignment)->with('error', 'This quiz is closed.');
        }

        $submission = $this->service->startQuiz($assignment, $request->user());
        if ($submission->isSubmitted()) {
            return redirect()->route('learn.assignments.show', $assignment);
        }

        $secondsLeft = $this->service->secondsLeft($assignment, $submission);
        if ($secondsLeft === 0) {
            // Time ran out without a submission (closed tab, lost connection): mark what we have.
            $this->service->submitQuiz($assignment, $submission, []);

            return redirect()->route('learn.assignments.show', $assignment)->with('error', 'Time was up for this quiz.');
        }

        $revisions = QuestionRevision::whereIn('id', $assignment->questions()->get()->pluck('pivot.question_revision_id'))->get()->keyBy('id');

        return view('learn.assignments.take', [
            'assignment' => $assignment->load('questions', 'classroom'),
            'revisions' => $revisions,
            'submission' => $submission,
            'secondsLeft' => $secondsLeft,
        ]);
    }

    /** The quiz page reports each time the student switches away from it (another tab or app). */
    public function left(Request $request, Assignment $assignment): Response
    {
        $this->authorizeStudent($request, $assignment);
        abort_unless($assignment->isQuiz(), 404);

        Submission::where('assignment_id', $assignment->id)->where('student_id', $request->user()->id)
            ->whereNull('submitted_at')->where('tab_leaves', '<', 1000)
            ->increment('tab_leaves');

        return response()->noContent();
    }

    public function submitQuiz(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeStudent($request, $assignment);
        abort_unless($assignment->isQuiz(), 404);
        $submission = Submission::where('assignment_id', $assignment->id)->where('student_id', $request->user()->id)->firstOrFail();
        $answers = $request->validate(['answers' => ['array'], 'answers.*' => ['nullable', 'string', 'max:2']])['answers'] ?? [];

        $submission = $this->service->submitQuiz($assignment, $submission, $answers);

        return redirect()->route('learn.assignments.show', $assignment)->with('success', 'Submitted. You scored '.(float) $submission->score.' out of '.(float) $submission->max_score.'.');
    }

    public function submitWork(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeStudent($request, $assignment);
        abort_if($assignment->isQuiz(), 404);
        if (! $assignment->isOpen()) {
            return back()->with('error', 'This assignment is closed.');
        }

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:20000', 'required_without:file'],
            'file' => ['nullable', 'file', 'max:'.(config('platform.library.max_upload_mb') * 1024)],
        ], ['body.required_without' => 'Type your answer or attach a file.']);

        $existing = Submission::where('assignment_id', $assignment->id)->where('student_id', $request->user()->id)->first();
        $file = $request->hasFile('file') ? $this->files->store($request->file('file'), 'classrooms/submissions') : null;
        $this->service->submitWork($assignment, $request->user(), ['body' => $data['body'] ?? null, 'file' => $file]);
        if ($file && $existing?->file_path) {
            $this->files->delete($existing->file_path);
        }

        return back()->with('success', $assignment->isOverdue() ? 'Handed in (late).' : 'Handed in. You can change it until your teacher marks it.');
    }

    public function handout(Request $request, Assignment $assignment): StreamedResponse
    {
        $this->authorizeStudent($request, $assignment);
        abort_unless($assignment->file_path, 404);

        return $this->files->download($assignment->file_path, $assignment->file_name, $assignment->file_mime);
    }

    public function myFile(Request $request, Assignment $assignment): StreamedResponse
    {
        $this->authorizeStudent($request, $assignment);
        $submission = Submission::where('assignment_id', $assignment->id)->where('student_id', $request->user()->id)->whereNotNull('file_path')->firstOrFail();

        return $this->files->download($submission->file_path, $submission->file_name, $submission->file_mime);
    }

    private function authorizeStudent(Request $request, Assignment $assignment): void
    {
        abort_unless($assignment->isPublished() && $assignment->classroom->hasStudent($request->user()), 404);
    }
}
