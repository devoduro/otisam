@extends('layouts.admin')

@section('title', $subject->exists ? 'Edit subject: ' . $subject->name : 'Add subject')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <form method="POST" action="{{ $subject->exists ? route('admin.curriculum.subjects.update', $subject) : route('admin.curriculum.subjects.store') }}" class="card-panel">
            @csrf
            @if ($subject->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="education_level_id" class="form-label fw-semibold">Education level</label>
                    <select id="education_level_id" name="education_level_id" class="form-select" required>
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}" @selected((int) old('education_level_id', $subject->education_level_id) === $level->id)>{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="category" class="form-label fw-semibold">Category</label>
                    <select id="category" name="category" class="form-select" required>
                        @foreach (\App\Models\Curriculum\Subject::CATEGORIES as $value => $label)
                            <option value="{{ $value }}" @selected(old('category', $subject->category) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label for="name" class="form-label fw-semibold">Name</label>
                    <input id="name" name="name" value="{{ old('name', $subject->name) }}" class="form-control" required maxlength="100">
                    @if ($subject->exists)<div class="form-text">Public link: /subjects/{{ $subject->slug }} (kept if you rename the subject)</div>@endif
                </div>
                <div class="col-md-4">
                    <label for="code" class="form-label fw-semibold">Code <span class="text-muted fw-normal">(optional)</span></label>
                    <input id="code" name="code" value="{{ old('code', $subject->code) }}" class="form-control" maxlength="20">
                </div>
                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">Short description <span class="text-muted fw-normal">(optional, shown on the public subject page)</span></label>
                    <textarea id="description" name="description" rows="2" maxlength="500" class="form-control">{{ old('description', $subject->description) }}</textarea>
                </div>
                <div class="col-6 col-md-3">
                    <label for="sort" class="form-label fw-semibold">Order</label>
                    <input id="sort" type="number" name="sort" value="{{ old('sort', $subject->sort ?? 0) }}" class="form-control" min="0">
                </div>
                <div class="col-6 col-md-3 d-flex align-items-end">
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $subject->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-premium" type="submit">{{ $subject->exists ? 'Save subject' : 'Add subject' }}</button>
                <a href="{{ route('admin.curriculum.subjects.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
    @if ($subject->exists)
        <div class="col-lg-4">
            <div class="card-panel">
                <h2 class="h6 fw-bold mb-2">Delete</h2>
                <p class="small text-muted">Only possible while the subject has no curriculum topics. Otherwise deactivate it.</p>
                <form method="POST" action="{{ route('admin.curriculum.subjects.destroy', $subject) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" type="submit" data-confirm="Delete {{ $subject->name }}?">Delete subject</button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
