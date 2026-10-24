@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', now()->format('l, j F Y'))

@section('content')
@php
    $hour = now()->hour;
    $greeting = ($hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening')).', '.(\Illuminate\Support\Str::before(auth()->user()->name, ' ') ?: auth()->user()->name);
    $me = auth()->user();
    $typeLabels = ['mcq' => 'Multiple choice', 'true_false' => 'True or false', 'fill_in' => 'Fill in the blank', 'theory' => 'Theory'];
    $statusLabel = fn ($counts, $labels) => collect($labels)->mapWithKeys(fn ($label, $key) => [$label => (int) ($counts[$key] ?? 0)]);

    // Each card links to its list page, where every row opens the full record. A card the
    // viewer's role cannot open is shown without a link.
    // [colour, icon, title, caption, value, badge, link]
    $tiles = [
        ['blue', 'fa-user-graduate', 'Students', '+'.$stats['newStudentsWeek'].' this week', number_format($stats['students']), null,
            $me->can('users.view') ? route('admin.students.index') : null],
        ['purple', 'fa-chalkboard-user', 'Teachers', $stats['pendingTeachers'].' awaiting approval', number_format($stats['teachers']), $stats['pendingTeachers'] ? 'Review' : null,
            $me->can('users.view') ? route('admin.teachers.index', $stats['pendingTeachers'] ? ['verification' => 'pending'] : []) : null],
        ['slate', 'fa-user-shield', 'Staff accounts', 'Admins and officers', number_format($stats['staff']), null,
            $me->can('staff.manage') ? route('admin.staff.index') : null],
        [$sms['failed'] > 0 ? 'red' : 'orange', 'fa-comment-sms', 'SMS (24 h)', $sms['failed'].' failed · '.$sms['queued'].' queued', number_format($sms['sent']), null,
            $me->can('notifications.view-logs') ? route('admin.messages.index', array_filter(['channel' => 'sms', 'status' => $sms['failed'] > 0 ? 'failed' : null])) : null],
    ];
    if ($commerce) {
        $tiles[] = ['green', 'fa-crown', 'Premium subscribers', 'With access right now', number_format($commerce['subscribers']), null, route('admin.commerce.subscriptions.index')];
        $tiles[] = ['blue', 'fa-sack-dollar', 'Revenue (30 days)', 'Confirmed payments', \App\Support\Money::format($commerce['revenue30']), null, route('admin.commerce.payments.index', ['status' => 'success'])];
        $tiles[] = [$commerce['pending'] ? 'orange' : 'slate', 'fa-hourglass-half', 'Payments waiting', 'Over 30 min; re-checked every 10', (string) $commerce['pending'], null, route('admin.commerce.payments.index', ['status' => 'pending'])];
    }

    $canTutorials = $me->can('tutorials.manage');
    $canLibrary = $me->canany(['resources.manage', 'resources.publish', 'resources.moderate']);
    // [icon, title, value, caption, link]
    $contentTiles = [
        ['fa-circle-play', 'Video lessons', (int) ($content['videos']['published'] ?? 0), ((int) ($content['videos']['pending'] ?? 0)).' awaiting review',
            $canTutorials ? route('admin.videos.index') : null],
        ['fa-list-check', 'Practice questions', $content['questionTotal'], ((int) ($content['questionTypes']['theory'] ?? 0)).' theory questions',
            $me->canany(['questions.manage', 'questions.review', 'questions.view-keys']) ? route('admin.questions.index', ['use' => 'practice']) : null],
        ['fa-book-open', 'Library resources', (int) ($content['resources']['published'] ?? 0), ((int) ($content['resources']['in_review'] ?? 0)).' in review',
            $canLibrary ? route('admin.library.resources.index') : null],
        ['fa-clipboard-list', 'Lesson plans', (int) ($content['lessonPlans']['published'] ?? 0), ((int) ($content['lessonPlans']['pending'] ?? 0)).' awaiting review',
            $canTutorials ? route('admin.lesson-plans.index') : null],
        ['fa-flag', 'Q&A reports', $content['openReports'], 'Posts with open reports',
            $canTutorials ? route('admin.qa.index') : null],
        ['fa-chart-line', 'Practice this week', $practice['attemptsWeek'], $practice['learnersWeek'].' learners · '.$practice['averageWeek'].'% average',
            $me->canany(['reports.view', 'results.view-all']) ? route('admin.attempts.index', ['days' => 7]) : null],
    ];
    $signupMax = max(1, collect($signups)->max(fn ($d) => $d['students'] + $d['teachers']));
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
                <span><i class="fas fa-list-check" aria-hidden="true"></i> {{ number_format($content['questionTotal']) }} practice questions</span>
                <span><i class="fas fa-circle-play" aria-hidden="true"></i> {{ number_format((int) ($content['videos']['published'] ?? 0)) }} videos</span>
                @if ($commerce)<span><i class="fas fa-crown" aria-hidden="true"></i> {{ number_format($commerce['subscribers']) }} premium</span>@endif
            </div>
        </div>
        <div class="actions d-flex flex-wrap gap-2 align-items-start">
            @can('resources.manage')<a class="primary" href="{{ route('admin.library.resources.create') }}"><i class="fas fa-upload" style="color:#14031f" aria-hidden="true"></i> Add resource</a>@endcan
            @can('tutorials.manage')<a href="{{ route('admin.videos.create') }}"><i class="fas fa-circle-play" aria-hidden="true"></i> Add video</a>@endcan
            @can('teachers.verify')<a href="{{ route('admin.teachers.index', ['verification' => 'pending']) }}"><i class="fas fa-user-check" aria-hidden="true"></i> Approve teachers</a>@endcan
            @can('payments.view')<a href="{{ route('admin.commerce.payments.index') }}"><i class="fas fa-receipt" aria-hidden="true"></i> Payments</a>@endcan
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach ($tiles as [$colour, $icon, $title, $caption, $value, $badge, $href])
        <div class="col-md-6 col-xl-3">
            @if ($href)<a href="{{ $href }}" class="j-tile j-{{ $colour }}">@else<div class="j-tile j-{{ $colour }}" title="Your role cannot open this list">@endif
                <span class="ic"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                <span class="bd">
                    <span style="min-width:0"><span class="t">{{ $title }}</span><span class="s">{{ $caption }}</span></span>
                    <span class="text-end"><span class="v d-block">{{ $value }}</span>@if ($badge)<span class="b">{{ $badge }}</span>@elseif ($href)<span class="more">View list <i class="fas fa-arrow-right" aria-hidden="true"></i></span>@endif</span>
                </span>
            @if ($href)</a>@else</div>@endif
        </div>
    @endforeach
</div>

<div class="d-flex justify-content-between align-items-end mb-2">
    <h2 class="h6 fw-bold mb-0 dash-section">Content and learning</h2>
    <span class="small text-muted">Click a card to open the list</span>
</div>
<div class="row g-3 mb-4">
    @foreach ($contentTiles as [$icon, $title, $value, $caption, $href])
        <div class="col-sm-6 col-xl-4 col-xxl-2">
            @if ($href)<a href="{{ $href }}" class="dash-stat text-decoration-none">@else<div class="dash-stat is-static" title="Your role cannot open this list">@endif
                <span class="dash-stat-ic"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                <span class="dash-stat-v">{{ number_format($value) }}</span>
                <span class="dash-stat-t">{{ $title }}</span>
                <span class="dash-stat-s">{{ $caption }}</span>
            @if ($href)</a>@else</div>@endif
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-7">
        <div class="card-panel h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="h6 fw-bold mb-0">Sign-ups in the last 14 days</h2>
                <span class="small d-flex gap-3"><span><i class="fas fa-square" style="color:#3a52b8" aria-hidden="true"></i> Students</span><span><i class="fas fa-square" style="color:#ffaf1b" aria-hidden="true"></i> Teachers</span></span>
            </div>
            <div class="dash-chart" role="img" aria-label="Daily sign-ups: {{ collect($signups)->map(fn ($d) => $d['label'].' '.$d['students'].' students, '.$d['teachers'].' teachers')->implode('; ') }}">
                @foreach ($signups as $day)
                    <div class="dash-col" title="{{ $day['label'] }}: {{ $day['students'] }} students, {{ $day['teachers'] }} teachers">
                        <div class="dash-col-bars">
                            <span class="t" style="height: {{ $day['teachers'] / $signupMax * 100 }}%"></span>
                            <span class="s" style="height: {{ $day['students'] / $signupMax * 100 }}%"></span>
                        </div>
                        <span class="dash-col-label">{{ $loop->index % 2 === 0 ? $day['label'] : '' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card-panel h-100">
            <h2 class="h6 fw-bold mb-3">Practice question bank</h2>
            @include('admin.partials.breakdown', ['heading' => null, 'items' => $statusLabel($content['questionTypes'], $typeLabels), 'empty' => 'No published practice questions yet.'])
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card-panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 fw-bold mb-0">Largest subject banks</h2>
                <a href="{{ route('practice.index') }}" class="small fw-semibold">Practice page</a>
            </div>
            @include('admin.partials.breakdown', ['heading' => null, 'items' => $content['topSubjects']->mapWithKeys(fn ($s) => [$s->name.($s->level ? ' · '.$s->level : '') => $s->total])])
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-panel h-100">
            <h2 class="h6 fw-bold mb-3">Most practised this week</h2>
            @include('admin.partials.breakdown', ['heading' => null, 'items' => $practice['popular']->pluck('total', 'name'), 'empty' => 'No practice attempts in the last 7 days.'])
        </div>
    </div>
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
@endsection
