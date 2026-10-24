@extends('layouts.admin')

@section('title', 'Practice attempts')
@section('subtitle', number_format($attempts->total()).' attempts by signed-in students')

@section('content')
<form method="GET" class="card-panel mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label for="subject" class="form-label small fw-semibold">Subject</label>
            <select id="subject" name="subject" class="form-select">
                <option value="">All subjects</option>
                @foreach ($subjects as $s)<option value="{{ $s->id }}" @selected((int) ($filters['subject'] ?? 0) === $s->id)>{{ $s->labelWithLevel() }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="kind" class="form-label small fw-semibold">Kind</label>
            <select id="kind" name="kind" class="form-select">
                <option value="">All</option>
                <option value="practice" @selected(($filters['kind'] ?? '') === 'practice')>Practice set</option>
                <option value="paper" @selected(($filters['kind'] ?? '') === 'paper')>Full paper</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label for="days" class="form-label small fw-semibold">Period</label>
            <select id="days" name="days" class="form-select">
                <option value="">All time</option>
                @foreach ([7, 30, 90] as $d)<option value="{{ $d }}" @selected((int) ($filters['days'] ?? 0) === $d)>Last {{ $d }} days</option>@endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-premium" type="submit">Filter</button></div>
    </div>
</form>

<div class="table-panel">
    <table class="table align-middle table-stack small mb-0">
        <thead><tr><th>When</th><th>Learner</th><th>Subject</th><th>Kind</th><th>Score</th><th>Time</th><th></th></tr></thead>
        <tbody>
            @forelse ($attempts as $attempt)
                <tr>
                    <td data-label="When" class="text-nowrap">{{ $attempt->created_at->format('j M Y, H:i') }}</td>
                    <td data-label="Learner">{{ $showNames ? ($attempt->user?->name ?? 'Deleted account') : 'Learner #'.$attempt->user_id }}</td>
                    <td data-label="Subject">{{ $attempt->subject?->labelWithLevel() }}</td>
                    <td data-label="Kind">{{ $attempt->kind === 'paper' ? 'Full paper' : 'Practice set' }}</td>
                    <td data-label="Score"><strong>{{ $attempt->score }}/{{ $attempt->total }}</strong> <span class="text-muted">({{ $attempt->percent() }}%)</span></td>
                    <td data-label="Time">{{ gmdate($attempt->seconds >= 3600 ? 'G:i:s' : 'i:s', (int) $attempt->seconds) }}@if ($attempt->late) <span class="badge text-bg-warning">Late</span>@endif</td>
                    <td class="text-end"><a href="{{ route('admin.attempts.show', $attempt) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No attempts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $attempts->links() }}</div>
@endsection
