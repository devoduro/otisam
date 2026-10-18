@extends('layouts.auth')

@section('title', 'My teacher profile')
@section('heading', 'My teacher profile')
@section('intro', 'Changes go to the administrator for review if your account is still being checked.')

@section('form')
<form method="POST" action="{{ route('teach.profile.update') }}" class="space-y-4">
    @csrf
    @method('PUT')
    <x-input name="school_name" label="School" required maxlength="150" :value="$profile->school_name" />
    <x-select name="region" label="Region" :options="config('platform.regions')" required :value="$profile->region" />
    <x-input name="district" label="District" maxlength="80" :value="$profile->district" />
    <x-input name="subjects_taught" label="Subjects you teach" required maxlength="255" :value="$profile->subjects_taught" />
    <x-input name="staff_number" label="GES staff number" maxlength="30" :value="$profile->staff_number" />
    <x-input name="current_rank" label="Current rank" maxlength="80" :value="$profile->current_rank" />
    <x-button full>Save profile</x-button>
</form>
<p class="mt-4 text-center text-sm"><a href="{{ route('teach.dashboard') }}">Back to dashboard</a></p>
@endsection
