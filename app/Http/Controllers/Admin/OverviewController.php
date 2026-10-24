<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Models\NotificationMessage;
use App\Models\QuestionBank\QuizAttempt;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Read-only overview pages behind the dashboard cards (students, teachers, staff, SMS).
 *
 * Every staff member can open them, so they show totals, breakdowns and the latest entries only,
 * with names and phone numbers shortened. Staff who may use the full lists (users.view,
 * staff.manage, notifications.view-logs) get the complete names and a link through.
 */
class OverviewController extends Controller
{
    public function students(): View
    {
        $full = auth()->user()->can('users.view');
        $byLevel = StudentProfile::query()
            ->leftJoin('class_levels', 'class_levels.id', '=', 'student_profiles.class_level_id')
            ->selectRaw("COALESCE(class_levels.name, 'Not set') as label, COUNT(*) as total")
            ->groupBy('label')->orderByDesc('total')->limit(10)->pluck('total', 'label');
        $byRegion = StudentProfile::query()
            ->selectRaw("COALESCE(NULLIF(region, ''), 'Not set') as label, COUNT(*) as total")
            ->groupBy('label')->orderByDesc('total')->limit(10)->pluck('total', 'label');

        $recent = User::where('account_type', AccountType::Student->value)->latest('id')->limit(10)->get(['id', 'name', 'created_at']);

        return view('admin.overview.show', [
            'title' => 'Students',
            'icon' => 'fa-user-graduate',
            'colour' => 'blue',
            'tiles' => [
                ['Registered', User::where('account_type', AccountType::Student->value)->count()],
                ['Joined this week', User::where('account_type', AccountType::Student->value)->where('created_at', '>=', now()->subWeek())->count()],
                ['Practised this week', QuizAttempt::where('created_at', '>=', now()->subWeek())->distinct('user_id')->count('user_id')],
            ],
            'sections' => [['By class', $byLevel], ['By region', $byRegion]],
            'listTitle' => 'Newest students',
            'rows' => $recent->map(fn ($u) => [
                'id' => $u->id,
                'title' => $full ? $u->name : $this->shortName($u->name),
                'sub' => 'Joined '.$u->created_at->diffForHumans(),
                'href' => $full ? route('admin.students.show', $u) : null,
            ]),
            'fullHref' => $full ? route('admin.students.index') : null,
            'fullLabel' => 'Open the full student list',
        ]);
    }

    public function teachers(): View
    {
        $full = auth()->user()->can('users.view');
        $status = TeacherProfile::query()->selectRaw('verification_status as label, COUNT(*) as total')->groupBy('label')->pluck('total', 'label')
            ->mapWithKeys(fn ($total, $s) => [ucfirst(str_replace('_', ' ', (string) $s)) => $total]);
        $byRegion = TeacherProfile::query()
            ->selectRaw("COALESCE(NULLIF(region, ''), 'Not set') as label, COUNT(*) as total")
            ->groupBy('label')->orderByDesc('total')->limit(10)->pluck('total', 'label');

        $recent = User::where('account_type', AccountType::Teacher->value)->with('teacherProfile')->latest('id')->limit(10)->get();

        return view('admin.overview.show', [
            'title' => 'Teachers',
            'icon' => 'fa-chalkboard-user',
            'colour' => 'purple',
            'tiles' => [
                ['Registered', User::where('account_type', AccountType::Teacher->value)->count()],
                ['Awaiting approval', (int) ($status['Pending'] ?? 0)],
                ['Joined this week', User::where('account_type', AccountType::Teacher->value)->where('created_at', '>=', now()->subWeek())->count()],
            ],
            'sections' => [['By verification status', $status], ['By region', $byRegion]],
            'listTitle' => 'Newest teachers',
            'rows' => $recent->map(fn ($u) => [
                'id' => $u->id,
                'title' => $full ? $u->name : $this->shortName($u->name),
                'sub' => collect([$u->teacherProfile?->school_name, $u->teacherProfile?->region])->filter()->implode(' · ') ?: 'Profile incomplete',
                'href' => $full ? route('admin.teachers.show', $u) : null,
            ]),
            'fullHref' => $full ? route('admin.teachers.index') : null,
            'fullLabel' => 'Open the full teacher list',
        ]);
    }

