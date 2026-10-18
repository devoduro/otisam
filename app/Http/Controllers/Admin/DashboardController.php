<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountType;
use App\Enums\TeacherVerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\NotificationMessage;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
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
            'sms' => ['sent' => (int) ($sms['sent'] ?? 0) + (int) ($sms['delivered'] ?? 0), 'failed' => (int) ($sms['failed'] ?? 0), 'queued' => (int) ($sms['queued'] ?? 0)],
            'teacherQueue' => auth()->user()->can('teachers.verify')
                ? User::where('account_type', AccountType::Teacher->value)
                    ->whereHas('teacherProfile', fn ($q) => $q->where('verification_status', TeacherVerificationStatus::Pending->value))
                    ->with('teacherProfile')->oldest()->limit(5)->get()
                : collect(),
            'recentActivity' => auth()->user()->can('audit.view')
                ? AuditLog::with('user')->latest('id')->limit(8)->get()
                : collect(),
        ]);
    }
}
