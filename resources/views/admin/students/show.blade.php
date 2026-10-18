@extends('layouts.admin')

@section('title', $student->name)
@section('subtitle', 'Student')

@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-panel">
            <h2 class="h6 fw-bold mb-3">Profile</h2>
            @php $p = $student->studentProfile; @endphp
            <dl class="row mb-0 small">
                <dt class="col-sm-4">Phone</dt><dd class="col-sm-8">{{ $student->phone }} {!! $student->phone_verified_at ? '<span class="badge text-bg-success">verified</span>' : '<span class="badge text-bg-secondary">not verified</span>' !!}</dd>
                <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $student->email ?? '—' }}</dd>
                <dt class="col-sm-4">Class</dt><dd class="col-sm-8">{{ $p?->classLevel?->name ?? '—' }}</dd>
                <dt class="col-sm-4">Programme</dt><dd class="col-sm-8">{{ $p?->programme?->name ?? '—' }}</dd>
                <dt class="col-sm-4">School</dt><dd class="col-sm-8">{{ $p?->school_name ?? '—' }}</dd>
                <dt class="col-sm-4">Region</dt><dd class="col-sm-8">{{ $p?->region ?? '—' }}</dd>
                <dt class="col-sm-4">Under 18</dt><dd class="col-sm-8">{{ $p?->is_minor ? 'Yes' : 'No' }}</dd>
                @if ($p?->guardian_phone)
                    <dt class="col-sm-4">Guardian</dt><dd class="col-sm-8">{{ $p->guardian_name ?? '—' }} · {{ $p->guardian_phone }}</dd>
                @endif
                <dt class="col-sm-4">Joined</dt><dd class="col-sm-8">{{ $student->created_at->format('j M Y, g:ia') }}</dd>
                <dt class="col-sm-4">Last sign-in</dt><dd class="col-sm-8">{{ $student->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-panel">
            <h2 class="h6 fw-bold mb-2">Account</h2>
            <p class="small mb-3">Status: <span class="badge text-bg-{{ $student->isActive() ? 'success' : 'danger' }}">{{ $student->status->label() }}</span></p>
            @include('admin.partials.account-status', ['account' => $student])
        </div>
    </div>
</div>
@endsection
