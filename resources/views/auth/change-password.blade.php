@extends('layouts.auth')

@section('title', 'Change password')
@section('heading', $forced ? 'Set your own password' : 'Change password')
@section('intro', $forced ? 'You signed in with a temporary password. Choose a new one to continue.' : 'You will stay signed in on this device and be signed out everywhere else.')

@section('form')
<form method="POST" action="{{ route('password.change.update') }}" class="space-y-5" data-auth-form>
    @csrf
    @method('PUT')
    <x-input name="current_password" type="password" :label="$forced ? 'Temporary password' : 'Current password'" required autocomplete="current-password" />
    <x-input name="password" type="password" label="New password" required autocomplete="new-password" hint="At least 10 characters, with letters and numbers." />
    <x-input name="password_confirmation" type="password" label="Confirm new password" required autocomplete="new-password" />
    <x-button full>Save password</x-button>
</form>
@endsection
