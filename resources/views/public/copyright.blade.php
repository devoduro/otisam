@extends('layouts.app')

@section('title', 'Copyright and content removal')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:py-12">
    <h1 class="text-3xl font-extrabold text-gray-800">Copyright and content removal</h1>
    <div class="mt-4 space-y-3">
        <p>{{ config('app.name') }} only publishes material it owns, has permission to share, or may lawfully make available. Premium content is our own worked solutions, explanations and practice material. Examination papers are credited to their copyright holders.</p>
        <p>If you hold the rights to something on this site and want it removed or corrected, send the form below. We review every request and reply by email.</p>
    </div>

    <form method="POST" action="{{ route('copyright.takedown') }}" class="mt-8 space-y-4 rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3] p-6">
        @csrf
        <x-flash />
        @if ($resource)
            <input type="hidden" name="resource_slug" value="{{ $resource->slug }}">
            <p class="rounded-lg bg-primary-50 p-3 text-sm">About: <strong>{{ $resource->title }}</strong></p>
        @else
            <x-input name="resource_url" label="Link to the item on our site" maxlength="500" />
        @endif
        <x-input name="name" label="Your name" required maxlength="120" autocomplete="name" />
        <x-input name="email" type="email" label="Email" required maxlength="150" autocomplete="email" />
        <x-input name="phone" type="tel" label="Phone" maxlength="20" autocomplete="tel" />
        <x-input name="rights_holder" label="Who owns the rights" required maxlength="200" hint="You, or the organisation you represent." />
        <div>
            <label for="details" class="mb-1 block text-sm font-semibold">What is the problem?</label>
            <textarea id="details" name="details" rows="5" required minlength="20" maxlength="5000" class="block w-full rounded-lg border border-gray-300 px-3 py-2.5">{{ old('details') }}</textarea>
            @error('details')<p class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-start gap-2 text-sm">
            <input type="checkbox" name="declaration" value="1" required class="mt-0.5 h-5 w-5 rounded border-gray-300">
            <span>I believe in good faith that this use is not authorised, and the information I have given is accurate.</span>
        </label>
        @error('declaration')<p class="text-sm font-medium text-red-700">{{ $message }}</p>@enderror
        <x-button>Send request</x-button>
    </form>
</div>
@endsection
