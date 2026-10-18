@extends('layouts.admin')

@section('title', 'Library settings')

@section('content')
<div class="card-panel mb-3">
    <h2 class="h6 fw-bold mb-2">Preview length</h2>
    <form method="POST" action="{{ route('admin.library.settings.preview') }}" class="d-flex flex-wrap gap-2 align-items-end">
        @csrf @method('PUT')
        <div>
            <label for="preview_pages" class="form-label small fw-semibold">Pages shown to people without full access</label>
            <input id="preview_pages" type="number" name="preview_pages" value="{{ $previewPages }}" min="1" max="20" class="form-control" style="width:120px" required>
        </div>
        <button class="btn btn-premium" type="submit">Save</button>
    </form>
    <p class="small text-muted mt-2 mb-0">Each resource can override this. A preview is never more than half of the document.</p>
</div>

<div class="card-panel mb-3">
    <h2 class="h6 fw-bold mb-3">Resource categories</h2>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead><tr><th>Name</th><th>For</th><th>Exam paper</th><th>Order</th><th>Active</th><th class="text-end">Resources / actions</th></tr></thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td colspan="6" class="p-0">
                            <form method="POST" action="{{ route('admin.library.categories.update', $category) }}" class="d-flex flex-wrap gap-2 align-items-center p-1">
                                @csrf @method('PUT')
                                <label class="visually-hidden" for="cat-name-{{ $category->id }}">Name</label>
                                <input id="cat-name-{{ $category->id }}" name="name" value="{{ $category->name }}" class="form-control form-control-sm" style="max-width:240px" required maxlength="80">
                                <label class="visually-hidden" for="cat-aud-{{ $category->id }}">For</label>
                                <select id="cat-aud-{{ $category->id }}" name="audience" class="form-select form-select-sm" style="width:auto">
                                    @foreach (\App\Models\Library\ResourceCategory::AUDIENCES as $v => $l)<option value="{{ $v }}" @selected($category->audience === $v)>{{ $l }}</option>@endforeach
                                </select>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_exam_paper" value="1" id="cat-exam-{{ $category->id }}" @checked($category->is_exam_paper)>
                                    <label class="form-check-label small" for="cat-exam-{{ $category->id }}">Exam paper</label>
                                </div>
                                <label class="visually-hidden" for="cat-sort-{{ $category->id }}">Order</label>
                                <input id="cat-sort-{{ $category->id }}" type="number" name="sort" value="{{ $category->sort }}" class="form-control form-control-sm" style="width:80px" min="0">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="cat-active-{{ $category->id }}" @checked($category->is_active)>
                                    <label class="form-check-label small" for="cat-active-{{ $category->id }}">Active</label>
                                </div>
                                <span class="small text-muted ms-auto">{{ $category->resources_count }}</span>
                                <button class="btn btn-sm btn-outline-primary" type="submit">Save</button>
                                @if ($category->resources_count === 0)
                                    <button class="btn btn-sm btn-outline-danger" type="submit" form="del-cat-{{ $category->id }}" data-confirm="Delete {{ $category->name }}?">Delete</button>
                                @endif
                            </form>
                            <form method="POST" action="{{ route('admin.library.categories.destroy', $category) }}" id="del-cat-{{ $category->id }}">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <form method="POST" action="{{ route('admin.library.categories.store') }}" class="d-flex flex-wrap gap-2 align-items-center mt-2">
        @csrf
        <label class="visually-hidden" for="new-cat">New category</label>
        <input id="new-cat" name="name" class="form-control form-control-sm" style="max-width:240px" placeholder="New category" required maxlength="80">
        <label class="visually-hidden" for="new-cat-aud">For</label>
        <select id="new-cat-aud" name="audience" class="form-select form-select-sm" style="width:auto">
            @foreach (\App\Models\Library\ResourceCategory::AUDIENCES as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
        </select>
        <div class="form-check mb-0"><input class="form-check-input" type="checkbox" name="is_exam_paper" value="1" id="new-cat-exam"><label class="form-check-label small" for="new-cat-exam">Exam paper</label></div>
        <button class="btn btn-sm btn-outline-primary" type="submit">Add category</button>
    </form>
</div>

<div class="card-panel">
    <h2 class="h6 fw-bold mb-3">Exam types</h2>
    @foreach ($examTypes as $type)
        <form method="POST" action="{{ route('admin.library.exam-types.update', $type) }}" class="d-flex flex-wrap gap-2 align-items-center border-bottom py-1">
            @csrf @method('PUT')
            <code class="small" style="width:100px">{{ $type->code }}</code>
            <label class="visually-hidden" for="et-name-{{ $type->id }}">Name</label>
            <input id="et-name-{{ $type->id }}" name="name" value="{{ $type->name }}" class="form-control form-control-sm" style="max-width:260px" required maxlength="80">
            <label class="visually-hidden" for="et-sort-{{ $type->id }}">Order</label>
            <input id="et-sort-{{ $type->id }}" type="number" name="sort" value="{{ $type->sort }}" class="form-control form-control-sm" style="width:80px" min="0">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="et-active-{{ $type->id }}" @checked($type->is_active)>
                <label class="form-check-label small" for="et-active-{{ $type->id }}">Active</label>
            </div>
            <button class="btn btn-sm btn-outline-primary ms-auto" type="submit">Save</button>
        </form>
    @endforeach
    <form method="POST" action="{{ route('admin.library.exam-types.store') }}" class="d-flex flex-wrap gap-2 align-items-center mt-3">
        @csrf
        <label class="visually-hidden" for="new-et-code">Code</label>
        <input id="new-et-code" name="code" class="form-control form-control-sm" style="width:120px" placeholder="CODE" required maxlength="20">
        <label class="visually-hidden" for="new-et-name">Name</label>
        <input id="new-et-name" name="name" class="form-control form-control-sm" style="max-width:260px" placeholder="New exam type" required maxlength="80">
        <button class="btn btn-sm btn-outline-primary" type="submit">Add exam type</button>
    </form>
    <p class="small text-muted mt-2 mb-0">Codes are fixed once created. Papers of {{ implode(', ', \App\Http\Controllers\Admin\Library\ResourceController::WAEC_EXAM_CODES) }} can only be published free.</p>
</div>
@endsection
