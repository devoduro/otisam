@extends('layouts.auth')

@section('title', 'Create a student account')
@section('wide', '1')
@section('heading', 'Create your account')
@section('intro', 'Free to join. We will text you a code to confirm your number.')

@section('form')
<form method="POST" action="{{ route('register.store') }}" class="space-y-5" data-auth-form>
    @csrf
    <x-input name="name" label="Full name" required autocomplete="name" maxlength="120" />
    <x-input name="phone" type="tel" label="Mobile number" required autocomplete="tel" inputmode="tel" placeholder="024 123 4567" hint="You will sign in with this number." />

    <div class="grid gap-5 sm:grid-cols-2">
        <x-select name="class_level_id" label="Class" :options="\App\Support\CurriculumOptions::classLevelChoices()" required />
        <x-select name="programme_id" label="Programme" :options="\App\Support\CurriculumOptions::programmeChoices()" />
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <x-input name="password" type="password" label="Password" required autocomplete="new-password" hint="8+ characters, letters and numbers." />
        <x-input name="password_confirmation" type="password" label="Confirm password" required autocomplete="new-password" />
    </div>

    <details class="rounded-md border border-gray-200 p-4" @if ($errors->hasAny(['email', 'school_name', 'region'])) open @endif>
        <summary class="cursor-pointer text-sm font-semibold text-gray-800"><i class="fas fa-plus-circle mr-1 text-primary-600" aria-hidden="true"></i> More details (optional)</summary>
        <div class="mt-4 space-y-4">
            <x-input name="email" type="email" label="Email" autocomplete="email" />
            <x-input name="school_name" label="School" maxlength="150" />
            <x-select name="region" label="Region" :options="config('platform.regions')" />
        </div>
    </details>

    <div class="space-y-4 rounded-md bg-primary-50 p-4 text-sm ring-1 ring-primary-100">
        <label class="flex items-start gap-2 font-medium text-gray-800">
            <input type="checkbox" name="is_adult" value="1" class="mt-0.5 h-4 w-4 rounded border-gray-300" @checked(old('is_adult'))>
            <span>I am 18 or older</span>
        </label>
        <p class="text-gray-600">Under 18? You can add a parent or guardian's contact. We never send them marketing messages.</p>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-input name="guardian_name" label="Parent/guardian name" />
            <x-input name="guardian_phone" type="tel" inputmode="tel" label="Parent/guardian mobile" placeholder="020 123 4567" />
        </div>
    </div>

    <label class="flex items-start gap-2 text-sm text-gray-700">
        <input type="checkbox" name="terms" value="1" required class="mt-0.5 h-4 w-4 rounded border-gray-300" @checked(old('terms'))>
        <span>I agree to the <a href="{{ route('terms') }}" target="_blank">terms</a> and <a href="{{ route('privacy') }}" target="_blank">privacy policy</a>.</span>
    </label>
    @error('terms')<p class="text-sm font-medium text-red-600">{{ $message }}</p>@enderror

    <x-button full>Create account</x-button>
</form>
@endsection

@section('after')
Already have an account? <a href="{{ route('login') }}" class="font-semibold">Sign in</a>
@endsection
