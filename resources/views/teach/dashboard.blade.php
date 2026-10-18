@extends('layouts.app')

@section('title', 'Teacher dashboard')

@section('content')
@php
    use App\Enums\TeacherVerificationStatus as V;
    $status = $profile->verification_status;
    $banner = match ($status) {
        V::Pending => ['border-accent-500 bg-amber-50', 'Your registration is being reviewed', 'An administrator will check your details. We will text and email you when there is a decision.'],
        V::MoreInfo => ['border-primary-600 bg-primary-50', 'We need a little more information', $profile->decision_note ?: 'Please update your profile so we can finish checking your account.'],
        V::Rejected => ['border-red-600 bg-red-50', 'Your registration was not approved', $profile->decision_note ?: 'Contact support if you think this is a mistake.'],
        V::Approved => ['border-green-600 bg-green-50', 'Your teacher account is approved', 'Teacher resources will appear here as they are published.'],
    };
@endphp
<div class="mx-auto max-w-6xl px-4 py-6 sm:py-10">
    <x-flash />

    <h1 class="text-2xl font-extrabold text-primary-900 sm:text-3xl">Welcome, {{ $user->name }}</h1>

    <section class="mt-4 rounded-xl border-l-4 p-5 {{ $banner[0] }}" role="status">
        <h2 class="font-bold">{{ $banner[1] }}</h2>
        <p class="mt-1 text-sm">{{ $banner[2] }}</p>
        @if (in_array($status, [V::MoreInfo, V::Pending, V::Rejected], true))
            <a href="{{ route('teach.profile.edit') }}" class="mt-3 inline-block text-sm font-semibold">Update my profile</a>
        @endif
    </section>

    <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="font-bold text-primary-900">My profile</h2>
            <dl class="mt-2 space-y-1 text-sm">
                <div class="flex justify-between gap-2"><dt class="text-muted">School</dt><dd class="text-right">{{ $profile->school_name }}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-muted">Region</dt><dd>{{ $profile->region }}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-muted">Subjects</dt><dd class="text-right">{{ $profile->subjects_taught }}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-muted">Staff number</dt><dd>{{ $profile->staff_number ?? '—' }}</dd></div>
            </dl>
            <a href="{{ route('teach.profile.edit') }}" class="mt-3 inline-block text-sm font-semibold">Edit profile</a>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="font-bold text-primary-900">What you can do</h2>
            @if ($status === V::Approved && count($capabilities))
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                    @foreach ($capabilities as $capability)
                        <li>{{ \Illuminate\Support\Str::after(\App\Support\Permissions::describe($capability), 'Teacher: ') }}</li>
                    @endforeach
                </ul>
            @elseif ($status === V::Approved)
                <p class="mt-1 text-sm text-muted">Browse and download teacher resources. Ask an administrator if you would like to contribute questions, resources or tutorials.</p>
            @else
                <p class="mt-1 text-sm text-muted">Teacher tools unlock once your account is approved.</p>
            @endif
        </div>
    </div>
</div>
@endsection
