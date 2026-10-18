@extends('layouts.auth')

@section('title', 'Choose a new password')
@section('heading', 'Choose a new password')
@section('intro')
    Enter the code sent to {{ $maskedPhone }} and your new password.
@endsection

@section('form')
<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <x-input name="code" label="Code" required inputmode="numeric" autocomplete="one-time-code" maxlength="10" autofocus />
    <x-input name="password" type="password" label="New password" required autocomplete="new-password" hint="At least 8 characters, with letters and numbers." />
    <x-input name="password_confirmation" type="password" label="Confirm new password" required autocomplete="new-password" />
    <x-button full>Change password</x-button>
</form>
<p class="mt-4 text-center text-sm"><a href="{{ route('password.request') }}">Didn't get a code? Request another</a></p>
@endsection
