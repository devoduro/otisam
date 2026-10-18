@extends('layouts.admin')

@section('title', $version->name)
@section('subtitle', $version->educationLevel->name . ' curriculum version')

@section('actions')
    @can('curriculum.manage')
        <a href="{{ route('admin.curriculum.versions.edit', $version) }}" class="btn btn-outline-secondary rounded-pill px-4">Edit version</a>
    @endcan
@endsection

@section('content')
<div class="card-panel mb-3">
    <dl class="row mb-0 small">
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">@include('admin.curriculum.versions._status', ['status' => $version->status]) {{ $version->isPublic() ? '' : '(learners cannot see it yet)' }}</dd>
        <dt class="col-sm-3">Effective</dt><dd class="col-sm-9">{{ $version->effective_from?->format('j M Y') ?? 'Not set' }}{{ $version->effective_to ? ' to ' . $version->effective_to->format('j M Y') : '' }}</dd>
        <dt class="col-sm-3">Official source</dt><dd class="col-sm-9">{{ $version->source_document ?? 'Not recorded' }}</dd>
        @if ($version->notes)<dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $version->notes }}</dd>@endif
    </dl>
</div>

<div class="table-panel">
    <table class="table table-hover align-middle table-stack">
        <thead><tr><th>Subject</th><th>Category</th><th>Items in this version</th><th class="text-end">Topic tree</th></tr></thead>
        <tbody>
            @forelse ($subjects as $subject)
                <tr>
                    <td data-label="Subject" class="fw-semibold">{{ $subject->name }} @unless ($subject->is_active)<span class="badge text-bg-secondary">inactive</span>@endunless</td>
                    <td data-label="Category">{{ \App\Models\Curriculum\Subject::CATEGORIES[$subject->category] ?? $subject->category }}</td>
                    <td data-label="Items">{{ $subject->nodes_count ?: '—' }}</td>
                    <td data-label="Topic tree" class="text-end">
                        <a href="{{ route('admin.curriculum.tree.show', [$version, $subject]) }}" class="btn btn-sm {{ $subject->nodes_count ? 'btn-outline-primary' : 'btn-outline-secondary' }}">{{ $subject->nodes_count ? 'Open' : 'Start' }}</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted small">This level has no subjects yet. Add them under Curriculum → Subjects.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