    public function staff(): View
    {
        $full = auth()->user()->can('staff.manage');
        $roles = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('users', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.model_type', User::class)
            ->where('users.account_type', AccountType::Staff->value)
            ->selectRaw('roles.name as label, COUNT(*) as total')
            ->groupBy('roles.name')->orderByDesc('total')->pluck('total', 'label');

        $recent = User::where('account_type', AccountType::Staff->value)->with('roles')->latest('id')->limit(10)->get();

        return view('admin.overview.show', [
            'title' => 'Staff accounts',
            'icon' => 'fa-user-shield',
            'colour' => 'slate',
            'tiles' => [
                ['Staff accounts', User::where('account_type', AccountType::Staff->value)->count()],
                ['Roles in use', $roles->count()],
            ],
            'sections' => [['By role', $roles]],
            'listTitle' => 'Staff',
            'rows' => $recent->map(fn ($u) => [
                'id' => $u->id,
                'title' => $full ? $u->name : $this->shortName($u->name),
                'sub' => $u->roles->pluck('name')->implode(', ') ?: 'No role',
                'href' => $full ? route('admin.staff.edit', $u) : null,
            ]),
            'fullHref' => $full ? route('admin.staff.index') : null,
            'fullLabel' => 'Manage staff accounts',
        ]);
    }

    public function sms(): View
    {
        $full = auth()->user()->can('notifications.view-logs');
        $sms = NotificationMessage::where('channel', NotificationMessage::CHANNEL_SMS);
        $day = (clone $sms)->where('created_at', '>=', now()->subDay());
        $dayStatus = (clone $day)->selectRaw('status as label, COUNT(*) as total')->groupBy('label')->pluck('total', 'label')
            ->mapWithKeys(fn ($total, $s) => [ucfirst((string) $s) => $total]);
        $weekStatus = (clone $sms)->where('created_at', '>=', now()->subWeek())->selectRaw('status as label, COUNT(*) as total')->groupBy('label')->pluck('total', 'label')
            ->mapWithKeys(fn ($total, $s) => [ucfirst((string) $s) => $total]);
        $events = (clone $sms)->where('created_at', '>=', now()->subWeek())->selectRaw('event_key as label, COUNT(*) as total')
            ->groupBy('label')->orderByDesc('total')->limit(8)->pluck('total', 'label');

        $recent = (clone $sms)->latest('id')->limit(10)->get(['id', 'recipient', 'event_key', 'status', 'created_at']);

        return view('admin.overview.show', [
            'title' => 'SMS (24 h)',
            'icon' => 'fa-comment-sms',
            'colour' => 'orange',
            'tiles' => [
                ['Sent or delivered', (int) ($dayStatus['Sent'] ?? 0) + (int) ($dayStatus['Delivered'] ?? 0)],
                ['Failed', (int) ($dayStatus['Failed'] ?? 0)],
                ['Queued', (int) ($dayStatus['Queued'] ?? 0)],
            ],
            'sections' => [['Last 24 hours by status', $dayStatus], ['Last 7 days by status', $weekStatus], ['Last 7 days by message type', $events]],
            'listTitle' => 'Latest messages',
            'rows' => $recent->map(fn ($m) => [
                'id' => $m->id,
                'title' => $full ? $m->recipient : $this->shortPhone($m->recipient),
                'sub' => $m->event_key.' · '.ucfirst($m->status).' · '.$m->created_at->diffForHumans(),
                'href' => $full ? route('admin.messages.show', $m) : null,
            ]),
            'fullHref' => $full ? route('admin.messages.index', ['channel' => 'sms']) : null,
            'fullLabel' => 'Open the message log',
        ]);
    }

    /** "Ama Owusu" => "Ama O." */
    private function shortName(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [$name];

        return count($parts) > 1 ? $parts[0].' '.mb_substr((string) end($parts), 0, 1).'.' : $parts[0];
    }

    /** Keeps only the last three characters of a phone number or email. */
    private function shortPhone(string $value): string
    {
        return str_contains($value, '@') ? '••••'.mb_substr($value, mb_strpos($value, '@')) : str_repeat('•', max(0, mb_strlen($value) - 3)).mb_substr($value, -3);
    }
}
