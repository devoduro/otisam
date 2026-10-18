@extends('layouts.admin')

@section('title', 'Teacher content')
@section('subtitle', 'Approve teacher videos and take down public materials that are not suitable')

@section('content')
<h3 class="h5 mb-2">Videos awaiting review <span class="badge text-bg-warning align-middle">{{ $videos->count() }}</span></h3>
<div class="table-panel mb-4">
    <table class="table align-middle table-stack">
        <thead><tr><th>Video</th><th>Teacher</th><th>Subject</th><th>Sent</th><th class="text-end">Decision</th></tr></thead>
        <tbody>
            @forelse ($videos as $v)
                <tr>
                    <td data-label="Video">
                        <a href="https://www.youtube.com/watch?v={{ $v->youtube_id }}" target="_blank" rel="noopener noreferrer" class="d-flex align-items-center gap-2 fw-semibold">
                            <img src="{{ $v->thumbnailUrl() }}" alt="" width="64" height="36" class="rounded" style="object-fit:cover" loading="lazy">
                            {{ \Illuminate\Support\Str::limit($v->title, 60) }}
                        </a>
                        @if ($v->description)<div class="small text-muted mt-1">{{ \Illuminate\Support\Str::limit($v->description, 140) }}</div>@endif
                    </td>
                    <td data-label="Teacher" class="small"><a href="{{ route('admin.teachers.show', $v->creator) }}">{{ $v->creator->name }}</a></td>
                    <td data-label="Subject" class="small">{{ $v->subject?->labelWithLevel() ?? '—' }}@if ($v->classLevel) · {{ $v->classLevel->name }}@endif</td>
                    <td data-label="Sent" class="small text-nowrap">{{ $v->updated_at->diffForHumans() }}</td>
                    <td data-label="Decision" class="text-end">
                        <form method="POST" action="{{ route('admin.moderation.videos.approve', $v) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <details class="d-inline-block text-start">
                            <summary class="btn btn-sm btn-outline-danger">Reject</summary>
                            <form method="POST" action="{{ route('admin.moderation.videos.reject', $v) }}" class="mt-2" style="min-width:260px">
                                @csrf
                                <label for="reject-{{ $v->id }}" class="form-label small fw-semibold">Reason (sent to the teacher)</label>
                                <textarea id="reject-{{ $v->id }}" name="reason" rows="2" maxlength="500" required class="form-control form-control-sm"></textarea>
                                <button type="submit" class="btn btn-sm btn-danger mt-2">Reject video</button>
                            </form>
                        </details>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No teacher videos are waiting for review.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h3 class="h5 mb-2">Public teacher materials</h3>
<p class="small text-muted">These are on Downloads and the teacher's public profile. Hiding one keeps it shared with other teachers only.</p>
<div class="table-panel">
    <table class="table align-middle table-stack">
        <thead><tr><th>Material</th><th>Teacher</th><th>Subject</th><th>Downloads</th><th>Shared</th><th class="text-end">Action</th></tr></thead>
        <tbody>
            @forelse ($materials as $m)
                <tr>
                    <td data-label="Material">
                        <a href="{{ route('admin.moderation.materials.download', $m) }}" class="fw-semibold">{{ \Illuminate\Support\Str::limit($m->title, 60) }}</a>
                        <div class="small text-muted">{{ \App\Models\Classroom\TeacherMaterial::KINDS[$m->kind] ?? $m->kind }} · {{ $m->file_name }}</div>
                    </td>
                    <td data-label="Teacher" class="small">@if ($m->owner)<a href="{{ route('admin.teachers.show', $m->owner) }}">{{ $m->owner->name }}</a>@else — @endif</td>
                    <td data-label="Subject" class="small">{{ $m->subject?->labelWithLevel() ?? '—' }}@if ($m->classLevel) · {{ $m->classLevel->name }}@endif</td>
                    <td data-label="Downloads">{{ number_format($m->download_count) }}</td>
                    <td data-label="Shared" class="small text-nowrap">{{ $m->created_at->diffForHumans() }}</td>
                    <td data-label="Action" class="text-end">
                        <details class="d-inline-block text-start">
                            <summary class="btn btn-sm btn-outline-danger">Hide</summary>
                            <form method="POST" action="{{ route('admin.moderation.materials.hide', $m) }}" class="mt-2" style="min-width:260px">
                                @csrf
                                <label for="hide-{{ $m->ulid }}" class="form-label small fw-semibold">Reason (sent to the teacher)</label>
                                <textarea id="hide-{{ $m->ulid }}" name="reason" rows="2" maxlength="500" required class="form-control form-control-sm" placeholder="e.g. Copyrighted textbook pages"></textarea>
                                <button type="submit" class="btn btn-sm btn-danger mt-2">Hide from public</button>
                            </form>
                        </details>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No public teacher materials.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $materials->links() }}</div>
@endsection
