@extends('layouts.admin')

@section('title', 'Library resources')

@section('actions')
    @can('resources.manage')
        @can('resources.bulk-import')
            <a href="{{ route('admin.library.resources.bulk') }}" class="btn btn-outline-secondary rounded-pill px-3">Bulk upload</a>
        @endcan
        <a href="{{ route('admin.library.resources.create') }}" class="btn btn-premium rounded-pill px-4">Add resource</a>
    @endcan
@endsection

@section('content')
<form method="GET" class="card-panel mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label for="q" class="form-label small fw-semibold">Search</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Title">
        </div>
        <div class="col-6 col-md-2">
            <label for="category" class="form-label small fw-semibold">Category</label>
            <select id="category" name="category" class="form-select">
                <option value="">All</option>
                @foreach ($categories as $c)<option value="{{ $c->id }}" @selected((int) ($filters['category'] ?? 0) === $c->id)>{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="subject" class="form-label small fw-semibold">Subject</label>
            <select id="subject" name="subject" class="form-select">
                <option value="">All</option>
                @foreach ($subjects as $s)<option value="{{ $s->id }}" @selected((int) ($filters['subject'] ?? 0) === $s->id)>{{ $s->labelWithLevel() }}</option>@endforeach
            </select>
        </div>
        <div class="col-4 col-md-1">
            <label for="year" class="form-label small fw-semibold">Year</label>
            <input id="year" type="number" name="year" value="{{ $filters['year'] ?? '' }}" class="form-control" min="1950">
        </div>
        <div class="col-4 col-md-2">
            <label for="status" class="form-label small fw-semibold">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">All</option>
                @foreach (\App\Models\Library\Resource::STATUSES as $v => $l)<option value="{{ $v }}" @selected(($filters['status'] ?? '') === $v)>{{ $l }}</option>@endforeach
            </select>
        </div>
        <div class="col-4 col-md-1">
            <label for="access" class="form-label small fw-semibold">Access</label>
            <select id="access" name="access" class="form-select">
                <option value="">All</option>
                @foreach (\App\Models\Library\Resource::ACCESS_LEVELS as $v => $l)<option value="{{ $v }}" @selected(($filters['access'] ?? '') === $v)>{{ $l }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-1 d-grid"><button class="btn btn-premium" type="submit">Filter</button></div>
    </div>
</form>

<div class="table-panel">
    <table class="table table-hover align-middle table-stack">
        <thead><tr><th>Title</th><th>Category</th><th>Subject</th><th>Exam</th><th>Access</th><th>Status</th><th>File</th></tr></thead>
        <tbody>
            @forelse ($resources as $r)
                <tr>
                    <td data-label="Title"><a href="{{ route('admin.library.resources.edit', $r) }}" class="fw-semibold">{{ \Illuminate\Support\Str::limit($r->title, 70) }}</a></td>
                    <td data-label="Category" class="small">{{ $r->category->name }}</td>
                    <td data-label="Subject" class="small">{{ $r->subject?->name ?? '—' }}</td>
                    <td data-label="Exam" class="small">{{ $r->examLabel() ?? '—' }}</td>
                    <td data-label="Access"><span class="badge text-bg-{{ $r->isFree() ? 'info' : 'warning' }}">{{ \App\Models\Library\Resource::ACCESS_LEVELS[$r->access_level] }}</span></td>
                    <td data-label="Status">
                        @include('admin.library.resources._status', ['resource' => $r])
                    </td>
                    <td data-label="File" class="small">
                        @if ($r->mainFile)
                            {{ strtoupper($r->mainFile->extension) }} · {{ $r->mainFile->humanSize() }}
                            @if ($r->mainFile->preview_status === 'failed')<span class="badge text-bg-danger">preview failed</span>@endif
                        @else
                            <span class="text-danger">No file</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No resources match.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $resources->links() }}</div>
@endsection
