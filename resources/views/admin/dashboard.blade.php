@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', now()->format('l, j F Y'))

@section('content')
@php
    $hour = now()->hour;
    $greeting = ($hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening')).', '.(\Illuminate\Support\Str::before(auth()->user()->name, ' ') ?: auth()->user()->name);
    $me = auth()->user();
    // [colour, icon, title, caption, value, badge, link] in jhs_portal's two-tone tile style.
    $tiles = [
        ['blue', 'fa-user-graduate', 'Students', '+'.$stats['newStudentsWeek'].' this week', number_format($stats['students']), null, $me->can('users.view') ? route('admin.students.index') : null],
        ['purple', 'fa-chalkboard-user', 'Teachers', $stats['pendingTeachers'].' awaiting approval', number_format($stats['teachers']), $stats['pendingTeachers'] ? 'Review' : null, $me->can('users.view') ? route('admin.teachers.index', $stats['pendingTeachers'] ? ['verification' => 'pending'] : []) : null],
        ['slate', 'fa-user-shield', 'Staff accounts', 'Admins and officers', number_format($stats['staff']), null, $me->can('staff.manage') ? route('admin.staff.index') : null],
        [$sms['failed'] > 0 ? 'red' : 'orange', 'fa-comment-sms', 'SMS (24 h)', $sms['failed'].' failed · '.$sms['queued'].' queued', number_format($sms['sent']), null, null],
    ];
    if ($commerce) {
        $tiles[] = ['green', 'fa-crown', 'Premium subscribers', 'With access right now', number_format($commerce['subscribers']), null, route('admin.commerce.subscriptions.index')];
        $tiles[] = ['blue', 'fa-sack-dollar', 'Revenue (30 days)', 'Confirmed payments', \App\Support\Money::format($commerce['revenue30']), null, route('admin.commerce.payments.index', ['status' => 'success'])];
        $tiles[] = [$commerce['pending'] ? 'orange' : 'slate', 'fa-hourglass-half', 'Payments waiting', 'Over 30 min; re-checked every 10', (string) $commerce['pending'], null, route('admin.commerce.payments.index', ['status' => 'pending'])];
    }
@endphp

<div class="j-hero mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
        <div>
            <p class="eyebrow">{{ now()->format('l, j F Y') }}</p>
            <h2>{{ $greeting }}</h2>
            <p class="sub">Here is what is happening on {{ config('app.name') }} today.</p>
            <div class="chips">
                <span><i class="fas fa-user-graduate" aria-hidden="true"></i> {{ number_format($stats['students']) }} students</span>
                <span><i class="fas fa-chalkboard-user" aria-hidden="true"></i> {{ number_format($stats['teachers']) }} teachers</span>
                @if ($commerce)<span><i class="fas fa-crown" aria-hidden="true"></i> {{ number_format($commerce['subscribers']) }} premium</span>@endif
            </div>
        </div>
        <div class="actions d-flex flex-wrap gap-2 align-items-start">
            @can('resources.manage')<a class="primary" href="{{ route('admin.library.resources.create') }}"><i class="fas fa-upload" style="color:#ffaf1b" aria-hidden="true"></i> Add resource</a>@endcan
            @can('teachers.verify')<a href="{{ route('admin.teachers.index', ['verification' => 'pending']) }}"><i class="fas fa-user-check" aria-hidden="true"></i> Approve teachers</a>@endcan
            @can('payments.view')<a href="{{ route('admin.commerce.payments.index') }}"><i class="fas fa-receipt" aria-hidden="true"></i> Payments</a>@endcan
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach ($tiles as [$colour, $icon, $title, $caption, $value, $badge, $href])
        <div class="col-md-6 col-xl-3">
            @if ($href)<a href="{{ $href }}" class="j-tile j-{{ $colour }}">@else<div class="j-tile j-{{ $colour }}">@endif
                <span class="ic"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                <span class="bd">
                    <span style="min-width:0"><span class="t">{{ $title }}</span><span class="s">{{ $caption }}</span></span>
                    <span class="text-end"><span class="v d-block">{{ $value }}</span>@if ($badge)<span class="b">{{ $badge }}</span>@endif</span>
                </span>
            @if ($href)</a>@else</div>@endif
        </div>
    @endforeach
</div>

<div class="row g-3">
    @can('teachers.verify')
        <div class="col-lg-6">
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 fw-bold mb-0">Teachers waiting for approval</h2>
                    <a href="{{ route('admin.teachers.index', ['verification' => 'pending']) }}" class="small fw-semibold">View all</a>
                </div>
                @forelse ($teacherQueue as $teacher)
                    <a href="{{ route('admin.teachers.show', $teacher) }}" class="report-item text-decoration-none">
                        <div>
                            <div class="fw-semibold text-dark">{{ $teacher->name }}</div>
                            <div class="small text-muted">{{ collect([$teacher->teacherProfile?->school_name, $teacher->teacherProfile?->region])->filter()->implode(' · ') ?: 'Profile incomplete' }} · registered {{ $teacher->created_at->diffForHumans() }}</div>
                        </div>
                        <i class="fa-solid fa-chevron-right report-arrow" aria-hidden="true"></i>
                    </a>
                @empty
                    <div class="empty-state-container py-4">
                        <p class="empty-text mb-0">No teachers are waiting for approval.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endcan

    @can('audit.view')
        <div class="col-lg-6">
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 fw-bold mb-0">Recent activity</h2>
                    <a href="{{ route('admin.audit.index') }}" class="small fw-semibold">Audit log</a>
                </div>
                @forelse ($recentActivity as $log)
                    <div class="py-2 border-bottom small">
                        <div class="text-dark">{{ $log->description }}</div>
                        <div class="text-muted">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Nothing recorded yet.</p>
                @endforelse
            </div>
        </div>
    @endcan
</div>

<p class="small text-muted mt-4 mb-0">Exam and content figures will appear here as those modules go live.</p>
@endsection
