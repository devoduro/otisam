@extends('layouts.admin')

@section('title', 'Staff accounts')

@section('actions')
    <a href="{{ route('admin.staff.create') }}" class="btn btn-premium rounded-pill px-4">Add staff</a>
@endsection

@section('content')
<div class="table-panel">
    <table class="table table-hover align-middle table-stack">
        <thead>
            <tr><th>Name</th><th>Contact</th><th>Roles</th><th>Status</th><th>Last sign-in</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($staff as $member)
                <tr>
                    <td data-label="Name" class="fw-semibold">{{ $member->name }} @if ($member->is(auth()->user()))<span class="badge text-bg-light border">you</span>@endif</td>
                    <td data-label="Contact" class="small">{{ $member->email }}<br>{{ $member->phone }}</td>
                    <td data-label="Roles">@foreach ($member->roles as $role)<span class="badge text-bg-primary me-1">{{ $role->name }}</span>@endforeach</td>
                    <td data-label="Status"><span class="badge text-bg-{{ $member->isActive() ? 'success' : 'danger' }}">{{ $member->status->label() }}</span></td>
                    <td data-label="Last sign-in" class="small">{{ $member->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td data-label="Actions" class="text-end">
                        <a href="{{ route('admin.staff.edit', $member) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No staff accounts.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $staff->links() }}</div>
@endsection
