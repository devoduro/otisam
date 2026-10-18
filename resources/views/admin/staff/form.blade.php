@extends('layouts.admin')

@section('title', $member->exists ? 'Edit ' . $member->name : 'Add staff')

@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <form method="POST" action="{{ $member->exists ? route('admin.staff.update', $member) : route('admin.staff.store') }}" class="card-panel">
            @csrf
            @if ($member->exists) @method('PUT') @endif

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Full name</label>
                <input id="name" name="name" value="{{ old('name', $member->name) }}" class="form-control" required maxlength="120">
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $member->email) }}" class="form-control" maxlength="150">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">Mobile number</label>
                    <input id="phone" type="tel" inputmode="tel" name="phone" value="{{ old('phone', $member->phone) }}" class="form-control" placeholder="024 123 4567">
                </div>
                <div class="col-12 form-text mt-1">At least one of email or phone. The phone is used for password-reset codes.</div>
            </div>

            <fieldset class="mb-3">
                <legend class="form-label fw-semibold">Roles</legend>
                @foreach ($roles as $role)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role-{{ $role->id }}" @checked(in_array($role->name, old('roles', $memberRoles), true))>
                        <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                    </div>
                @endforeach
            </fieldset>

            @unless ($member->exists)
                <p class="small text-muted">A temporary password is generated and shown to you once. The new staff member must change it at first sign-in.</p>
            @endunless

            <div class="d-flex gap-2">
                <button class="btn btn-premium" type="submit">{{ $member->exists ? 'Save changes' : 'Create account' }}</button>
                <a href="{{ route('admin.staff.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>

    @if ($member->exists)
        <div class="col-lg-5">
            <div class="card-panel mb-3">
                <h2 class="h6 fw-bold mb-2">Password</h2>
                <p class="small text-muted">Issue a new temporary password and sign this person out everywhere.</p>
                <form method="POST" action="{{ route('admin.staff.reset-password', $member) }}">
                    @csrf
                    <button class="btn btn-outline-warning btn-sm" type="submit" data-confirm="Issue a new temporary password for {{ $member->name }}?">Issue temporary password</button>
                </form>
            </div>
            <div class="card-panel">
                <h2 class="h6 fw-bold mb-2">Account</h2>
                <p class="small mb-3">Status: <span class="badge text-bg-{{ $member->isActive() ? 'success' : 'danger' }}">{{ $member->status->label() }}</span></p>
                @include('admin.partials.account-status', ['account' => $member])
            </div>
        </div>
    @endif
</div>
@endsection
