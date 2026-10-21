@extends('layouts.admin')

@section('title', 'Q&A reports')
@section('subtitle', 'Community questions and answers people have reported. Posts with '.config('platform.qa.hide_after_reports', 3).' open reports are hidden until you decide.')

@section('content')
@php
    use Illuminate\Support\Str;
    $postLink = fn ($post) => $post instanceof \App\Models\Community\Question ? route('qa.show', $post) : route('qa.show', $post->qa_question_id).'#answer-'.$post->id;
    $typeOf = fn ($post) => $post instanceof \App\Models\Community\Question ? 'question' : 'answer';
@endphp
<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link {{ $tab === 'reported' ? 'active' : '' }}" href="{{ route('admin.qa.index') }}">Reported <span class="badge text-bg-light">{{ $openCount }}</span></a></li>
    <li class="nav-item"><a class="nav-link {{ $tab === 'hidden' ? 'active' : '' }}" href="{{ route('admin.qa.index', ['tab' => 'hidden']) }}">Hidden <span class="badge text-bg-light">{{ $hiddenCount }}</span></a></li>
</ul>


@if ($tab === 'reported')
    <div class="table-panel">
        <table class="table align-middle table-stack">
            <thead><tr><th>Post</th><th>Author</th><th>Reports</th><th>Status</th><th class="text-end">Decision</th></tr></thead>
            <tbody>
                @forelse ($reported as $row)
                    @php $post = $row->reportable; $why = $reasons[$row->reportable_type.':'.$row->reportable_id] ?? collect(); @endphp
                    @continue(! $post)
                    <tr>
                        <td data-label="Post">
                            <span class="badge text-bg-secondary">{{ ucfirst($typeOf($post)) }}</span>
                            <a href="{{ $postLink($post) }}" target="_blank" rel="noopener" class="fw-semibold">{{ Str::limit($post instanceof \App\Models\Community\Question ? $post->title : $post->body, 80) }}</a>
                            <div class="small text-muted">{{ Str::limit($post->body, 160) }}</div>
                        </td>
                        <td data-label="Author" class="small">{{ $post->author?->name ?? '—' }}</td>
                        <td data-label="Reports" class="small">
                            <strong>{{ $row->open_count }}</strong>
                            <ul class="list-unstyled mb-0">
                                @foreach ($why->take(5) as $r)
                                    <li>{{ $labels[$r->reason] ?? $r->reason }}@if ($r->note): <span class="text-muted">{{ Str::limit($r->note, 80) }}</span>@endif <span class="text-muted">({{ $r->reporter?->name ?? 'deleted' }}, {{ $r->created_at->diffForHumans() }})</span></li>
                                @endforeach
                            </ul>
                        </td>
                        <td data-label="Status" class="small">{{ $post->isVisible() ? 'Visible' : 'Hidden' }}</td>
                        <td data-label="Decision" class="text-end">@include('admin.qa.actions', ['post' => $post, 'type' => $typeOf($post)])</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No open reports.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $reported->links() }}</div>
@else
    @foreach (['Questions' => $hiddenQuestions, 'Answers' => $hiddenAnswers] as $heading => $list)
        <h2 class="h6 mt-3">{{ $heading }}</h2>
        <div class="table-panel">
            <table class="table align-middle table-stack">
                <thead><tr><th>Post</th><th>Author</th><th>Why hidden</th><th>Hidden</th><th class="text-end">Decision</th></tr></thead>
                <tbody>
                    @forelse ($list as $post)
                        <tr>
                            <td data-label="Post">
                                <a href="{{ $postLink($post) }}" target="_blank" rel="noopener" class="fw-semibold">{{ Str::limit($post instanceof \App\Models\Community\Question ? $post->title : $post->body, 80) }}</a>
                                @if ($post instanceof \App\Models\Community\Answer)<div class="small text-muted">On: {{ Str::limit($post->question?->title, 70) }}</div>@endif
                            </td>
                            <td data-label="Author" class="small">{{ $post->author?->name ?? '—' }}</td>
                            <td data-label="Why hidden" class="small">{{ $post->hidden_reason === 'reports' ? 'Reports' : 'Moderator' }}</td>
                            <td data-label="Hidden" class="small text-nowrap">{{ $post->updated_at->diffForHumans() }}</td>
                            <td data-label="Decision" class="text-end">@include('admin.qa.actions', ['post' => $post, 'type' => $typeOf($post)])</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Nothing hidden.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $list->links() }}</div>
    @endforeach
@endif
@endsection
