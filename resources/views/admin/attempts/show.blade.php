@extends('layouts.admin')

@section('title', ($attempt->subject?->name ?? 'Practice').' attempt')
@section('subtitle', $attempt->created_at->format('l, j F Y, H:i'))

@section('actions')
    <a href="{{ route('admin.attempts.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Back to attempts</a>

@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card-panel">
            <p class="display-6 fw-bold mb-0">{{ $attempt->score }}/{{ $attempt->total }} <span class="fs-5 text-muted">{{ $attempt->percent() }}%</span></p>
            <dl class="row small mt-3 mb-0">
                <dt class="col-5 text-muted">Learner</dt>
                <dd class="col-7">
                    @if ($showNames && $attempt->user)
                        <a href="{{ route('admin.students.show', $attempt->user) }}">{{ $attempt->user->name }}</a>
                    @else
                        Learner #{{ $attempt->user_id }}
                    @endif
                </dd>
                <dt class="col-5 text-muted">Subject</dt><dd class="col-7">{{ $attempt->subject?->labelWithLevel() }}</dd>
                <dt class="col-5 text-muted">Kind</dt><dd class="col-7">{{ $attempt->kind === 'paper' ? 'Full paper: '.($attempt->paper?->title ?? '') : 'Practice set' }}</dd>
                <dt class="col-5 text-muted">Time taken</dt><dd class="col-7">{{ gmdate($attempt->seconds >= 3600 ? 'G:i:s' : 'i:s', (int) $attempt->seconds) }}@if ($attempt->late) <span class="badge text-bg-warning">Handed in late</span>@endif</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card-panel">
            <h2 class="h6 fw-bold mb-3">Other recent attempts by this learner</h2>
            @forelse ($others as $other)
                <a href="{{ route('admin.attempts.show', $other) }}" class="report-item text-decoration-none">
                    <div>
                        <div class="fw-semibold text-dark">{{ $other->subject?->name }} · {{ $other->score }}/{{ $other->total }} ({{ $other->percent() }}%)</div>
                        <div class="small text-muted">{{ $other->created_at->diffForHumans() }}</div>
                    </div>
                    <i class="fa-solid fa-chevron-right report-arrow" aria-hidden="true"></i>
                </a>
            @empty
                <p class="text-muted small mb-0">None.</p>
            @endforelse
        </div>
    </div>
</div>

@if ($attempt->answers->isNotEmpty())
    <div class="j-card j-card-flush mt-3">
        <div class="j-card-head j-pad"><div><h4>Question by question</h4><p class="j-note">{{ $attempt->answers->where('is_correct', true)->count() }} right, {{ $attempt->answers->where('is_correct', false)->count() }} wrong{{ $attempt->answers->whereNull('is_correct')->count() ? ', '.$attempt->answers->whereNull('is_correct')->count().' theory (not marked)' : '' }}.</p></div></div>
        <div class="table-responsive">
            <table class="j-table">
                <thead><tr><th style="width:3rem">#</th><th>Question</th><th>Answer given</th><th class="text-end">Result</th></tr></thead>
                <tbody>
                    @foreach ($attempt->answers as $i => $answer)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><a href="{{ route('admin.questions.show', $answer->question) }}" style="font-size:.875rem">{{ \Illuminate\Support\Str::limit($answer->question?->currentRevision?->stem ?? 'Question removed', 120) }}</a></td>
                            <td>{{ $answer->given !== null && $answer->given !== '' ? \Illuminate\Support\Str::limit($answer->given, 120) : '(left blank)' }}</td>
                            <td class="text-end">
                                @if ($answer->is_correct === null)<span class="badge text-bg-secondary">Theory</span>
                                @elseif ($answer->is_correct)<span class="badge text-bg-success">Right</span>
                                @else<span class="badge text-bg-danger">Wrong</span>@endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <p class="small text-muted mt-3">Question-level results were not saved for this attempt; only its score is available.</p>
@endif
@endsection
