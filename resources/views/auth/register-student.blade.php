@extends('layouts.auth')

@section('title', 'Create a student account')
@section('heading', 'Create your account')
@section('intro', 'Free to join. We will text you a code to confirm your number.')

@section('form')
<form method="POST" action="{{ route('register.store') }}" class="space-y-4">
    @csrf
    <x-input name="name" label="Full name" required autocomplete="name" maxlength="120" />
    <x-input name="phone" type="tel" label="Mobile number" required autocomplete="tel" inputmode="tel" placeholder="024 123 4567" hint="You will sign in with this number." />
    <x-select name="class_level" label="Class" :options="config('platform.class_levels')" required />
    <x-select name="programme" label="Programme" :options="config('platform.programmes')" />
    <x-input name="password" type="password" label="Password" required autocomplete="new-password" hint="At least 8 characters, with letters and numbers." />
    <x-input name="password_confirmation" type="password" label="Confirm password" required autocomplete="new-password" />

    <details class="rounded-lg border border-gray-200 p-3" @if ($errors->hasAny(['email', 'school_name', 'region', 'guardian_name', 'guardian_phone'])) open @endif>
        <summary class="cursor-pointer text-sm font-semibold text-primary-900">More details (optional)</summary>
        <div class="mt-3 space-y-4">
            <x-input name="email" type="email" label="Email" autocomplete="email" />
            <x-input name="school_name" label="School" maxlength="150" />
            <x-select name="region" label="Region" :options="config('platform.regions')" />
        </div>
    </details>

    <div class="space-y-3 rounded-lg bg-primary-50 p-3 text-sm">
        <label class="flex items-start gap-2">
            <input type="checkbox" name="is_adult" value="1" class="mt-0.5 h-5 w-5 rounded border-gray-300" @checked(old('is_adult'))>
            <span>I am 18 or older</span>
        </label>
        <p class="text-muted">If you are under 18, you can add a parent or guardian's contact. We never send them marketing messages.</p>
        <x-input name="guardian_name" label="Parent/guardian name" />
        <x-input name="guardian_phone" type="tel" inputmode="tel" label="Parent/guardian mobile" placeholder="020 123 4567" />
    </div>

    <label class="flex items-start gap-2 text-sm">
        <input type="checkbox" name="terms" value="1" required class="mt-0.5 h-5 w-5 rounded border-gray-300" @checked(old('terms'))>
        <span>I agree to the <a href="{{ route('terms') }}" target="_blank">terms</a> and <a href="{{ route('privacy') }}" target="_blank">privacy policy</a>.</span>
    </label>
    @error('terms')<p class="text-sm font-medium text-red-700">{{ $message }}</p>@enderror

    <x-button full>Create account</x-button>
</form>
@endsection

@section('after')
<p class="mt-6 text-center text-sm text-muted">Already have an account? <a href="{{ route('login') }}" class="font-semibold">Sign in</a></p>
@endsection
