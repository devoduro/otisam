@extends('layouts.admin')

@section('title', $version->exists ? 'Edit version: ' . $version->name : 'New curriculum version')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <form method="POST" action="{{ $version->exists ? route('admin.curriculum.versions.update', $version) : route('admin.curriculum.versions.store') }}" class="card-panel">
            @csrf
            @if ($version->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="education_level_id" class="form-label fw-semibold">Education level</label>
                    <select id="education_level_id" name="education_level_id" class="form-select" required>
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}" @selected((int) old('education_level_id', $version->education_level_id) === $level->id)>{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        @foreach (\App\Models\Curriculum\CurriculumVersion::STATUSES as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $version->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label for="name" class="form-label fw-semibold">Name</label>
                    <input id="name" name="name" value="{{ old('name', $version->name) }}" class="form-control" required maxlength="120" placeholder="Name it as the official document does">
                </div>
                <div class="col-md-6">
                    <label for="effective_from" class="form-label fw-semibold">Effective from</label>
                    <input id="effective_from" type="date" name="effective_from" value="{{ old('effective_from', $version->effective_from?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="effective_to" class="form-label fw-semibold">Effective until <span class="text-muted fw-normal">(optional)</span></label>
                    <input id="effective_to" type="date" name="effective_to" value="{{ old('effective_to', $version->effective_to?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label for="source_document" class="form-label fw-semibold">Official source document</label>
                    <input id="source_document" name="source_document" value="{{ old('source_document', $version->source_document) }}" class="form-control" maxlength="500" placeholder="Title, publisher (NaCCA/GES), year, and where it can be obtained">
                    <div class="form-text">Required before the version can be made active.</div>
                </div>
                <div class="col-12">
                    <label for="notes" class="form-label fw-semibold">Notes <span class="text-muted fw-normal">(internal)</span></label>
                    <textarea id="notes" name="notes" rows="3" maxlength="2000" class="form-control">{{ old('notes', $version->notes) }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-premium" type="submit">{{ $version->exists ? 'Save version' : 'Create version' }}</button>
                <a href="{{ $version->exists ? route('admin.curriculum.versions.show', $version) : route('admin.curriculum.versions.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
    @if ($version->exists)
        <div class="col-lg-4">
            <div class="card-panel">
                <h2 class="h6 fw-bold mb-2">Delete</h2>
                <p class="small text-muted">Only possible while the version has no topics. A version that was used should be retired instead, so its history stays.</p>
                <form method="POST" action="{{ route('admin.curriculum.versions.destroy', $version) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" type="submit" data-confirm="Delete {{ $version->name }}?">Delete version</button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
