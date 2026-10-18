@extends('layouts.portal')

@section('title', 'Teacher dashboard')
@section('hide_heading', '1')

@section('content')
@php
    use App\Enums\TeacherVerificationStatus as V;
    $status = $profile->verification_status;
    [$tone, $icon, $heading, $text] = match ($status) {
        V::Pending => ['bg-yellow-50 ring-yellow-200 text-yellow-800', 'fa-hourglass-half', 'Your registration is being reviewed', 'An administrator will check your details. We will text and email you when there is a decision.'],
        V::MoreInfo => ['bg-primary-50 ring-primary-200 text-primary-800', 'fa-circle-info', 'We need a little more information', $profile->decision_note ?: 'Please update your profile so we can finish checking your account.'],
        V::Rejected => ['bg-red-50 ring-red-200 text-red-800', 'fa-circle-xmark', 'Your registration was not approved', $profile->decision_note ?: 'Contact support if you think this is a mistake.'],
        V::Approved => ['bg-green-50 ring-green-200 text-green-800', 'fa-circle-check', 'Your teacher account is approved', 'Teacher resources will appear in the library as they are published.'],
    };
@endphp
<div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    <div class="md:col-span-3">
        <x-dashboard-hero :title="'Welcome, '.$user->name" :eyebrow="now()->format('l, j F Y')" subtitle="Teaching resources, question writing and professional assessments, in one place.">
            <x-slot:chips>
                <span><i class="fas fa-school"></i> {{ $profile->school_name }}</span>
                <span><i class="fas fa-location-dot"></i> {{ $profile->region }}</span>
                <span><i class="fas fa-shield-halved"></i> {{ $status->label() }}</span>
            </x-slot:chips>
            <a class="primary" href="{{ route('library.index') }}"><i class="fas fa-book-open text-[#42a5dc]"></i> Library</a>
            <a href="{{ route('teach.profile.edit') }}"><i class="fas fa-id-badge"></i> My profile</a>
        </x-dashboard-hero>
    </div>

    <div class="md:col-span-3 flex items-start gap-3 rounded-md p-4 ring-1 {{ $tone }}" role="status">
        <i class="fas {{ $icon }} mt-0.5" aria-hidden="true"></i>
        <div>
            <p class="font-semibold">{{ $heading }}</p>
            <p class="text-sm">{{ $text }}</p>
            @if (in_array($status, [V::MoreInfo, V::Pending, V::Rejected], true))
                <a href="{{ route('teach.profile.edit') }}" class="mt-2 inline-block text-sm font-semibold">Update my profile</a>
            @endif
        </div>
    </div>

    <div class="md:col-span-2 rounded-md bg-white p-4 shadow-sm ring-1 ring-gray-100">
        <h2 class="mb-3 text-sm font-semibold text-gray-700">My profile</h2>
        <dl class="grid gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
            <div><dt class="text-gray-500">School</dt><dd class="font-medium text-gray-800">{{ $profile->school_name }}</dd></div>
            <div><dt class="text-gray-500">Region / district</dt><dd class="font-medium text-gray-800">{{ $profile->region }}{{ $profile->district ? ' · '.$profile->district : '' }}</dd></div>
            <div><dt class="text-gray-500">Subjects</dt><dd class="font-medium text-gray-800">{{ $profile->subjects_taught }}</dd></div>
            <div><dt class="text-gray-500">GES staff number</dt><dd class="font-medium text-gray-800">{{ $profile->staff_number ?? '—' }}</dd></div>
        </dl>
    </div>

    <div class="rounded-md bg-white p-4 shadow-sm ring-1 ring-gray-100">
        <h2 class="mb-3 text-sm font-semibold text-gray-700">What you can do</h2>
        @if ($status === V::Approved && count($capabilities))
            <ul class="space-y-2 text-sm">
                @foreach ($capabilities as $capability)
                    <li class="flex gap-2"><i class="fas fa-check mt-1 text-green-600" aria-hidden="true"></i>{{ \Illuminate\Support\Str::after(\App\Support\Permissions::describe($capability), 'Teacher: ') }}</li>
                @endforeach
            </ul>
        @elseif ($status === V::Approved)
            <p class="text-sm text-gray-500">Browse and download teacher resources. Ask an administrator if you would like to contribute questions, resources or tutorials.</p>
        @else
            <p class="text-sm text-gray-500">Teacher tools unlock once your account is approved.</p>
        @endif
    </div>
</div>
@endsection
