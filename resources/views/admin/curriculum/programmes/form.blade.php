@extends('layouts.admin')

@section('title', $programme->exists ? 'Edit programme: ' . $programme->name : 'Add programme')

@section('content')
<form method="POST" action="{{ $programme->exists ? route('admin.curriculum.programmes.update', $programme) : route('admin.curriculum.programmes.store') }}">
    @csrf
    @if ($programme->exists) @method('PUT') @endif

    <div class="card-panel mb-3">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="education_level_id" class="form-label fw-semibold">Education level</label>
                <select id="education_level_id" name="education_level_id" class="form-select" required>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" @selected((int) old('education_level_id', $programme->education_level_id) === $level->id)>{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label for="name" class="form-label fw-semibold">Name</label>
                <input id="name" name="name" value="{{ old('name', $programme->name) }}" class="form-control" required maxlength="80">
            </div>
            <div class="col-6 col-md-1">
                <label for="sort" class="form-label fw-semibold">Order</label>
                <input id="sort" type="number" name="sort" value="{{ old('sort', $programme->sort ?? 0) }}" class="form-control" min="0">
            </div>
            <div class="col-6 col-md-2 d-flex align-items-end">
                <input type="hidden" name="is_active" value="0">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $programme->is_active ?? true))>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <div class="col-12">
                <label for="description" class="form-label fw-semibold">Description <span class="text-muted fw-normal">(optional)</span></label>
                <textarea id="description" name="description" rows="2" maxlength="500" class="form-control">{{ old('description', $programme->description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card-panel mb-3">
        <h2 class="h6 fw-bold mb-1">Subjects</h2>
        <p class="small text-muted">Choose which subjects this programme takes and whether each is core or elective for it. Only subjects of the selected level are saved.</p>
        @php $chosen = old('subjects', collect($selected)->map(fn ($core) => $core ? 'core' : 'elective')->all()); @endphp
        @foreach ($levels as $level)
            <fieldset class="mb-3" data-level="{{ $level->id }}">
                <legend class="small fw-bold text-uppercase text-muted">{{ $level->name }}</legend>
                <div class="row g-2">
                    @forelse ($level->subjects as $subject)
                        <div class="col-md-6 col-xl-4">
                            <div class="border rounded p-2 d-flex justify-content-between align-items-center gap-2">
                                <span class="small fw-semibold">{{ $subject->name }}</span>
                                <label class="visually-hidden" for="subj-{{ $subject->id }}">Role of {{ $subject->name }}</label>
                                <select id="subj-{{ $subject->id }}" name="subjects[{{ $subject->id }}]" class="form-select form-select-sm" style="width:auto">
                                    <option value="">Not taken</option>
                                    <option value="core" @selected(($chosen[$subject->id] ?? '') === 'core')>Core</option>
                                    <option value="elective" @selected(($chosen[$subject->id] ?? '') === 'elective')>Elective</option>
                                </select>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 small text-muted">No subjects for this level yet.</div>
                    @endforelse
                </div>
            </fieldset>
        @endforeach
    </div>

    <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-premium" type="submit">{{ $programme->exists ? 'Save programme' : 'Add programme' }}</button>
        <a href="{{ route('admin.curriculum.programmes.index') }}" class="btn btn-light">Cancel</a>
        @if ($programme->exists)
            <button class="btn btn-outline-danger ms-auto" type="submit" form="delete-programme" data-confirm="Delete {{ $programme->name }}?">Delete</button>
        @endif
    </div>
</form>
@if ($programme->exists)
    <form method="POST" action="{{ route('admin.curriculum.programmes.destroy', $programme) }}" id="delete-programme">@csrf @method('DELETE')</form>
@endif

@endsection
