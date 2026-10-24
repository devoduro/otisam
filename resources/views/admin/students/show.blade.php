@extends('layouts.admin')

@section('title', $student->name)
@section('subtitle', 'Student')

@php
    $fmtTime = fn (int $s) => $s >= 3600 ? floor($s / 3600).' h '.floor($s % 3600 / 60).' min' : ($s >= 60 ? floor($s / 60).' min' : $s.' s');
    $stem = fn ($q) => \Illuminate\Support\Str::limit($q?->currentRevision?->stem ?? 'Question removed', 110);
    $colour = fn (int $p) => $p >= 70 ? 'j-green' : ($p >= 50 ? 'j-blue' : ($p >= 40 ? 'j-orange' : 'j-red'));
    $rate = $answered ? (int) round($right / $answered * 100) : null;
@endphp

@section('content')
<div class="d-grid gap-4">
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card-panel h-100">
                <h2 class="h6 fw-bold mb-3">Profile</h2>
                @php $p = $student->studentProfile; @endphp
                <dl class="row mb-0 small">
                    <dt class="col-sm-4">Phone</dt><dd class="col-sm-8">{{ $student->phone }} {!! $student->phone_verified_at ? '<span class="badge text-bg-success">verified</span>' : '<span class="badge text-bg-secondary">not verified</span>' !!}</dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $student->email ?? '—' }}</dd>
                    <dt class="col-sm-4">Class</dt><dd class="col-sm-8">{{ $p?->classLevel?->name ?? '—' }}</dd>
                    <dt class="col-sm-4">Programme</dt><dd class="col-sm-8">{{ $p?->programme?->name ?? '—' }}</dd>
                    <dt class="col-sm-4">School</dt><dd class="col-sm-8">{{ $p?->school_name ?? '—' }}</dd>
                    <dt class="col-sm-4">Region</dt><dd class="col-sm-8">{{ $p?->region ?? '—' }}</dd>
                    <dt class="col-sm-4">Under 18</dt><dd class="col-sm-8">{{ $p?->is_minor ? 'Yes' : 'No' }}</dd>
                    @if ($p?->guardian_phone)
                        <dt class="col-sm-4">Guardian</dt><dd class="col-sm-8">{{ $p->guardian_name ?? '—' }} · {{ $p->guardian_phone }}</dd>
                    @endif
                    <dt class="col-sm-4">Joined</dt><dd class="col-sm-8">{{ $student->created_at->format('j M Y, g:ia') }}</dd>
                    <dt class="col-sm-4">Last sign-in</dt><dd class="col-sm-8">{{ $student->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card-panel h-100">
                <h2 class="h6 fw-bold mb-2">Account</h2>
                <p class="small mb-3">Status: <span class="badge text-bg-{{ $student->isActive() ? 'success' : 'danger' }}">{{ $student->status->label() }}</span></p>
                @include('admin.partials.account-status', ['account' => $student])
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-end">
        <h2 class="h5 fw-bold mb-0">Practice and exam performance</h2>
        <a href="{{ route('admin.attempts.index') }}" class="small fw-semibold">All students' attempts</a>
    </div>

    {{-- Summary --}}
    <div class="j-tiles j-tiles-4">
        <div class="j-tile j-blue"><span class="ic"><i class="fas fa-clipboard-check" aria-hidden="true"></i></span><span class="bd"><span style="min-width:0"><span class="t">Attempts</span><span class="s">{{ $summary['practice'] }} practice · {{ $summary['papers'] }} papers</span></span><span class="text-end"><span class="v d-block">{{ number_format($summary['attempts']) }}</span></span></span></div>
        <div class="j-tile {{ $colour($summary['average']) }}"><span class="ic"><i class="fas fa-percent" aria-hidden="true"></i></span><span class="bd"><span style="min-width:0"><span class="t">Average score</span><span class="s">Best {{ $summary['best'] }}%</span></span><span class="text-end"><span class="v d-block">{{ $summary['average'] }}%</span></span></span></div>
        <div class="j-tile j-purple"><span class="ic"><i class="fas fa-circle-question" aria-hidden="true"></i></span><span class="bd"><span style="min-width:0"><span class="t">Questions answered</span><span class="s">{{ number_format($summary['correct']) }} correct</span></span><span class="text-end"><span class="v d-block">{{ number_format($summary['questions']) }}</span></span></span></div>
        <div class="j-tile j-slate"><span class="ic"><i class="fas fa-stopwatch" aria-hidden="true"></i></span><span class="bd"><span style="min-width:0"><span class="t">Time practising</span><span class="s">{{ $summary['days'] }} {{ \Illuminate\Support\Str::plural('day', $summary['days']) }} active{{ $summary['last'] ? ' · last '.$summary['last']->diffForHumans() : '' }}</span></span><span class="text-end"><span class="v d-block" style="font-size:1.1rem">{{ $summary['seconds'] ? $fmtTime($summary['seconds']) : '—' }}</span></span></span></div>
    </div>

    @if ($summary['attempts'] === 0)
        <div class="j-card"><p class="j-empty">This student has not saved any practice sets or papers yet.</p></div>
    @else
        <div class="j-cols j-cols-23">
            <div class="j-card">
                <div class="j-card-head"><h4>Score trend: last {{ $trend->count() }} {{ \Illuminate\Support\Str::plural('attempt', $trend->count()) }}</h4></div>
                <div class="j-chart"><canvas id="trendChart" aria-label="Score of each recent attempt"></canvas></div>
            </div>
            <div class="j-card j-card-flush">
                <div class="j-card-head j-pad"><h4>By subject</h4></div>
                <div class="table-responsive">
                    <table class="j-table">
                        <thead><tr><th>Subject</th><th class="text-end">Tries</th><th class="text-end">Avg</th><th class="text-end">Best</th></tr></thead>
                        <tbody>
                            @foreach ($bySubject as $row)
                                <tr>
                                    <td><span class="fw-semibold">{{ $row['subject']?->name }}</span><span class="d-block j-row-sub">{{ $row['questions'] }} questions · last {{ $row['last']->diffForHumans() }}</span></td>
                                    <td class="text-end">{{ $row['attempts'] }}</td>
                                    <td class="text-end"><span class="badge {{ $row['average'] >= 70 ? 'text-bg-success' : ($row['average'] >= 50 ? 'text-bg-primary' : ($row['average'] >= 40 ? 'text-bg-warning' : 'text-bg-danger')) }}">{{ $row['average'] }}%</span></td>
                                    <td class="text-end">{{ $row['best'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Every attempt --}}
        <div class="j-card j-card-flush">
            <div class="j-card-head j-pad"><div><h4>All attempts</h4><p class="j-note">{{ number_format($attempts->total()) }} saved, newest first. Open one to see each question.</p></div></div>
            <div class="table-responsive">
                <table class="j-table">
                    <thead><tr><th>When</th><th>Subject</th><th>Kind</th><th class="text-end">Score</th><th style="width: 22%">Result</th><th class="text-end">Time</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($attempts as $attempt)
                            @php $pc = $attempt->percent(); @endphp
                            <tr>
                                <td class="text-nowrap">{{ $attempt->created_at->format('j M Y, H:i') }}</td>
                                <td>{{ $attempt->subject?->name }}</td>
                                <td>{{ $attempt->kind === 'paper' ? 'Paper' : 'Practice' }}@if ($attempt->late) <span class="badge text-bg-warning">Late</span>@endif</td>
                                <td class="text-end text-nowrap"><strong>{{ $attempt->score }}/{{ $attempt->total }}</strong></td>
                                <td><div class="j-bar"><div class="j-bar-track"><span class="{{ $pc >= 70 ? 'is-full' : '' }}" style="width: {{ $pc }}%"></span></div><small>{{ $pc }}%</small></div></td>
                                <td class="text-end text-nowrap">{{ $attempt->seconds ? $fmtTime((int) $attempt->seconds) : '—' }}</td>
                                <td class="text-end"><a href="{{ route('admin.attempts.show', $attempt) }}">{{ $attempt->answers_count ? 'Questions' : 'Details' }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($attempts->hasPages())<div class="p-3 border-top">{{ $attempts->links() }}</div>@endif
        </div>

        {{-- Question by question --}}
        <div class="d-flex justify-content-between align-items-end">
            <h2 class="h5 fw-bold mb-0">Question trials</h2>
            <span class="small text-muted">{{ $answered ? number_format($answered).' answers recorded · '.$rate.'% correct' : 'Recorded from the date this feature was added' }}</span>
        </div>

        @if ($answered === 0)
            <div class="j-card"><p class="j-empty">Question-level results are saved for attempts made from now on. Earlier attempts show their score only.</p></div>
        @else
            <div class="j-cols j-cols-2">
                <div class="j-card j-card-flush">
                    <div class="j-card-head j-pad"><div><h4>Questions this student gets wrong</h4><p class="j-note">Most misses first, with how many times it was tried.</p></div></div>
                    <div class="j-list">
                        @forelse ($missed as $row)
                            @php $q = $missedQuestions[$row->question_id] ?? null; @endphp
                            <a href="{{ $q ? route('admin.questions.show', $q) : '#' }}" class="j-row">
                                <span class="j-rank" style="background:#da4453">{{ (int) $row->tries - (int) $row->rights }}</span>
                                <span class="j-row-main"><span class="j-row-title" style="white-space: normal">{{ $stem($q) }}</span><span class="j-row-sub">{{ $q?->subject?->name }} · tried {{ $row->tries }}× · right {{ (int) $row->rights }}× · last {{ \Illuminate\Support\Carbon::parse($row->last_at)->diffForHumans() }}</span></span>
                            </a>
                        @empty
                            <p class="j-empty">No wrong answers recorded.</p>
                        @endforelse
                    </div>
                </div>

                <div class="j-card j-card-flush">
                    <div class="j-card-head j-pad"><div><h4>Every answer given</h4><p class="j-note">Newest first.</p></div></div>
                    <div class="j-list" style="max-height: 32rem">
                        @foreach ($latest as $answer)
                            <div class="j-row">
                                <span class="j-avatar" style="background: {{ $answer->is_correct === null ? '#5b6f8f' : ($answer->is_correct ? '#8cc152' : '#da4453') }}"><i class="fas {{ $answer->is_correct === null ? 'fa-pen' : ($answer->is_correct ? 'fa-check' : 'fa-xmark') }}" aria-hidden="true"></i></span>
                                <span class="j-row-main">
                                    <span class="j-row-title" style="white-space: normal">{{ $stem($answer->question) }}</span>
                                    <span class="j-row-sub" style="white-space: normal">Answered: <strong>{{ $answer->given !== null && $answer->given !== '' ? \Illuminate\Support\Str::limit($answer->given, 80) : '(left blank)' }}</strong> · {{ $answer->created_at->format('j M, H:i') }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                    @if ($latest->hasPages())<div class="p-3 border-top">{{ $latest->links() }}</div>@endif
                </div>
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
@if ($summary['attempts'] > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
window.addEventListener('load', function () {
    var el = document.getElementById('trendChart');
    if (!el || typeof Chart === 'undefined') { if (el) { el.parentNode.innerHTML = '<p class="j-empty">Charts need an internet connection.</p>'; } return; }
    var trend = @json($trend);
    new Chart(el, {
        type: 'line',
        data: { labels: trend.map(function (t) { return t.label; }), datasets: [{ label: 'Score %', data: trend.map(function (t) { return t.percent; }), borderColor: '#42a5dc', backgroundColor: '#42a5dc22', fill: true, tension: .3, pointRadius: 4, pointBackgroundColor: trend.map(function (t) { return t.percent >= 70 ? '#8cc152' : (t.percent >= 50 ? '#42a5dc' : (t.percent >= 40 ? '#f5922f' : '#da4453')); }) }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { afterLabel: function (c) { return trend[c.dataIndex].subject || ''; } } } }, scales: { y: { min: 0, max: 100, ticks: { callback: function (v) { return v + '%'; } } }, x: { grid: { display: false } } } },
    });
});
</script>
@endif
@endpush
