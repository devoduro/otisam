<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Models\Curriculum\ClassLevel;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuizAttempt;
use App\Models\QuestionBank\QuizAttemptAnswer;
use App\Models\User;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,suspended,deactivated'],
            'class_level_id' => ['nullable', 'integer'],
            'verified' => ['nullable', 'in:yes,no'],
        ]);

        $students = User::query()
            ->where('account_type', AccountType::Student->value)
            ->with('studentProfile.classLevel', 'studentProfile.programme')
            ->when($filters['q'] ?? null, function ($query, $q) {
                $phone = Phone::clean($q);
                $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")
                    ->when($phone && strlen($phone) >= 4, fn ($p) => $p->orWhere('phone', 'like', "%{$phone}%")));
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['class_level_id'] ?? null, fn ($query, $level) => $query->whereHas('studentProfile', fn ($p) => $p->where('class_level_id', $level)))
            ->when(($filters['verified'] ?? null) === 'yes', fn ($query) => $query->whereNotNull('phone_verified_at'))
            ->when(($filters['verified'] ?? null) === 'no', fn ($query) => $query->whereNull('phone_verified_at'))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $classLevels = ClassLevel::with('educationLevel')->orderBy('education_level_id')->orderBy('sort')->get();

        return view('admin.students.index', compact('students', 'filters', 'classLevels'));
    }

    public function show(Request $request, User $student): View
    {
        abort_unless($student->isStudent(), 404);

        $student->load('studentProfile.classLevel', 'studentProfile.programme');

        $attempts = QuizAttempt::where('user_id', $student->id);
        $all = (clone $attempts)->with('subject.educationLevel')->get();
        $pct = fn ($a) => $a->total ? $a->score / $a->total * 100 : 0;

        $summary = [
            'attempts' => $all->count(),
            'practice' => $all->where('kind', 'practice')->count(),
            'papers' => $all->where('kind', 'paper')->count(),
            'questions' => (int) $all->sum('total'),
            'correct' => (int) $all->sum('score'),
            'average' => $all->isEmpty() ? 0 : (int) round($all->avg($pct)),
            'best' => $all->isEmpty() ? 0 : (int) round($all->max($pct)),
            'seconds' => (int) $all->sum('seconds'),
            'last' => $all->max('created_at'),
            'days' => $all->map(fn ($a) => $a->created_at->toDateString())->unique()->count(),
        ];

        $bySubject = $all->groupBy('subject_id')->map(function ($rows) use ($pct) {
            return [
                'subject' => $rows->first()->subject,
                'attempts' => $rows->count(),
                'questions' => (int) $rows->sum('total'),
                'average' => (int) round($rows->avg($pct)),
                'best' => (int) round($rows->max($pct)),
                'last' => $rows->max('created_at'),
            ];
        })->sortByDesc('attempts')->values();

        $trend = $all->sortBy('created_at')->take(-25)->values()->map(fn ($a) => [
            'label' => $a->created_at->format('j M'),
            'percent' => (int) round($pct($a)),
            'subject' => $a->subject?->name,
        ]);

        // Question by question: how often each was tried and how often it was right.
        $trials = QuizAttemptAnswer::query()
            ->join('quiz_attempts', 'quiz_attempts.id', '=', 'quiz_attempt_answers.quiz_attempt_id')
            ->where('quiz_attempts.user_id', $student->id)
            ->whereNotNull('quiz_attempt_answers.is_correct');
        $answered = (clone $trials)->count();
        $right = (clone $trials)->where('quiz_attempt_answers.is_correct', true)->count();

        $missed = (clone $trials)
            ->selectRaw('quiz_attempt_answers.question_id, COUNT(*) as tries, SUM(quiz_attempt_answers.is_correct) as rights, MAX(quiz_attempt_answers.created_at) as last_at')
            ->groupBy('quiz_attempt_answers.question_id')
            ->havingRaw('COUNT(*) > SUM(quiz_attempt_answers.is_correct)')
            ->orderByRaw('(COUNT(*) - SUM(quiz_attempt_answers.is_correct)) DESC')->orderByDesc('last_at')
            ->limit(15)->get();
        $missedQuestions = Question::with('currentRevision', 'subject')->whereIn('id', $missed->pluck('question_id'))->get()->keyBy('id');

        $latest = QuizAttemptAnswer::query()
            ->join('quiz_attempts', 'quiz_attempts.id', '=', 'quiz_attempt_answers.quiz_attempt_id')
            ->where('quiz_attempts.user_id', $student->id)
            ->select('quiz_attempt_answers.*')->with('question.currentRevision', 'question.subject')
            ->orderByDesc('quiz_attempt_answers.id')->paginate(20, pageName: 'answers')->withQueryString();

        return view('admin.students.show', [
            'student' => $student,
            'summary' => $summary,
            'bySubject' => $bySubject,
            'trend' => $trend,
            'attempts' => (clone $attempts)->with('subject.educationLevel', 'paper')->withCount('answers')->latest('id')->paginate(15, pageName: 'attempts')->withQueryString(),
            'answered' => $answered,
            'right' => $right,
            'missed' => $missed,
            'missedQuestions' => $missedQuestions,
            'latest' => $latest,
            'canKeys' => $request->user()->can('questions.view-keys'),
        ]);
    }
}
