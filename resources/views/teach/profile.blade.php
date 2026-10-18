@extends('layouts.portal')

@section('title', 'My teacher profile')
@section('subtitle', 'Changes go back to the administrator for review if your account is still being checked.')

@section('content')
<form method="POST" action="{{ route('teach.profile.update') }}" class="max-w-3xl space-y-5 rounded-md bg-white p-6 shadow-sm ring-1 ring-gray-100" data-auth-form>
    @csrf
    @method('PUT')
    <div class="grid gap-5 sm:grid-cols-2">
        <x-input name="school_name" label="School" required maxlength="150" :value="$profile->school_name" />
        <x-select name="region" label="Region" :options="config('platform.regions')" required :value="$profile->region" />
        <x-input name="district" label="District" maxlength="80" :value="$profile->district" />
        <x-input name="subjects_taught" label="Subjects you teach" required maxlength="255" :value="$profile->subjects_taught" />
        <x-input name="staff_number" label="GES staff number" maxlength="30" :value="$profile->staff_number" />
        <x-input name="current_rank" label="Current rank" maxlength="80" :value="$profile->current_rank" />
    </div>
    <div class="flex gap-3">
        <x-button icon="fa-save">Save profile</x-button>
        <x-button variant="outline" :href="route('teach.dashboard')">Cancel</x-button>
    </div>
</form>
@endsection
