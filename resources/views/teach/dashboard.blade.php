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
                @if ($profile->school_name)<span><i class="fas fa-school"></i> {{ $profile->school_name }}</span>@endif
                @if ($profile->region)<span><i class="fas fa-location-dot"></i> {{ $profile->region }}</span>@endif
                <span><i class="fas fa-shield-halved"></i> {{ $status->label() }}</span>
            </x-slot:chips>
            <a class="primary" href="{{ route('library.index') }}"><i class="fas fa-book-open"></i> Library</a>
            <a href="{{ route('teach.profile.edit') }}"><i class="fas fa-id-badge"></i> My profile</a>
        </x-dashboard-hero>
    </div>

    @unless ($profile->isComplete())
    <div class="md:col-span-3 flex flex-wrap items-center justify-between gap-3 rounded-md bg-primary-50 p-4 ring-1 ring-primary-200 text-primary-800" role="status" data-profile-incomplete>
        <div class="flex items-start gap-3">
            <i class="fas fa-user-pen mt-0.5" aria-hidden="true"></i>
            <div>
                <p class="font-semibold">Complete your profile</p>
                <p class="text-sm">Add your name, school and the subjects you teach so an administrator can verify you.</p>
            </div>
        </div>
        <x-button :href="route('teach.profile.edit')" icon="fa-arrow-right">Complete profile</x-button>
    </div>
    @endunless

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

    @if ($status === V::Approved)
        <div class="md:col-span-3 grid gap-4 sm:grid-cols-3">
            <x-stat-tile color="blue" icon="fa-people-group" title="My classes" :sub="$stats['students'].' '.\Illuminate\Support\Str::plural('student', $stats['students'])" :value="$stats['classes']" :href="route('teach.classes.index')" />
            <x-stat-tile color="orange" icon="fa-list-check" title="To mark" sub="Handed-in work" :value="$stats['to_mark']" :href="route('teach.classes.index')" />
            <x-stat-tile color="green" icon="fa-folder-open" title="Lesson materials" sub="Shared by teachers" :value="$stats['shared_materials']" :href="route('teach.materials.index')" />
        </div>
    @endif

    {{-- Invite colleagues --}}
    <div class="md:col-span-3 relative overflow-hidden rounded-2xl bg-primary-700 bg-crosshatch p-5 text-white shadow-lift" data-referral>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="eyebrow">Invite colleagues</p>
                <h2 class="mt-1 text-xl font-extrabold">Share {{ config('app.name') }} with other teachers</h2>
                <p class="mt-1 text-sm text-white/75">Send your link to colleagues. When they register as teachers with it, they show here.</p>
            </div>
            <div class="shrink-0 text-sm"><span class="font-display text-3xl font-extrabold">{{ $referrals['total'] }}</span> joined · <span class="font-semibold">{{ $referrals['approved'] }}</span> approved</div>
        </div>
        <div class="mt-4 flex flex-col gap-2 sm:flex-row">
            <label for="referral-link" class="sr-only">Your invite link</label>
            <input id="referral-link" readonly value="{{ $referralLink }}" class="min-w-0 flex-1 rounded-xl border-0 bg-white px-4 py-2.5 font-mono text-sm text-primary-900" onclick="this.select()">
            <button type="button" class="rounded-xl bg-gold-500 px-4 py-2.5 text-sm font-bold text-primary-950 shadow-gold hover:bg-gold-400" onclick="navigator.clipboard?.writeText(document.getElementById('referral-link').value); this.textContent='Copied!';"><i class="fas fa-copy mr-1" aria-hidden="true"></i>Copy link</button>
            <a href="https://wa.me/?text={{ rawurlencode('Join me on '.config('app.name').': lesson plans, past questions and class quizzes for Ghanaian teachers. Register here: '.$referralLink) }}" target="_blank" rel="noopener" class="rounded-xl bg-[#25D366] px-4 py-2.5 text-center text-sm font-bold text-white no-underline hover:text-white"><i class="fab fa-whatsapp mr-1" aria-hidden="true"></i>WhatsApp</a>
        </div>
    </div>

    <div class="md:col-span-2 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]">
        <h2 class="mb-3 text-sm font-semibold text-gray-700">My profile</h2>
        <dl class="grid gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
            <div><dt class="text-gray-500">School</dt><dd class="font-medium text-gray-800">{{ $profile->school_name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Region / district</dt><dd class="font-medium text-gray-800">{{ $profile->region ?? '—' }}{{ $profile->district ? ' · '.$profile->district : '' }}</dd></div>
            <div><dt class="text-gray-500">Subjects</dt><dd class="font-medium text-gray-800">{{ $profile->subjects_taught ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">GES staff number</dt><dd class="font-medium text-gray-800">{{ $profile->staff_number ?? '—' }}</dd></div>
        </dl>
    </div>

    <div class="rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]">
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
