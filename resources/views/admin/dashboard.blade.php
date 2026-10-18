@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Welcome back, ' . auth()->user()->name)

@section('content')
<div class="row g-3 mb-4">
    @foreach ([
        ['Students', $stats['students'], '+' . $stats['newStudentsWeek'] . ' this week', 'fa-user-graduate', 'bg-icon-blue'],
        ['Teachers', $stats['teachers'], $stats['pendingTeachers'] . ' awaiting approval', 'fa-chalkboard-user', 'bg-icon-purple'],
        ['Staff accounts', $stats['staff'], 'Admins and officers', 'fa-user-shield', 'bg-icon-green'],
        ['SMS (24 h)', $sms['sent'], $sms['failed'] . ' failed · ' . $sms['queued'] . ' queued', 'fa-comment-sms', $sms['failed'] > 0 ? 'bg-icon-red' : 'bg-icon-orange'],
    ] as [$label, $value, $hint, $icon, $tone])
        <div class="col-6 col-xl-3">
            <div class="stat-tile d-flex gap-3 align-items-start">
                <div class="kpi-icon-wrapper {{ $tone }}"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></div>
                <div>
                    <div class="stat-label">{{ $label }}</div>
                    <div class="stat-value">{{ number_format($value) }}</div>
                    <div class="stat-hint">{{ $hint }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if ($commerce)
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><div class="stat-tile"><div class="stat-label">Premium subscribers</div><div class="stat-value">{{ number_format($commerce['subscribers']) }}</div><div class="stat-hint">with access right now</div></div></div>
        <div class="col-6 col-xl-3"><div class="stat-tile"><div class="stat-label">Revenue (30 days)</div><div class="stat-value">{{ \App\Support\Money::format($commerce['revenue30']) }}</div><div class="stat-hint"><a href="{{ route('admin.commerce.payments.index', ['status' => 'success']) }}">Payments</a></div></div></div>
        <div class="col-6 col-xl-3"><div class="stat-tile"><div class="stat-label">Payments waiting &gt; 30 min</div><div class="stat-value {{ $commerce['pending'] ? 'text-warning' : '' }}">{{ $commerce['pending'] }}</div><div class="stat-hint">re-checked every 10 minutes</div></div></div>
    </div>
@endif

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
                            <div class="small text-muted">{{ $teacher->teacherProfile->school_name }} · {{ $teacher->teacherProfile->region }} · registered {{ $teacher->created_at->diffForHumans() }}</div>
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
