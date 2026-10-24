@extends('layouts.admin')

@section('title', 'Question bank')
@section('subtitle', number_format($questions->total()).' questions match')

@section('content')
<form method="GET" class="card-panel mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="q" class="form-label small fw-semibold">Search</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Question text contains…">
        </div>
        <div class="col-md-3">
            <label for="subject" class="form-label small fw-semibold">Subject</label>
            <select id="subject" name="subject" class="form-select">
                <option value="">All subjects</option>
                @foreach ($subjects as $s)<option value="{{ $s->id }}" @selected((int) ($filters['subject'] ?? 0) === $s->id)>{{ $s->labelWithLevel() }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="type" class="form-label small fw-semibold">Type</label>
            <select id="type" name="type" class="form-select">
                <option value="">All types</option>
                @foreach ($typeLabels as $value => $label)<option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="use" class="form-label small fw-semibold">Used for</label>
            <select id="use" name="use" class="form-select">
                <option value="">Any</option>
                @foreach (\App\Models\QuestionBank\Question::CONFIDENTIALITY as $value => $label)<option value="{{ $value }}" @selected(($filters['use'] ?? '') === $value)>{{ $label }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-1 d-grid"><button class="btn btn-premium" type="submit">Filter</button></div>
    </div>
</form>

<div class="table-panel">
    <table class="table align-middle table-stack small mb-0">
        <thead><tr><th>Question</th><th>Subject</th><th>Type</th><th>Difficulty</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse ($questions as $question)
                <tr>
                    <td data-label="Question"><a href="{{ route('admin.questions.show', $question) }}" class="fw-semibold text-decoration-none">{{ \Illuminate\Support\Str::limit($question->currentRevision?->stem, 110) }}</a></td>
                    <td data-label="Subject">{{ $question->subject?->labelWithLevel() }}</td>
                    <td data-label="Type">{{ $typeLabels[$question->type] ?? $question->type }}</td>
                    <td data-label="Difficulty">{{ $question->difficulty }}/5</td>
                    <td data-label="Status"><span class="badge text-bg-light border">{{ \App\Models\QuestionBank\Question::STATUSES[$question->status] ?? $question->status }}</span></td>
                    <td class="text-end"><a href="{{ route('admin.questions.show', $question) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No questions match.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $questions->links() }}</div>
@endsection
