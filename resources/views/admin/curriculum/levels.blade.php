@extends('layouts.admin')

@section('title', 'Levels & classes')
@section('subtitle', 'Education levels and the class/year groups in each')

@section('content')
@php $canManage = auth()->user()->can('curriculum.manage'); @endphp

@foreach ($levels as $level)
    <div class="card-panel mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold mb-1">
                    {{ $level->name }} <span class="text-muted fw-normal">({{ $level->code }})</span>
                    @unless ($level->is_active)<span class="badge text-bg-secondary ms-1">inactive</span>@endunless
                </h2>
                <div class="small text-muted">{{ $level->subjects_count }} subjects · {{ $level->programmes_count }} programmes · {{ $level->versions_count }} curriculum versions</div>
            </div>
            @if ($canManage)
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-level-{{ $level->id }}" aria-expanded="false">Edit level</button>
            @endif
        </div>

        @if ($canManage)
            <form method="POST" action="{{ route('admin.curriculum.levels.update', $level) }}" id="edit-level-{{ $level->id }}" class="collapse border rounded p-3 mb-3">
                @csrf @method('PUT')
                <div class="row g-2 align-items-end">
                    <div class="col-md-5"><label class="form-label small fw-semibold" for="lname-{{ $level->id }}">Name</label><input id="lname-{{ $level->id }}" name="name" value="{{ $level->name }}" class="form-control form-control-sm" required maxlength="60"></div>
                    <div class="col-6 col-md-2"><label class="form-label small fw-semibold" for="lcode-{{ $level->id }}">Code</label><input id="lcode-{{ $level->id }}" name="code" value="{{ $level->code }}" class="form-control form-control-sm" required maxlength="20"></div>
                    <div class="col-6 col-md-2"><label class="form-label small fw-semibold" for="lsort-{{ $level->id }}">Order</label><input id="lsort-{{ $level->id }}" type="number" name="sort" value="{{ $level->sort }}" class="form-control form-control-sm" min="0"></div>
                    <div class="col-6 col-md-2 form-check ms-2 mb-1">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="lactive-{{ $level->id }}" @checked($level->is_active)>
                        <label class="form-check-label small" for="lactive-{{ $level->id }}">Active</label>
                    </div>
                    <div class="col-auto"><button class="btn btn-sm btn-premium" type="submit">Save</button></div>
                </div>
                <div class="form-text">Inactive levels are hidden from registration and public pages but keep their data.</div>
            </form>
        @endif

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-2">
                <thead><tr><th>Class</th><th style="width:110px">Order</th><th style="width:110px">Active</th>@if ($canManage)<th class="text-end" style="width:170px">Actions</th>@endif</tr></thead>
                <tbody>
                    @forelse ($level->classLevels as $class)
                        <tr>
                            @if ($canManage)
                                <td colspan="4" class="p-0">
                                    <form method="POST" action="{{ route('admin.curriculum.classes.update', $class) }}" class="d-flex flex-wrap gap-2 align-items-center p-1">
                                        @csrf @method('PUT')
                                        <label class="visually-hidden" for="cname-{{ $class->id }}">Class name</label>
                                        <input id="cname-{{ $class->id }}" name="name" value="{{ $class->name }}" class="form-control form-control-sm" style="max-width:220px" required maxlength="40">
                                        <label class="visually-hidden" for="csort-{{ $class->id }}">Order</label>
                                        <input id="csort-{{ $class->id }}" type="number" name="sort" value="{{ $class->sort }}" class="form-control form-control-sm" style="width:90px" min="0">
                                        <input type="hidden" name="is_active" value="0">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="cactive-{{ $class->id }}" @checked($class->is_active)>
                                            <label class="form-check-label small" for="cactive-{{ $class->id }}">Active</label>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary ms-auto" type="submit">Save</button>
                                        <button class="btn btn-sm btn-outline-danger" type="submit" form="del-class-{{ $class->id }}" data-confirm="Delete {{ $class->name }}?">Delete</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.curriculum.classes.destroy', $class) }}" id="del-class-{{ $class->id }}">@csrf @method('DELETE')</form>
                                </td>
                            @else
                                <td>{{ $class->name }}</td><td>{{ $class->sort }}</td><td>{{ $class->is_active ? 'Yes' : 'No' }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted small">No classes yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($canManage)
            <form method="POST" action="{{ route('admin.curriculum.classes.store', $level) }}" class="d-flex flex-wrap gap-2 align-items-center">
                @csrf
                <label class="visually-hidden" for="newclass-{{ $level->id }}">New class name</label>
                <input id="newclass-{{ $level->id }}" name="name" class="form-control form-control-sm" style="max-width:220px" placeholder="New class, e.g. SHS 4" required maxlength="40">
                <button class="btn btn-sm btn-outline-primary" type="submit">Add class</button>
            </form>
        @endif
    </div>
@endforeach

@if ($canManage)
    <form method="POST" action="{{ route('admin.curriculum.levels.store') }}" class="card-panel">
        @csrf
        <h2 class="h6 fw-bold mb-3">Add an education level</h2>
        <div class="row g-2 align-items-end">
            <div class="col-md-5"><label class="form-label small fw-semibold" for="new-level-name">Name</label><input id="new-level-name" name="name" value="{{ old('name') }}" class="form-control" placeholder="e.g. Upper Primary" required maxlength="60"></div>
            <div class="col-6 col-md-2"><label class="form-label small fw-semibold" for="new-level-code">Code</label><input id="new-level-code" name="code" value="{{ old('code') }}" class="form-control" placeholder="UP" required maxlength="20"></div>
            <div class="col-6 col-md-2"><label class="form-label small fw-semibold" for="new-level-sort">Order</label><input id="new-level-sort" type="number" name="sort" value="{{ old('sort', 0) }}" class="form-control" min="0"></div>
            <div class="col-md-3 d-grid"><button class="btn btn-premium" type="submit">Add level</button></div>
        </div>
    </form>
@endif
@endsection
