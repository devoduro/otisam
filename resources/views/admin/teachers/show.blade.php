@extends('layouts.admin')

@section('title', $teacher->name)
@section('subtitle', 'Teacher')

@section('content')
@php $p = $teacher->teacherProfile; @endphp
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-panel mb-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h2 class="h6 fw-bold mb-0">Professional details</h2>
                <span class="badge text-bg-{{ $p->verification_status->badge() }}">{{ $p->verification_status->label() }}</span>
            </div>
            @unless ($p->isComplete())
                <div class="alert alert-warning small py-2"><i class="fas fa-triangle-exclamation me-1"></i> Profile incomplete: the teacher has not yet added their school, region or subjects.</div>
            @endunless
            <dl class="row mb-0 small">
                <dt class="col-sm-4">Phone</dt><dd class="col-sm-8">{{ $teacher->phone }} {!! $teacher->phone_verified_at ? '<span class="badge text-bg-success">verified</span>' : '<span class="badge text-bg-secondary">not verified</span>' !!}</dd>
                <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $teacher->email ?? '—' }}</dd>
                <dt class="col-sm-4">Staff number</dt><dd class="col-sm-8">{{ $p->staff_number ?? '—' }}</dd>
                <dt class="col-sm-4">Rank</dt><dd class="col-sm-8">{{ $p->current_rank ?? '—' }}</dd>
                <dt class="col-sm-4">School</dt><dd class="col-sm-8">{{ $p->school_name ?? '—' }}</dd>
                <dt class="col-sm-4">Region / district</dt><dd class="col-sm-8">{{ $p->region ?? '—' }}{{ $p->district ? ' · ' . $p->district : '' }}</dd>
                <dt class="col-sm-4">Subjects</dt><dd class="col-sm-8">{{ $p->subjects_taught ?? '—' }}</dd>
                <dt class="col-sm-4">Registered</dt><dd class="col-sm-8">{{ $teacher->created_at->format('j M Y, g:ia') }}</dd>
                @if ($p->verified_at)
                    <dt class="col-sm-4">Last decision</dt>
                    <dd class="col-sm-8">{{ $p->verification_status->label() }} by {{ $p->verifier?->name ?? 'unknown' }}, {{ $p->verified_at->format('j M Y') }}
                        @if ($p->decision_note)<div class="text-muted">“{{ $p->decision_note }}”</div>@endif
                    </dd>
                @endif
            </dl>
        </div>

        @can('teachers.verify')
            <div class="card-panel">
                <h2 class="h6 fw-bold mb-3">Decision</h2>
                @unless ($teacher->phone_verified_at)
                    <div class="alert alert-warning small">This teacher has not confirmed their phone number yet.</div>
                @endunless
                <form method="POST" action="{{ route('admin.teachers.decide', $teacher) }}">
                    @csrf
                    <fieldset class="mb-3">
                        <legend class="form-label small fw-semibold">Outcome</legend>
                        @foreach (['approved' => 'Approve', 'more_info' => 'Ask for more information', 'rejected' => 'Reject'] as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="decision" id="decision-{{ $value }}" value="{{ $value }}" @checked(old('decision') === $value) required>
                                <label class="form-check-label" for="decision-{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </fieldset>
                    <div class="mb-3">
                        <label for="note" class="form-label small fw-semibold">Message to the teacher <span class="text-muted fw-normal">(required unless approving; sent by SMS and email)</span></label>
                        <textarea id="note" name="note" rows="3" maxlength="500" class="form-control">{{ old('note') }}</textarea>
                    </div>
                    <button class="btn btn-premium" type="submit">Save decision</button>
                </form>
            </div>
        @endcan
    </div>

    <div class="col-lg-5">
        @can('teachers.grant-capabilities')
            <div class="card-panel mb-3">
                <h2 class="h6 fw-bold mb-1">Capabilities</h2>
                <p class="small text-muted">Extra things this teacher may do. Uploads, questions and tutorials still go through review unless stated.</p>
                @if ($p->isApproved())
                    <form method="POST" action="{{ route('admin.teachers.capabilities.update', $teacher) }}">
                        @csrf
                        @method('PUT')
                        @foreach ($capabilities as $capability)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="capabilities[]" value="{{ $capability }}" id="cap-{{ $loop->index }}" @checked(in_array($capability, $granted, true))>
                                <label class="form-check-label small" for="cap-{{ $loop->index }}">{{ \Illuminate\Support\Str::after(\App\Support\Permissions::describe($capability), 'Teacher: ') }}</label>
                            </div>
                        @endforeach
                        <button class="btn btn-outline-primary btn-sm mt-2" type="submit">Save capabilities</button>
                    </form>
                @else
                    <p class="small mb-0">Approve the teacher first.</p>
                @endif
            </div>
        @endcan

        <div class="card-panel">
            <h2 class="h6 fw-bold mb-2">Account</h2>
            <p class="small mb-3">Status: <span class="badge text-bg-{{ $teacher->isActive() ? 'success' : 'danger' }}">{{ $teacher->status->label() }}</span></p>
            @include('admin.partials.account-status', ['account' => $teacher])
        </div>
    </div>
</div>
@endsection
