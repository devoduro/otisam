@extends('layouts.admin')

@section('title', 'Roles & permissions')

@section('actions')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-premium rounded-pill px-4">New role</a>
@endsection

@section('content')
<div class="table-panel">
    <table class="table table-hover align-middle table-stack">
        <thead><tr><th>Role</th><th>Staff</th><th>Permissions</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
            @foreach ($roles as $role)
                @php $builtIn = array_key_exists($role->name, \App\Support\Permissions::DEFAULT_ROLES); @endphp
                <tr>
                    <td data-label="Role" class="fw-semibold">
                        {{ $role->name }}
                        @if ($builtIn)<span class="badge text-bg-light border">built-in</span>@endif
                    </td>
                    <td data-label="Staff">{{ $role->users_count }}</td>
                    <td data-label="Permissions">{{ $role->name === \App\Support\Permissions::SUPER_ADMIN ? 'All' : $role->permissions_count }}</td>
                    <td data-label="Actions" class="text-end">
                        @if ($role->name !== \App\Support\Permissions::SUPER_ADMIN)
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            @unless ($builtIn)
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="Delete the {{ $role->name }} role?">Delete</button>
                                </form>
                            @endunless
                        @else
                            <span class="small text-muted">Always has every permission</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
