@extends('layouts.auth')

@section('title', 'Sign in')
@section('heading', 'Sign in')
@section('intro', 'Use the phone number or email on your account.')

@section('form')
<form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
    @csrf
    <x-input name="login" label="Phone number or email" required autocomplete="username" autofocus inputmode="email" placeholder="024 123 4567" />
    <x-input name="password" type="password" label="Password" required autocomplete="current-password" />
    <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="remember" value="1" class="h-5 w-5 rounded border-gray-300"> Keep me signed in
        </label>
        <a href="{{ route('password.request') }}">Forgot password?</a>
    </div>
    <x-button full>Sign in</x-button>
</form>
@endsection

@section('after')
<p class="mt-6 text-center text-sm text-muted">
    New here? <a href="{{ route('register') }}" class="font-semibold">Create a student account</a>
    · <a href="{{ route('teachers.register') }}" class="font-semibold">Register as a teacher</a>
</p>
@endsection
