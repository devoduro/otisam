<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountType;
use App\Enums\TeacherVerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Commerce\Payment;
use App\Models\Commerce\Subscription;
use App\Models\Community\Report;
use App\Models\LessonPlans\LessonPlan;
use App\Models\Library\Resource;
use App\Models\NotificationMessage;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuizAttempt;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\Videos\VideoLesson;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $me = auth()->user();
        $counts = User::query()
            ->selectRaw('account_type, COUNT(*) as total')
            ->groupBy('account_type')
            ->pluck('total', 'account_type');

        $pendingTeachers = TeacherProfile::where('verification_status', TeacherVerificationStatus::Pending->value)->count();

        $since = now()->subDay();
        $sms = NotificationMessage::where('channel', NotificationMessage::CHANNEL_SMS)->where('created_at', '>=', $since)
            ->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.dashboard', [
            'stats' => [
                'students' => (int) ($counts[AccountType::Student->value] ?? 0),
                'teachers' => (int) ($counts[AccountType::Teacher->value] ?? 0),
                'staff' => (int) ($counts[AccountType::Staff->value] ?? 0),
                'pendingTeachers' => $pendingTeachers,
                'newStudentsWeek' => User::where('account_type', AccountType::Student->value)->where('created_at', '>=', now()->subWeek())->count(),
            ],
            'commerce' => $me->can('payments.view') ? [
                'subscribers' => Subscription::current()->distinct('user_id')->count('user_id'),
                'revenue30' => (int) Payment::where('status', 'success')->where('paid_at', '>=', now()->subDays(30))->sum('amount_minor'),
                'pending' => Payment::whereIn('status', ['initiated', 'pending'])->where('provider', 'paystack')->where('created_at', '<', now()->subMinutes(30))->where('created_at', '>', now()->subDays(2))->count(),
            ] : null,
            'sms' => ['sent' => (int) ($sms['sent'] ?? 0) + (int) ($sms['delivered'] ?? 0), 'failed' => (int) ($sms['failed'] ?? 0), 'queued' => (int) ($sms['queued'] ?? 0)],
            'content' => $this->content(),
            'practice' => $this->practice(),
            'signups' => $this->signups(),
            'teacherQueue' => $me->can('teachers.verify')
                ? User::where('account_type', AccountType::Teacher->value)
                    ->whereHas('teacherProfile', fn ($q) => $q->where('verification_status', TeacherVerificationStatus::Pending->value))
                    ->with('teacherProfile')->oldest()->limit(5)->get()
                : collect(),
            'recentActivity' => $me->can('audit.view')
                ? AuditLog::with('user')->latest('id')->limit(8)->get()
                : collect(),
        ]);
    }

    /** Library, video, lesson-plan, question bank and Q&A figures. */
    private function content(): array
    {
        $statusCounts = fn (string $model) => $model::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $questions = Question::where('questions.status', 'published')->where('questions.confidentiality', 'practice');

        return [
            'videos' => $statusCounts(VideoLesson::class),
            'resources' => $statusCounts(Resource::class),
            'lessonPlans' => $statusCounts(LessonPlan::class),
            'questionTotal' => (clone $questions)->count(),
            'questionTypes' => (clone $questions)->selectRaw('questions.type as type, COUNT(*) as total')->groupBy('type')->pluck('total', 'type'),
            'topSubjects' => (clone $questions)->join('subjects', 'subjects.id', '=', 'questions.subject_id')
                ->leftJoin('education_levels', 'education_levels.id', '=', 'subjects.education_level_id')
                ->selectRaw("subjects.name as name, COALESCE(education_levels.code, '') as level, COUNT(*) as total")
                ->groupBy('subjects.id', 'subjects.name', 'education_levels.code')->orderByDesc('total')->limit(8)->get(),
            'openReports' => Report::openPostCount(),
        ];
    }

    /** Practice and paper attempts over the last 7 days. */
    private function practice(): array
    {
        $week = QuizAttempt::where('quiz_attempts.created_at', '>=', now()->subWeek());

        return [
            'attemptsWeek' => (clone $week)->count(),
            'learnersWeek' => (clone $week)->distinct('user_id')->count('user_id'),
            'averageWeek' => (int) round((float) (clone $week)->where('total', '>', 0)->selectRaw('AVG(score * 100.0 / total) as avg')->value('avg')),
            'popular' => (clone $week)->join('subjects', 'subjects.id', '=', 'quiz_attempts.subject_id')
                ->selectRaw('subjects.name as name, COUNT(*) as total')
                ->groupBy('subjects.id', 'subjects.name')->orderByDesc('total')->limit(5)->get(),
        ];
    }

    /** New student and teacher sign-ups for each of the last 14 days. */
    private function signups(): array
    {
        $from = now()->subDays(13)->startOfDay();
        $rows = User::where('created_at', '>=', $from)
            ->whereIn('account_type', [AccountType::Student->value, AccountType::Teacher->value])
            ->selectRaw('DATE(created_at) as day, account_type, COUNT(*) as total')
            ->groupBy('day', 'account_type')->toBase()->get();

        $days = [];
        for ($d = $from->copy(); $d->lte(now()); $d->addDay()) {
            $key = $d->toDateString();
            $days[$key] = [
                'label' => $d->format('j M'),
                'students' => (int) $rows->where('day', $key)->where('account_type', AccountType::Student->value)->sum('total'),
                'teachers' => (int) $rows->where('day', $key)->where('account_type', AccountType::Teacher->value)->sum('total'),
            ];
        }

        return array_values($days);
    }
}
