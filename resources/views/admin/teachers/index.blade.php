@extends('layouts.admin')

@section('title', ($filters['verification'] ?? null) === 'pending' ? 'Teacher approval queue' : 'Teachers')

@section('content')
<form method="GET" class="card-panel mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label for="q" class="form-label small fw-semibold">Search</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Name, phone, email, staff number or school">
        </div>
        <div class="col-6 col-md-3">
            <label for="verification" class="form-label small fw-semibold">Verification</label>
            <select id="verification" name="verification" class="form-select">
                <option value="">All</option>
                @foreach (\App\Enums\TeacherVerificationStatus::cases() as $case)
                    <option value="{{ $case->value }}" @selected(($filters['verification'] ?? '') === $case->value)>{{ $case->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="status" class="form-label small fw-semibold">Account</label>
            <select id="status" name="status" class="form-select">
                <option value="">All</option>
                @foreach (['active', 'suspended'] as $s)
                    <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-premium" type="submit">Filter</button>
        </div>
    </div>
</form>

<div class="table-panel">
    <table class="table table-hover align-middle table-stack">
        <thead>
            <tr><th>Name</th><th>School</th><th>Region</th><th>Subjects</th><th>Verification</th><th>Registered</th></tr>
        </thead>
        <tbody>
            @forelse ($teachers as $teacher)
                @php $p = $teacher->teacherProfile; @endphp
                <tr>
                    <td data-label="Name">
                        <a href="{{ route('admin.teachers.show', $teacher) }}" class="fw-semibold">{{ $teacher->name }}</a>
                        @unless ($teacher->isActive())<span class="badge text-bg-danger">suspended</span>@endunless
                    </td>
                    <td data-label="School">{{ $p?->school_name ?? '—' }}</td>
                    <td data-label="Region">{{ $p?->region ?? '—' }}</td>
                    <td data-label="Subjects" class="text-truncate" style="max-width:220px">{{ $p?->subjects_taught ?? '—' }}</td>
                    <td data-label="Verification">
                        @if ($p)<span class="badge text-bg-{{ $p->verification_status->badge() }}">{{ $p->verification_status->label() }}</span>@endif
                    </td>
                    <td data-label="Registered">{{ $teacher->created_at->format('j M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No teachers match these filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $teachers->links() }}</div>
@endsection
