@extends('layouts.auth')

@section('title', 'Teacher registration')
@section('heading', 'Register as a teacher')
@section('intro', 'Just your phone number and a password. You can add your school and subjects after you sign in.')

@section('form')
<form method="POST" action="{{ route('teachers.register.store') }}" class="space-y-5" data-auth-form>
    @csrf
    <x-input name="phone" type="tel" label="Mobile number" required autocomplete="tel" inputmode="tel" placeholder="024 123 4567" autofocus hint="We will text you a code to confirm it." />
    <x-input name="password" type="password" label="Password" required autocomplete="new-password" hint="At least 8 characters, with letters and numbers." />
    <x-input name="password_confirmation" type="password" label="Confirm password" required autocomplete="new-password" />

    <div class="grid gap-5 sm:grid-cols-2">
        <x-select name="region" label="Region" :options="config('platform.regions')" />
        <x-input name="district" label="District" maxlength="80" />
    </div>

    <x-button full>Register</x-button>
    <p class="text-center text-xs text-gray-500">By registering you agree to the <a href="{{ route('terms') }}" target="_blank">terms</a> and <a href="{{ route('privacy') }}" target="_blank">privacy policy</a>.</p>
</form>
@endsection

@section('after')
Already registered? <a href="{{ route('login') }}" class="font-semibold">Sign in</a>
@endsection
