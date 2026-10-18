@extends('layouts.auth')

@section('title', 'Sign in')
@section('heading', 'Welcome back')
@section('intro', 'Sign in with the phone number or email on your account.')

@section('form')
<form method="POST" action="{{ route('login.attempt') }}" class="space-y-5" data-auth-form>
    @csrf
    <x-input name="login" label="Phone number or email" required autocomplete="username" autofocus placeholder="024 123 4567" />
    <x-input name="password" type="password" label="Password" required autocomplete="current-password" />

    <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 text-gray-700">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-gray-300 text-primary-600">
            Remember me
        </label>
        <a href="{{ route('password.request') }}" class="font-medium underline underline-offset-2">Forgot password</a>
    </div>

    <x-button full>Sign in</x-button>

    <a href="{{ route('teachers.register') }}" class="flex w-full items-center justify-center gap-2 rounded-md border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 no-underline transition hover:bg-gray-50 hover:text-gray-800">
        <i class="fas fa-chalkboard-user text-brand-600" aria-hidden="true"></i> Register as a teacher
    </a>
</form>
@endsection

@section('after')
    New here? <a href="{{ route('register') }}" class="font-semibold">Create a free student account</a>
@endsection
