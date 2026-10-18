@extends('layouts.portal')

@section('title', 'My teacher profile')
@section('subtitle', $profile->isComplete() ? 'Changes to your school, region or subjects go back to the administrator for review if your account is still being checked.' : 'Add your name, school and subjects so an administrator can verify you.')

@section('actions')
    @if ($profile->isListed())
        <x-button variant="outline" :href="route('teachers.show', $profile->slug)" icon="fa-eye" target="_blank">View public profile</x-button>
    @endif
@endsection

@section('content')
@php
    $user = auth()->user();
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]';
@endphp
<form method="POST" enctype="multipart/form-data" action="{{ route('teach.profile.update') }}" class="max-w-3xl space-y-6" data-auth-form>
    @csrf
    @method('PUT')

    <section class="panel space-y-5 p-6">
        <h2 class="font-bold text-gray-800">Your details <span class="text-sm font-normal text-gray-500">(checked by an administrator)</span></h2>
        <div class="grid gap-5 sm:grid-cols-2">
            <x-input name="name" label="Full name" required maxlength="120" autocomplete="name" :value="str_starts_with($user->name, 'Teacher ') && ! $profile->isComplete() ? '' : $user->name" />
            <x-input name="email" type="email" label="Email" maxlength="150" autocomplete="email" :value="$user->email" />
            <x-input name="school_name" label="School" required maxlength="150" :value="$profile->school_name" />
            <x-select name="region" label="Region" :options="config('platform.regions')" required :value="$profile->region" />
            <x-input name="district" label="District" maxlength="80" :value="$profile->district" />
            <x-input name="subjects_taught" label="Subjects you teach" required maxlength="255" :value="$profile->subjects_taught" placeholder="e.g. Core Mathematics, Physics" />
            <x-input name="staff_number" label="GES staff number" maxlength="30" :value="$profile->staff_number" hint="Helps us verify you faster. Never shown publicly." />
            <x-input name="current_rank" label="Current rank" maxlength="80" :value="$profile->current_rank" />
        </div>
    </section>

    <section class="panel space-y-5 p-6" aria-labelledby="public-heading">
        <div>
            <h2 id="public-heading" class="font-bold text-gray-800">Public profile</h2>
            <p class="text-sm text-gray-500">Appear on the <a href="{{ route('teachers.index') }}" target="_blank">Teachers</a> page with your school, subjects, video tutorials and public materials. Your phone number, email and staff number are never shown.</p>
        </div>

        <label class="flex items-start gap-3 rounded-xl border-[1.5px] border-[#e2e0ea] p-3 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
            <input type="hidden" name="is_public" value="0">
            <input type="checkbox" name="is_public" value="1" class="mt-1 h-4 w-4 accent-[#28409e]" @checked(old('is_public', $profile->is_public))>
            <span><span class="block font-semibold text-gray-800">Show my profile on the Teachers page</span>
                <span class="text-sm text-gray-500">{{ $profile->isApproved() ? 'You can switch this off at any time.' : 'Your profile appears once your account is approved.' }}</span></span>
        </label>

        <div class="flex flex-wrap items-center gap-5">
            @if ($profile->photoUrl())
                <img src="{{ $profile->photoUrl() }}" alt="Your photo" class="h-20 w-20 rounded-full object-cover ring-2 ring-gold-400">
            @else
                <span class="flex h-20 w-20 items-center justify-center rounded-full bg-primary-600 font-display text-2xl font-extrabold text-white">{{ $profile->initials() }}</span>
            @endif
            <div class="min-w-0 flex-1">
                <label for="photo" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Photo <span class="font-normal text-gray-500">(optional, JPG/PNG, up to 4 MB)</span></label>
                <input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:font-semibold file:text-primary-700">
                @error('photo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @if ($profile->photo_path)<label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-600"><input type="checkbox" name="remove_photo" value="1" class="accent-[#28409e]"> Remove photo</label>@endif
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-3">
            <div class="sm:col-span-2"><x-input name="headline" label="Headline" maxlength="120" :value="$profile->headline" placeholder="e.g. Physics teacher and WASSCE examiner" /></div>
            <x-input name="years_teaching" type="number" label="Years teaching" min="0" max="60" :value="$profile->years_teaching" />
        </div>
        <div>
            <label for="bio" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">About you <span class="font-normal text-gray-500">(optional)</span></label>
            <textarea id="bio" name="bio" rows="4" maxlength="2000" class="{{ $control }}" placeholder="Your teaching experience, what you help students with…">{{ old('bio', $profile->bio) }}</textarea>
        </div>
    </section>

    <div class="flex gap-3">
        <x-button icon="fa-save">Save profile</x-button>
        <x-button variant="outline" :href="route('teach.dashboard')">Cancel</x-button>
    </div>
</form>
@endsection
