@extends('layouts.admin')

@section('title', 'Curriculum versions')
@section('subtitle', 'Each version holds the topic tree of every subject, entered from an official document')

@section('actions')
    @can('curriculum.manage')
        <a href="{{ route('admin.curriculum.versions.create') }}" class="btn btn-premium rounded-pill px-4">New version</a>
    @endcan
@endsection

@section('content')
<div class="alert alert-info small">
    Enter strands, topics and indicators only from the official GES/NaCCA documents, and mark them <strong>official</strong> with a page reference.
    Drafts are hidden from learners until you set the version to <strong>Active</strong>.
</div>

@foreach ($levels as $level)
    @continue($level->versions->isEmpty() && !$level->is_active)
    <h2 class="h6 fw-bold mt-2 mb-2">{{ $level->name }}</h2>
    <div class="table-panel mb-4">
        <table class="table table-hover align-middle table-stack">
            <thead><tr><th>Version</th><th>Status</th><th>Effective</th><th>Items</th><th>Source</th></tr></thead>
            <tbody>
                @forelse ($level->versions as $version)
                    <tr>
                        <td data-label="Version"><a href="{{ route('admin.curriculum.versions.show', $version) }}" class="fw-semibold">{{ $version->name }}</a></td>
                        <td data-label="Status">@include('admin.curriculum.versions._status', ['status' => $version->status])</td>
                        <td data-label="Effective" class="small">{{ $version->effective_from?->format('j M Y') ?? '—' }}{{ $version->effective_to ? ' – ' . $version->effective_to->format('j M Y') : '' }}</td>
                        <td data-label="Items">{{ $version->nodes_count }}</td>
                        <td data-label="Source" class="small text-truncate" style="max-width:260px">{{ $version->source_document ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted small">No curriculum versions yet for this level.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endforeach
@endsection
