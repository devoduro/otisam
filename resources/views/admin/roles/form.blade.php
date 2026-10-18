@extends('layouts.admin')

@section('title', $role->exists ? 'Edit role: ' . $role->name : 'New role')

@section('content')
@php
    use App\Support\Permissions;
    $checked = old('permissions', $rolePermissions);
    $builtIn = $role->exists && array_key_exists($role->name, Permissions::DEFAULT_ROLES);
@endphp
<form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
    @csrf
    @if ($role->exists) @method('PUT') @endif

    <div class="card-panel mb-3">
        <label for="name" class="form-label fw-semibold">Role name</label>
        <input id="name" name="name" value="{{ old('name', $role->name) }}" class="form-control" maxlength="60" required @readonly($builtIn)>
        @if ($builtIn)<div class="form-text">Built-in roles cannot be renamed.</div>@endif
    </div>

    <div class="row g-3">
        @foreach (Permissions::GROUPS as $group => $permissions)
            @php $available = array_diff_key($permissions, array_flip(Permissions::SUPER_ADMIN_ONLY)); @endphp
            @continue(empty($available))
            <div class="col-md-6 col-xl-4">
                <fieldset class="card-panel h-100">
                    <legend class="h6 fw-bold">{{ $group }}</legend>
                    @foreach ($available as $permission => $description)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission }}" id="perm-{{ \Illuminate\Support\Str::slug($permission) }}" @checked(in_array($permission, $checked, true))>
                            <label class="form-check-label small" for="perm-{{ \Illuminate\Support\Str::slug($permission) }}">{{ $description }}</label>
                        </div>
                    @endforeach
                </fieldset>
            </div>
        @endforeach
    </div>

    <p class="small text-muted mt-3">Managing staff accounts, roles, integrations and maintenance stays with the Super Admin and cannot be given to other roles.</p>

    <div class="d-flex gap-2">
        <button class="btn btn-premium" type="submit">{{ $role->exists ? 'Save role' : 'Create role' }}</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
    </div>
</form>
@endsection
