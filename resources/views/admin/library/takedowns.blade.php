@extends('layouts.admin')

@section('title', 'Takedown requests')
@section('subtitle', 'Copyright complaints from the public form')

@section('content')
<ul class="nav nav-pills mb-3">
    @foreach (['open' => 'Open', 'resolved' => 'Resolved', 'rejected' => 'Rejected'] as $value => $label)
        <li class="nav-item"><a class="nav-link {{ $status === $value ? 'active' : '' }}" href="{{ route('admin.library.takedowns.index', ['status' => $value]) }}">{{ $label }}</a></li>
    @endforeach
</ul>

@forelse ($requests as $r)
    <div class="card-panel mb-3">
        <div class="d-flex flex-wrap justify-content-between gap-2">
            <div>
                <h2 class="h6 fw-bold mb-1">#{{ $r->id }} · {{ $r->rights_holder }}</h2>
                <p class="small text-muted mb-2">{{ $r->name }} · <a href="mailto:{{ $r->email }}">{{ $r->email }}</a>{{ $r->phone ? ' · '.$r->phone : '' }} · {{ $r->created_at->format('j M Y, H:i') }}</p>
            </div>
            @if ($r->resource)
                <div class="small">Resource: <a href="{{ route('admin.library.resources.edit', $r->resource) }}">{{ \Illuminate\Support\Str::limit($r->resource->title, 60) }}</a>
                    @include('admin.library.resources._status', ['resource' => $r->resource])</div>
            @elseif ($r->resource_url)
                <div class="small text-break">Link given: {{ $r->resource_url }}</div>
            @endif
        </div>
        <p class="small mb-3" style="white-space:pre-line">{{ $r->details }}</p>

        @if ($r->status === 'open')
            <form method="POST" action="{{ route('admin.library.takedowns.update', $r) }}" class="border-top pt-3">
                @csrf @method('PUT')
                <label for="note-{{ $r->id }}" class="form-label small fw-semibold">Outcome note (kept on record)</label>
                <textarea id="note-{{ $r->id }}" name="resolution_note" rows="2" class="form-control form-control-sm mb-2" required maxlength="2000"></textarea>
                @if ($r->resource && $r->resource->status === 'published')
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="unpublish" value="1" id="unpub-{{ $r->id }}">
                        <label class="form-check-label small" for="unpub-{{ $r->id }}">Archive the resource (removes it from the public library)</label>
                    </div>
                @endif
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success" type="submit" name="status" value="resolved">Mark resolved</button>
                    <button class="btn btn-sm btn-outline-secondary" type="submit" name="status" value="rejected">Reject</button>
                </div>
            </form>
        @else
            <p class="small mb-0 border-top pt-2"><strong>{{ ucfirst($r->status) }}</strong> by {{ $r->handler?->name ?? 'unknown' }}, {{ $r->handled_at?->format('j M Y') }}: {{ $r->resolution_note }}</p>
        @endif
    </div>
@empty
    <div class="card-panel"><p class="text-muted mb-0">No {{ $status }} requests.</p></div>
@endforelse
{{ $requests->links() }}
@endsection
