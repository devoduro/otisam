@extends('layouts.admin')

@section('title', 'Subjects')

@section('actions')
    @can('curriculum.manage')
        <a href="{{ route('admin.curriculum.subjects.create', ['level' => $filters['level'] ?? null]) }}" class="btn btn-premium rounded-pill px-4">Add subject</a>
    @endcan
@endsection

@section('content')
<form method="GET" class="card-panel mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label for="q" class="form-label small fw-semibold">Search</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Name or code">
        </div>
        <div class="col-md-4">
            <label for="level" class="form-label small fw-semibold">Level</label>
            <select id="level" name="level" class="form-select">
                <option value="">All levels</option>
                @foreach ($levels as $level)
                    <option value="{{ $level->id }}" @selected((int) ($filters['level'] ?? 0) === $level->id)>{{ $level->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-grid"><button class="btn btn-premium" type="submit">Filter</button></div>
    </div>
</form>

<div class="table-panel">
    <table class="table table-hover align-middle table-stack">
        <thead><tr><th>Subject</th><th>Level</th><th>Category</th><th>Programmes</th><th>Topics</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
            @forelse ($subjects as $subject)
                <tr>
                    <td data-label="Subject" class="fw-semibold">{{ $subject->name }} @if ($subject->code)<span class="text-muted small">{{ $subject->code }}</span>@endif</td>
                    <td data-label="Level">{{ $subject->educationLevel->code }}</td>
                    <td data-label="Category">{{ \App\Models\Curriculum\Subject::CATEGORIES[$subject->category] ?? $subject->category }}</td>
                    <td data-label="Programmes">{{ $subject->programmes_count }}</td>
                    <td data-label="Topics">{{ $subject->nodes_count }}</td>
                    <td data-label="Status"><span class="badge text-bg-{{ $subject->is_active ? 'success' : 'secondary' }}">{{ $subject->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td data-label="Actions" class="text-end">
                        @can('curriculum.manage')
                            <a href="{{ route('admin.curriculum.subjects.edit', $subject) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No subjects match.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $subjects->links() }}</div>
@endsection
