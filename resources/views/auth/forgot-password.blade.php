@extends('layouts.auth')

@section('title', 'Reset password')
@section('heading', 'Reset your password')
@section('intro', 'Enter your mobile number and we will text you a reset code.')

@section('form')
<form method="POST" action="{{ route('password.send') }}" class="space-y-5" data-auth-form>
    @csrf
    <x-input name="phone" type="tel" label="Mobile number" required inputmode="tel" autocomplete="tel" placeholder="024 123 4567" autofocus />
    <x-button full>Send code</x-button>
</form>
@endsection

@section('after')
<a href="{{ route('login') }}" class="font-semibold">Back to sign in</a>
@endsection
