<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Practice and paper attempts. Learner names are shown only to staff who may view student
 * accounts; others see a learner number.
 */
class PracticeAttemptController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'subject' => ['nullable', 'integer'],
            'kind' => ['nullable', 'in:practice,paper'],
            'days' => ['nullable', 'in:7,30,90'],
        ]);

        $attempts = QuizAttempt::with(['user', 'subject.educationLevel', 'paper'])
            ->when($filters['subject'] ?? null, fn ($q, $id) => $q->where('subject_id', $id))
            ->when($filters['kind'] ?? null, fn ($q, $k) => $q->where('kind', $k))
            ->when($filters['days'] ?? null, fn ($q, $d) => $q->where('created_at', '>=', now()->subDays((int) $d)))
            ->latest('id')
            ->paginate(30)->withQueryString();

        return view('admin.attempts.index', [
            'attempts' => $attempts,
            'filters' => $filters,
            'subjects' => Subject::with('educationLevel')->whereIn('id', QuizAttempt::distinct()->pluck('subject_id'))->orderBy('name')->get(),
            'showNames' => $request->user()->can('users.view'),
        ]);
    }

    public function show(Request $request, QuizAttempt $attempt): View
    {
        $attempt->load(['user', 'subject.educationLevel', 'paper']);

        return view('admin.attempts.show', [
            'attempt' => $attempt,
            'showNames' => $request->user()->can('users.view'),
            'others' => QuizAttempt::where('user_id', $attempt->user_id)->whereKeyNot($attempt->id)->with('subject')->latest('id')->limit(10)->get(),
        ]);
    }
}
