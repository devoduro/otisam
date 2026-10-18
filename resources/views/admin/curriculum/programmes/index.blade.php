@extends('layouts.admin')

@section('title', 'Programmes')
@section('subtitle', 'Programmes and pathways, and the subjects each one takes')

@section('actions')
    @can('curriculum.manage')
        <a href="{{ route('admin.curriculum.programmes.create') }}" class="btn btn-premium rounded-pill px-4">Add programme</a>
    @endcan
@endsection

@section('content')
@foreach ($levels as $level)
    @continue($level->programmes->isEmpty() && !$level->is_active)
    <h2 class="h6 fw-bold mt-2 mb-2">{{ $level->name }}</h2>
    <div class="table-panel mb-4">
        <table class="table table-hover align-middle table-stack">
            <thead><tr><th>Programme</th><th>Subjects</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse ($level->programmes as $programme)
                    <tr>
                        <td data-label="Programme" class="fw-semibold">{{ $programme->name }}</td>
                        <td data-label="Subjects">{{ $programme->subjects_count }}</td>
                        <td data-label="Status"><span class="badge text-bg-{{ $programme->is_active ? 'success' : 'secondary' }}">{{ $programme->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td data-label="Actions" class="text-end">
                            @can('curriculum.manage')
                                <a href="{{ route('admin.curriculum.programmes.edit', $programme) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted small">No programmes for this level.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endforeach
@endsection
