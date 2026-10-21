@extends('layouts.admin')

@section('title', 'Lesson plans')
@section('subtitle', 'Review lesson plans teachers share before they appear in Lesson plans')

@section('content')
<ul class="nav nav-pills mb-3">
    @foreach (['pending' => 'Awaiting review', 'published' => 'Published', 'archived' => 'Archived'] as $key => $label)
        <li class="nav-item">
            <a class="nav-link {{ $status === $key ? 'active' : '' }}" href="{{ route('admin.lesson-plans.index', ['status' => $key]) }}">{{ $label }} <span class="badge text-bg-light">{{ $counts[$key] ?? 0 }}</span></a>
        </li>
    @endforeach
</ul>

<div class="table-panel">
    <table class="table align-middle table-stack">
        <thead><tr><th>Plan</th><th>Author</th><th>Class</th><th>Updated</th><th class="text-end">Decision</th></tr></thead>
        <tbody>
            @forelse ($plans as $p)
                <tr>
                    <td data-label="Plan">
                        <a href="{{ route('lesson-plans.show', $p) }}" target="_blank" rel="noopener" class="fw-semibold">{{ \Illuminate\Support\Str::limit($p->title, 70) }}</a>
                        <div class="small text-muted">{{ collect([$p->weekLabel(), $p->strandLabel()])->filter()->implode(' · ') }}</div>
                    </td>
                    <td data-label="Author" class="small">@if ($p->creator?->isTeacher())<a href="{{ route('admin.teachers.show', $p->creator) }}">{{ $p->creator->name }}</a>@else {{ $p->authorName() }} @endif</td>
                    <td data-label="Class" class="small">{{ $p->subject?->labelWithLevel() ?? '—' }}@if ($p->classLevel) · {{ $p->classLevel->name }}@endif</td>
                    <td data-label="Updated" class="small text-nowrap">{{ $p->updated_at->diffForHumans() }}</td>
                    <td data-label="Decision" class="text-end">
                        @if ($p->status === 'pending')
                            <form method="POST" action="{{ route('admin.lesson-plans.approve', $p) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                        @endif
                        @if ($p->status === 'published')
                            <form method="POST" action="{{ route('admin.lesson-plans.archive', $p) }}" class="d-inline" onsubmit="return confirm('Take this plan off the public site?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Archive</button>
                            </form>
                        @endif
                        @if (in_array($p->status, ['pending', 'published'], true) && $p->creator)
                            <details class="d-inline-block text-start">
                                <summary class="btn btn-sm btn-outline-danger">Send back</summary>
                                <form method="POST" action="{{ route('admin.lesson-plans.reject', $p) }}" class="mt-2" style="min-width:260px">
                                    @csrf
                                    <label for="reject-{{ $p->id }}" class="form-label small fw-semibold">Reason (sent to the teacher)</label>
                                    <textarea id="reject-{{ $p->id }}" name="reason" rows="2" maxlength="500" required class="form-control form-control-sm"></textarea>
                                    <button type="submit" class="btn btn-sm btn-danger mt-2">Send back</button>
                                </form>
                            </details>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Nothing here.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $plans->links() }}</div>
@endsection
