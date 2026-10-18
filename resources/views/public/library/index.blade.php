@extends('layouts.app')

@section('title', 'Past questions & study library')
@section('description', 'Past questions, worked solutions, marking guides and notes for SHS students in Ghana.')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:py-12">
    <h1 class="text-3xl font-extrabold text-primary-900">Library</h1>
    <p class="mt-1 text-muted">Past questions, worked solutions and notes. Free items open straight away; premium items show a preview.</p>

    <form method="GET" class="mt-6 grid gap-3 rounded-xl border border-gray-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-6" role="search">
        <div class="lg:col-span-2">
            <label for="q" class="mb-1 block text-sm font-semibold">Search</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="block w-full rounded-lg border border-gray-300 px-3 py-2.5" placeholder="e.g. quadratic, 2019">
        </div>
        <div>
            <label for="subject" class="mb-1 block text-sm font-semibold">Subject</label>
            <select id="subject" name="subject" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5">
                <option value="">All subjects</option>
                @foreach ($subjects as $s)<option value="{{ $s->slug }}" @selected(($filters['subject'] ?? '') === $s->slug)>{{ $s->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label for="category" class="mb-1 block text-sm font-semibold">Type</label>
            <select id="category" name="category" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5">
                <option value="">All types</option>
                @foreach ($categories as $c)<option value="{{ $c->slug }}" @selected(($filters['category'] ?? '') === $c->slug)>{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label for="year" class="mb-1 block text-sm font-semibold">Year</label>
            <select id="year" name="year" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5">
                <option value="">Any year</option>
                @foreach ($years as $y)<option value="{{ $y }}" @selected((int) ($filters['year'] ?? 0) === (int) $y)>{{ $y }}</option>@endforeach
            </select>
        </div>
        <div>
            <label for="access" class="mb-1 block text-sm font-semibold">Access</label>
            <select id="access" name="access" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5">
                <option value="">Free and premium</option>
                <option value="free" @selected(($filters['access'] ?? '') === 'free')>Free only</option>
                <option value="premium" @selected(($filters['access'] ?? '') === 'premium')>Premium only</option>
            </select>
        </div>
        <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-6">
            <x-button>Show results</x-button>
            @if (array_filter($filters))<a href="{{ route('library.index') }}" class="px-3 py-2 text-sm">Clear filters</a>@endif
        </div>
    </form>

    <p class="mt-6 text-sm text-muted" role="status">{{ $resources->total() }} {{ \Illuminate\Support\Str::plural('item', $resources->total()) }}</p>

    @if ($resources->isEmpty())
        <p class="mt-3 rounded-xl border border-gray-200 bg-white p-6 text-muted">Nothing matches yet. Try fewer filters, or check back soon. New material is being added.</p>
    @else
        <ul class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($resources as $resource)
                @include('public.library._card')
            @endforeach
        </ul>
        <div class="mt-6">{{ $resources->links('pagination::tailwind') }}</div>
    @endif
</div>
@endsection
