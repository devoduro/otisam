@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', now()->format('l, j F Y'))

@section('content')
@php
    $hour = now()->hour;
    $greeting = ($hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening')).', '.(\Illuminate\Support\Str::before(auth()->user()->name, ' ') ?: auth()->user()->name);
    $me = auth()->user();
    $typeLabels = ['mcq' => 'Multiple choice', 'true_false' => 'True or false', 'fill_in' => 'Fill in the blank', 'theory' => 'Theory'];

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
    $canQuestions = $me->canany(['questions.manage', 'questions.review', 'questions.view-keys']);
    $contentTiles = [
        ['green', 'fa-circle-play', 'Video lessons', (int) ($content['videos']['published'] ?? 0), ((int) ($content['videos']['pending'] ?? 0)).' awaiting review',
            $canTutorials ? route('admin.videos.index') : null],
        ['blue', 'fa-list-check', 'Practice questions', $content['questionTotal'], ((int) ($content['questionTypes']['theory'] ?? 0)).' theory questions',
            $canQuestions ? route('admin.questions.index', ['use' => 'practice']) : null],
        ['purple', 'fa-book-open', 'Library resources', (int) ($content['resources']['published'] ?? 0), ((int) ($content['resources']['in_review'] ?? 0)).' in review',
            $canLibrary ? route('admin.library.resources.index') : null],
        ['orange', 'fa-clipboard-list', 'Lesson plans', (int) ($content['lessonPlans']['published'] ?? 0), ((int) ($content['lessonPlans']['pending'] ?? 0)).' awaiting review',
            $canTutorials ? route('admin.lesson-plans.index') : null],
        [$content['openReports'] ? 'red' : 'slate', 'fa-flag', 'Q&A reports', $content['openReports'], 'Posts with open reports',
            $canTutorials ? route('admin.qa.index') : null],
        ['slate', 'fa-chart-line', 'Practice this week', $practice['attemptsWeek'], $practice['learnersWeek'].' learners · '.$practice['averageWeek'].'% average',
            $me->canany(['reports.view', 'results.view-all']) ? route('admin.attempts.index', ['days' => 7]) : null],
    ];

    // Publishing progress: share of each content type that is live.
    $pipeline = [
        ['Video lessons', $content['videos'], $canTutorials ? route('admin.videos.index') : null],
        ['Library resources', $content['resources'], $canLibrary ? route('admin.library.resources.index') : null],
        ['Lesson plans', $content['lessonPlans'], $canTutorials ? route('admin.lesson-plans.index') : null],
    ];
    $palette = ['#42a5dc', '#8cc152', '#f5922f', '#967adc', '#5b6f8f', '#da4453', '#37bc9b', '#e8a838'];
    $typeChart = collect($typeLabels)->map(fn ($label, $key) => ['label' => $label, 'count' => (int) ($content['questionTypes'][$key] ?? 0)])->values();
    $subjectChart = $content['topSubjects']->map(fn ($s) => ['label' => $s->name.($s->level ? ' · '.$s->level : ''), 'count' => (int) $s->total])->values();
@endphp

<div class="d-grid gap-4">
    {{-- Welcome hero --}}
    <div class="j-hero">
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
                @can('resources.manage')<a class="primary" href="{{ route('admin.library.resources.create') }}"><i class="fas fa-upload" aria-hidden="true"></i> Add resource</a>@endcan
                @can('tutorials.manage')<a href="{{ route('admin.videos.create') }}"><i class="fas fa-circle-play" aria-hidden="true"></i> Add video</a>@endcan
                @can('teachers.verify')<a href="{{ route('admin.teachers.index', ['verification' => 'pending']) }}"><i class="fas fa-user-check" aria-hidden="true"></i> Approve teachers</a>@endcan
                @can('payments.view')<a href="{{ route('admin.commerce.payments.index') }}"><i class="fas fa-receipt" aria-hidden="true"></i> Payments</a>@endcan
            </div>
        </div>
    </div>

    {{-- Quick find --}}
    @can('users.view')
        <form method="GET" action="{{ route('admin.students.index') }}" class="j-card j-find">
            <label for="quick-find"><i class="fas fa-magnifying-glass" aria-hidden="true"></i> Find a student</label>
            <input id="quick-find" type="search" name="q" placeholder="Type a name, phone number or email and press Enter" autocomplete="off">
            <button type="submit">Search</button>
        </form>
    @endcan

    {{-- Metric tiles --}}
    <div class="j-tiles j-tiles-4">
        @foreach ($tiles as [$colour, $icon, $title, $caption, $value, $badge, $href])
            @if ($href)<a href="{{ $href }}" class="j-tile j-{{ $colour }}">@else<div class="j-tile j-{{ $colour }}" title="Your role cannot open this list">@endif
                <span class="ic"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                <span class="bd">
                    <span style="min-width:0"><span class="t">{{ $title }}</span><span class="s">{{ $caption }}</span></span>
                    <span class="text-end"><span class="v d-block">{{ $value }}</span>@if ($badge)<span class="b">{{ $badge }}</span>@endif</span>
                </span>
            @if ($href)</a>@else</div>@endif
        @endforeach
    </div>

    <div class="j-tiles j-tiles-3">
        @foreach ($contentTiles as [$colour, $icon, $title, $value, $caption, $href])
            @if ($href)<a href="{{ $href }}" class="j-tile j-{{ $colour }}">@else<div class="j-tile j-{{ $colour }}" title="Your role cannot open this list">@endif
                <span class="ic"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                <span class="bd">
                    <span style="min-width:0"><span class="t">{{ $title }}</span><span class="s">{{ $caption }}</span></span>
                    <span class="text-end"><span class="v d-block">{{ number_format($value) }}</span></span>
                </span>
            @if ($href)</a>@else</div>@endif
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="j-cols j-cols-23">
        <div class="j-card">
            <div class="j-card-head"><h4>Sign-ups in the last 14 days</h4></div>
            <div class="j-chart"><canvas id="signupChart" aria-label="Daily sign-ups of students and teachers"></canvas></div>
        </div>
        <div class="j-card">
            <div class="j-card-head"><h4>Practice question bank</h4>@if ($canQuestions)<a href="{{ route('admin.questions.index') }}">Browse <i class="fas fa-arrow-right"></i></a>@endif</div>
            @if ($typeChart->sum('count') === 0)
                <p class="j-empty">No published practice questions yet.</p>
            @else
                <div class="j-chart j-chart-sm"><canvas id="typeChart" aria-label="Practice questions by type"></canvas></div>
                <ul class="j-legend">
                    @foreach ($typeChart as $i => $row)
                        <li><span><i style="background: {{ $palette[$i % count($palette)] }}"></i>{{ $row['label'] }}</span><strong>{{ number_format($row['count']) }}</strong></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="j-cols j-cols-23">
        <div class="j-card">
            <div class="j-card-head"><h4>Largest subject question banks</h4></div>
            @if ($subjectChart->isEmpty())
                <p class="j-empty">No published practice questions yet.</p>
            @else
                <div class="j-chart"><canvas id="subjectChart" aria-label="Questions per subject"></canvas></div>
            @endif
        </div>
        <div class="j-card j-card-flush">
            <div class="j-card-head j-pad"><h4>Most practised this week</h4>@if ($me->canany(['reports.view', 'results.view-all']))<a href="{{ route('admin.attempts.index', ['days' => 7]) }}">All attempts</a>@endif</div>
            <div class="j-list">
                @forelse ($practice['popular'] as $i => $row)
                    <div class="j-row">
                        <span class="j-rank" style="background: {{ ['#f5922f', '#5b6f8f', '#967adc'][$i] ?? '#42a5dc' }}">{{ $i + 1 }}</span>
                        <span class="j-row-main"><span class="j-row-title">{{ $row->name }}</span><span class="j-row-sub">{{ number_format($row->total) }} {{ \Illuminate\Support\Str::plural('attempt', $row->total) }}</span></span>
                    </div>
                @empty
                    <p class="j-empty">No practice attempts in the last 7 days.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="j-cols j-cols-23">
        <div class="j-card j-card-flush">
            <div class="j-card-head j-pad">
                <div><h4>Publishing progress</h4><p class="j-note">Share of each content type that is live for learners.</p></div>
            </div>
            <div class="table-responsive">
                <table class="j-table">
                    <thead><tr><th>Content</th><th class="text-end">Live</th><th class="text-end">Other</th><th style="width: 40%">Published share</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($pipeline as [$name, $counts, $href])
                            @php
                                $total = (int) collect($counts)->sum();
                                $live = (int) ($counts['published'] ?? 0);
                                $pct = $total ? (int) round($live / $total * 100) : 0;
                            @endphp
                            <tr>
                                <td><span class="fw-semibold">{{ $name }}</span></td>
                                <td class="text-end">{{ number_format($live) }}</td>
                                <td class="text-end">{{ number_format($total - $live) }}</td>
                                <td>
                                    <div class="j-bar"><div class="j-bar-track"><span class="{{ $pct >= 100 ? 'is-full' : '' }}" style="width: {{ $pct }}%"></span></div><small>{{ $pct }}%</small></div>
                                </td>
                                <td class="text-end">@if ($href)<a href="{{ $href }}">Open</a>@endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="j-card j-card-flush">
            <div class="j-card-head j-pad"><h4>Newest students</h4>@if ($newStudents->isNotEmpty())<a href="{{ route('admin.students.index') }}">All students</a>@endif</div>
            <div class="j-list">
                @forelse ($newStudents as $student)
                    <a href="{{ route('admin.students.show', $student) }}" class="j-row">
                        <span class="j-avatar">{{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}</span>
                        <span class="j-row-main"><span class="j-row-title">{{ $student->name }}</span><span class="j-row-sub">Joined {{ $student->created_at->diffForHumans() }}</span></span>
                    </a>
                @empty
                    <p class="j-empty">{{ $me->can('users.view') ? 'No students yet.' : 'Your role cannot see student names.' }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="j-cols j-cols-2">
        @can('teachers.verify')
            <div class="j-card j-card-flush">
                <div class="j-card-head j-pad"><h4>Teachers waiting for approval</h4><a href="{{ route('admin.teachers.index', ['verification' => 'pending']) }}">View all</a></div>
                <div class="j-list">
                    @forelse ($teacherQueue as $teacher)
                        <a href="{{ route('admin.teachers.show', $teacher) }}" class="j-row">
                            <span class="j-avatar" style="background:#967adc">{{ mb_strtoupper(mb_substr($teacher->name, 0, 1)) }}</span>
                            <span class="j-row-main"><span class="j-row-title">{{ $teacher->name }}</span><span class="j-row-sub">{{ collect([$teacher->teacherProfile?->school_name, $teacher->teacherProfile?->region])->filter()->implode(' · ') ?: 'Profile incomplete' }} · registered {{ $teacher->created_at->diffForHumans() }}</span></span>
                            <i class="fa-solid fa-chevron-right j-chev" aria-hidden="true"></i>
                        </a>
                    @empty
                        <p class="j-empty">No teachers are waiting for approval.</p>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('audit.view')
            <div class="j-card j-card-flush">
                <div class="j-card-head j-pad"><h4>Recent activity</h4><a href="{{ route('admin.audit.index') }}">Audit log</a></div>
                <div class="j-list">
                    @forelse ($recentActivity as $log)
                        <div class="j-row">
                            <span class="j-avatar" style="background:#5b6f8f"><i class="fas fa-clock-rotate-left" aria-hidden="true"></i></span>
                            <span class="j-row-main"><span class="j-row-title">{{ $log->description }}</span><span class="j-row-sub">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</span></span>
                        </div>
                    @empty
                        <p class="j-empty">Nothing recorded yet.</p>
                    @endforelse
                </div>
            </div>
        @endcan
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
window.addEventListener('load', function () {
    if (typeof Chart === 'undefined') { document.querySelectorAll('.j-chart').forEach(function (c) { c.innerHTML = '<p class="j-empty">Charts need an internet connection.</p>'; }); return; }
    var C = { blue: '#42a5dc', green: '#8cc152', orange: '#f5922f', purple: '#967adc', slate: '#5b6f8f' };
    var palette = @json($palette);

    var signups = @json($signups);
    var signupCanvas = document.getElementById('signupChart');
    if (signupCanvas) new Chart(signupCanvas, {
        type: 'bar',
        data: { labels: signups.map(function (d) { return d.label; }), datasets: [
            { label: 'Students', data: signups.map(function (d) { return d.students; }), backgroundColor: C.blue, borderRadius: 3 },
            { label: 'Teachers', data: signups.map(function (d) { return d.teachers; }), backgroundColor: C.purple, borderRadius: 3 },
        ] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { x: { stacked: true, grid: { display: false } }, y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } } } },
    });

    var types = @json($typeChart);
    var typeCanvas = document.getElementById('typeChart');
    if (typeCanvas) new Chart(typeCanvas, {
        type: 'doughnut',
        data: { labels: types.map(function (t) { return t.label; }), datasets: [{ data: types.map(function (t) { return t.count; }), backgroundColor: palette, borderWidth: 2, borderColor: '#fff' }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { display: false } } },
    });

    var subjects = @json($subjectChart);
    var subjectCanvas = document.getElementById('subjectChart');
    if (subjectCanvas) new Chart(subjectCanvas, {
        type: 'bar',
        data: { labels: subjects.map(function (s) { return s.label; }), datasets: [{ label: 'Questions', data: subjects.map(function (s) { return s.count; }), backgroundColor: subjects.map(function (_, i) { return palette[i % 5]; }), borderRadius: 3 }] },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { grid: { display: false } } } },
    });
});
</script>
@endpush
