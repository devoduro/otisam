@extends('layouts.auth')

@section('title', 'Confirm your number')
@section('heading', 'Confirm your number')
@section('intro')
    Enter the 6-digit code we sent to {{ $maskedPhone }}.
@endsection

@section('form')
<form method="POST" action="{{ route('verify.phone.check') }}" class="space-y-4">
    @csrf
    <x-input name="code" label="Code" required inputmode="numeric" autocomplete="one-time-code" pattern="[0-9 ]*" maxlength="10" autofocus class="text-center text-2xl tracking-[0.5em]" />
    <x-button full>Confirm</x-button>
</form>

<form method="POST" action="{{ route('verify.phone.resend') }}" class="mt-4 text-center">
    @csrf
    <button type="submit" class="text-sm font-semibold text-primary-600 underline">Send a new code</button>
</form>
<p class="mt-3 text-center text-xs text-muted">Codes expire after {{ config('platform.otp.ttl_minutes') }} minutes. Wrong number? Log out and register again.</p>
@endsection
