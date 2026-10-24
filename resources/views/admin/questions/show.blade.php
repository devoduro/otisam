@extends('layouts.admin')

@section('title', 'Question')
@section('subtitle', collect([$question->subject?->labelWithLevel(), $typeLabels[$question->type] ?? $question->type])->filter()->join(' · '))

@section('actions')
    <a href="{{ route('admin.questions.index', ['subject' => $question->subject_id]) }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Back to the list</a>
@endsection

@section('content')
@php $revision = $question->currentRevision; @endphp
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-panel">
            <p class="fw-semibold fs-6 mb-3" style="white-space: pre-line">{{ $revision?->stem }}</p>

            @if (in_array($question->type, ['mcq', 'true_false'], true))
                <ul class="list-unstyled mb-0">
                    @foreach ($revision?->payload['options'] ?? [] as $option)
                        @php $isCorrect = $key && ($key['correct'] ?? null) === $option['id']; @endphp
                        <li class="border rounded-3 px-3 py-2 mb-2 {{ $isCorrect ? 'border-success bg-success-subtle' : '' }}">
                            <strong>{{ $option['id'] }}.</strong> {{ $option['text'] }} @if ($isCorrect)<span class="badge text-bg-success ms-1">Correct</span>@endif
                        </li>
                    @endforeach
                </ul>
            @elseif ($question->type === 'fill_in' && $key)
                <p class="mb-0"><span class="text-muted">Accepted answers:</span> <strong>{{ implode(' / ', $key['accepted'] ?? []) }}</strong></p>
            @elseif ($question->type === 'theory' && $key)
                <h3 class="h6 fw-bold mt-2">Model answer ({{ $question->default_marks + 0 }} marks)</h3>
                <p class="bg-success-subtle rounded-3 p-3" style="white-space: pre-line">{{ $key['model'] ?? '' }}</p>
                @if (! empty($key['points']))
                    <h3 class="h6 fw-bold">Marking points</h3>
                    <ul class="mb-0">@foreach ($key['points'] as $point)<li>{{ $point }}</li>@endforeach</ul>
                @endif
            @endif

            @unless ($key)
                <p class="small text-muted mt-3 mb-0">Your role cannot see answer keys.</p>
            @endunless

            @if ($revision?->explanation)
                <div class="alert alert-info mt-3 mb-0"><strong>Explanation:</strong> {{ $revision->explanation }}</div>
            @endif
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-panel">
            <h2 class="h6 fw-bold mb-3">Details</h2>
            <dl class="row small mb-0">
                <dt class="col-5 text-muted">Status</dt><dd class="col-7">{{ \App\Models\QuestionBank\Question::STATUSES[$question->status] ?? $question->status }}</dd>
                <dt class="col-5 text-muted">Used for</dt><dd class="col-7">{{ \App\Models\QuestionBank\Question::CONFIDENTIALITY[$question->confidentiality] ?? $question->confidentiality }}</dd>
                <dt class="col-5 text-muted">Difficulty</dt><dd class="col-7">{{ $question->difficulty }}/5</dd>
                <dt class="col-5 text-muted">Access</dt><dd class="col-7">{{ ucfirst($question->access_level ?? 'free') }}</dd>
                <dt class="col-5 text-muted">Topics</dt><dd class="col-7">{{ $question->curriculumNodes->pluck('title')->join(', ') ?: 'None' }}</dd>
                <dt class="col-5 text-muted">Author</dt><dd class="col-7">{{ $question->author?->name ?? 'Otisam' }}</dd>
                <dt class="col-5 text-muted">Added</dt><dd class="col-7">{{ $question->created_at?->format('j M Y') }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
