@extends('layouts.admin')

@section('title', 'Students')

@section('content')
<form method="GET" class="card-panel mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="q" class="form-label small fw-semibold">Search</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Name, phone or email">
        </div>
        <div class="col-6 col-md-2">
            <label for="class_level" class="form-label small fw-semibold">Class</label>
            <select id="class_level" name="class_level" class="form-select">
                <option value="">All</option>
                @foreach (config('platform.class_levels') as $level)
                    <option @selected(($filters['class_level'] ?? '') === $level)>{{ $level }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="status" class="form-label small fw-semibold">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">All</option>
                @foreach (['active', 'suspended', 'deactivated'] as $s)
                    <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="verified" class="form-label small fw-semibold">Phone</label>
            <select id="verified" name="verified" class="form-select">
                <option value="">All</option>
                <option value="yes" @selected(($filters['verified'] ?? '') === 'yes')>Verified</option>
                <option value="no" @selected(($filters['verified'] ?? '') === 'no')>Not verified</option>
            </select>
        </div>
        <div class="col-6 col-md-2 d-grid">
            <button class="btn btn-premium" type="submit">Filter</button>
        </div>
    </div>
</form>

<div class="table-panel">
    <table class="table table-hover align-middle table-stack">
        <thead>
            <tr><th>Name</th><th>Phone</th><th>Class</th><th>Programme</th><th>Status</th><th>Joined</th></tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr>
                    <td data-label="Name"><a href="{{ route('admin.students.show', $student) }}" class="fw-semibold">{{ $student->name }}</a></td>
                    <td data-label="Phone">
                        {{ $student->phone }}
                        @if (!$student->phone_verified_at)<span class="badge text-bg-light border">unverified</span>@endif
                    </td>
                    <td data-label="Class">{{ $student->studentProfile?->class_level ?? '—' }}</td>
                    <td data-label="Programme">{{ $student->studentProfile?->programme ?? '—' }}</td>
                    <td data-label="Status"><span class="badge text-bg-{{ $student->isActive() ? 'success' : 'danger' }}">{{ $student->status->label() }}</span></td>
                    <td data-label="Joined">{{ $student->created_at->format('j M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No students match these filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $students->links() }}</div>
@endsection
