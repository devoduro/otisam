@extends('layouts.admin')

@section('title', 'Video lessons')
@section('subtitle', 'YouTube lessons embedded on the site, organised by subject and topic')

@section('actions')
    @if (config('platform.video_channels'))
        <form method="POST" action="{{ route('admin.videos.import-youtube') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-primary" title="Add new uploads from {{ collect(config('platform.video_channels'))->pluck('name')->implode(', ') }}"><i class="fab fa-youtube me-1" aria-hidden="true"></i> Import from YouTube</button>
        </form>
    @endif
    <a href="{{ route('admin.videos.create') }}" class="btn btn-premium"><i class="fas fa-plus me-1" aria-hidden="true"></i> Add video</a>
@endsection

@section('content')
<form method="GET" class="card-panel mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label for="q" class="form-label small fw-semibold">Search</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Title">
        </div>
        <div class="col-6 col-md-3">
            <label for="subject" class="form-label small fw-semibold">Subject</label>
            <select id="subject" name="subject" class="form-select">
                <option value="">All</option>
                @foreach ($subjects as $s)<option value="{{ $s->id }}" @selected((int) ($filters['subject'] ?? 0) === $s->id)>{{ $s->labelWithLevel() }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="status" class="form-label small fw-semibold">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">All</option>
                @foreach (\App\Models\Videos\VideoLesson::STATUSES as $v => $l)<option value="{{ $v }}" @selected(($filters['status'] ?? '') === $v)>{{ $l }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-premium" type="submit">Filter</button></div>
    </div>
</form>

<div class="table-panel">
    <table class="table table-hover align-middle table-stack">
        <thead><tr><th>Video</th><th>Subject</th><th>Access</th><th>Status</th><th>Views</th></tr></thead>
        <tbody>
            @forelse ($videos as $v)
                <tr>
                    <td data-label="Video">
                        <a href="{{ route('admin.videos.edit', $v) }}" class="d-flex align-items-center gap-2 fw-semibold">
                            <img src="{{ $v->thumbnailUrl() }}" alt="" width="64" height="36" class="rounded" style="object-fit:cover" loading="lazy">
                            {{ \Illuminate\Support\Str::limit($v->title, 60) }}
                        </a>
                    </td>
                    <td data-label="Subject" class="small">{{ $v->subject?->name ?? '—' }}</td>
                    <td data-label="Access"><span class="badge text-bg-{{ $v->isFree() ? 'info' : 'warning' }}">{{ ucfirst($v->access_level) }}</span></td>
                    <td data-label="Status"><span class="badge text-bg-{{ ['draft' => 'secondary', 'pending' => 'warning', 'published' => 'success', 'archived' => 'dark'][$v->status] }}">{{ \App\Models\Videos\VideoLesson::STATUSES[$v->status] }}</span></td>
                    <td data-label="Views">{{ number_format($v->view_count) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">No video lessons yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $videos->links() }}</div>
@endsection
