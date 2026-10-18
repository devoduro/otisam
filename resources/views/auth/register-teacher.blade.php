@extends('layouts.auth')

@section('title', 'Teacher registration')
@section('heading', 'Register as a teacher')
@section('intro', 'An administrator checks every teacher account before teacher resources are unlocked. You can sign in while you wait.')

@section('form')
<form method="POST" action="{{ route('teachers.register.store') }}" class="space-y-4">
    @csrf
    <x-input name="name" label="Full name" required autocomplete="name" maxlength="120" />
    <x-input name="phone" type="tel" label="Mobile number" required autocomplete="tel" inputmode="tel" placeholder="024 123 4567" />
    <x-input name="email" type="email" label="Email" required autocomplete="email" />
    <x-input name="password" type="password" label="Password" required autocomplete="new-password" hint="At least 8 characters, with letters and numbers." />
    <x-input name="password_confirmation" type="password" label="Confirm password" required autocomplete="new-password" />

    <hr class="border-gray-200">

    <x-input name="school_name" label="School" required maxlength="150" />
    <x-select name="region" label="Region" :options="config('platform.regions')" required />
    <x-input name="district" label="District" maxlength="80" />
    <x-input name="subjects_taught" label="Subjects you teach" required maxlength="255" placeholder="e.g. Core Mathematics, Physics" />
    <x-input name="staff_number" label="GES staff number" maxlength="30" hint="Helps us verify you faster." />
    <x-input name="current_rank" label="Current rank" maxlength="80" placeholder="e.g. Principal Superintendent" />

    <label class="flex items-start gap-2 text-sm">
        <input type="checkbox" name="declaration" value="1" required class="mt-0.5 h-5 w-5 rounded border-gray-300" @checked(old('declaration'))>
        <span>The information I have given is accurate, and I agree to the <a href="{{ route('terms') }}" target="_blank">terms</a> and <a href="{{ route('privacy') }}" target="_blank">privacy policy</a>.</span>
    </label>
    @error('declaration')<p class="text-sm font-medium text-red-700">{{ $message }}</p>@enderror

    <x-button full>Submit registration</x-button>
</form>
@endsection

@section('after')
<p class="mt-6 text-center text-sm text-muted">Already registered? <a href="{{ route('login') }}" class="font-semibold">Sign in</a></p>
@endsection
