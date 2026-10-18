@extends('layouts.app')

@section('title', 'My dashboard')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-6 sm:py-10">
    <x-flash />

    <section class="rounded-2xl bg-primary-900 p-6 text-white sm:p-8">
        <p class="text-sm font-semibold text-accent-400">{{ $profile?->class_level }}{{ $profile?->programme ? ' · ' . $profile->programme : '' }}</p>
        <h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">Hello, {{ \Illuminate\Support\Str::before($user->name, ' ') ?: $user->name }}</h1>
        <p class="mt-2 max-w-xl text-primary-100">Your account is ready. Subjects, practice questions and past-question solutions are being added. You will see them here as soon as they are published.</p>
    </section>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="font-bold text-primary-900">Subscription</h2>
            <p class="mt-1 text-sm text-muted">Free account. Premium plans open when the library launches.</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="font-bold text-primary-900">Your details</h2>
            <dl class="mt-2 space-y-1 text-sm">
                <div class="flex justify-between gap-2"><dt class="text-muted">Phone</dt><dd>{{ \App\Support\Phone::mask($user->phone) }} ✓</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-muted">School</dt><dd>{{ $profile?->school_name ?? '—' }}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-muted">Region</dt><dd>{{ $profile?->region ?? '—' }}</dd></div>
            </dl>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="font-bold text-primary-900">Security</h2>
            <p class="mt-1 text-sm text-muted">Keep your password private. We will never ask for it by SMS or phone.</p>
            <a href="{{ route('password.change') }}" class="mt-3 inline-block text-sm font-semibold">Change password</a>
        </div>
    </div>
</div>
@endsection
