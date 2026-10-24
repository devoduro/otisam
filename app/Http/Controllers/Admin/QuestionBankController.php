<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\PracticeController;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Read-only browser for the question bank: filter, count and open single questions. */
class QuestionBankController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'integer'],
            'type' => ['nullable', 'in:'.implode(',', Question::PRACTICE_TYPES)],
            'status' => ['nullable', 'in:'.implode(',', array_keys(Question::STATUSES))],
            'use' => ['nullable', 'in:'.implode(',', array_keys(Question::CONFIDENTIALITY))],
        ]);

        $questions = Question::with(['subject.educationLevel', 'currentRevision'])
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->whereHas('currentRevision', fn ($r) => $r->where('stem', 'like', "%{$s}%")))
            ->when($filters['subject'] ?? null, fn ($q, $id) => $q->where('subject_id', $id))
            ->when($filters['type'] ?? null, fn ($q, $t) => $q->where('type', $t))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['use'] ?? null, fn ($q, $u) => $q->where('confidentiality', $u))
            ->latest('id')
            ->paginate(25)->withQueryString();

        $subjectIds = Question::distinct()->pluck('subject_id');

        return view('admin.questions.index', [
            'questions' => $questions,
            'filters' => $filters,
            'subjects' => Subject::with('educationLevel')->whereIn('id', $subjectIds)->orderBy('education_level_id')->orderBy('name')->get(),
            'typeLabels' => PracticeController::TYPE_LABELS,
        ]);
    }

    public function show(Request $request, Question $question): View
    {
        $question->load(['subject.educationLevel', 'currentRevision', 'curriculumNodes', 'author']);
        $key = $request->user()->can('questions.view-keys') && $question->current_revision_id
            ? QuestionKey::where('question_revision_id', $question->current_revision_id)->value('key')
            : null;

        return view('admin.questions.show', [
            'question' => $question,
            'key' => $key,
            'typeLabels' => PracticeController::TYPE_LABELS,
        ]);
    }
}
