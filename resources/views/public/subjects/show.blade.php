@extends('layouts.app')

@section('title', $subject->name)
@section('description', $subject->description ?: "{$subject->name} curriculum topics for {$subject->educationLevel->name}.")

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:py-12">
    <nav aria-label="Breadcrumb" class="text-sm text-gray-500">
        <ol class="flex flex-wrap gap-1">
            <li><a href="{{ route('subjects.index') }}">Subjects</a> /</li>
            <li aria-current="page">{{ $subject->name }}</li>
        </ol>
    </nav>

    <h1 class="mt-2 text-3xl font-extrabold text-gray-800">{{ $subject->name }}</h1>
    <p class="mt-1 text-gray-500">{{ $subject->educationLevel->name }} · {{ \App\Models\Curriculum\Subject::CATEGORIES[$subject->category] ?? '' }}</p>
    @if ($subject->description)<p class="mt-3">{{ $subject->description }}</p>@endif
    <p class="mt-3"><a href="{{ route('library.index', ['subject' => $subject->slug]) }}" class="font-semibold">Past questions, solutions and notes for {{ $subject->name }} →</a></p>

    @if ($version)
        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-md bg-white shadow-sm ring-1 ring-gray-100 p-4">
            <div>
                <p class="text-sm font-semibold text-gray-800">{{ $version->name }}</p>
                <p class="text-xs text-gray-500">
                    {{ $version->status === 'retired' ? 'Earlier curriculum' : 'Current curriculum' }}{{ $version->effective_from ? ' · from ' . $version->effective_from->format('F Y') : '' }}
                </p>
            </div>
            @if ($versions->count() > 1)
                <form method="GET" class="flex items-center gap-2">
                    <label for="version" class="text-sm text-gray-500">Curriculum</label>
                    <select id="version" name="version" class="rounded-lg border border-gray-300 px-2 py-2 text-sm" onchange="this.form.submit()">
                        @foreach ($versions as $v)
                            <option value="{{ $v->id }}" @selected($v->id === $version->id)>{{ $v->name }}</option>
                        @endforeach
                    </select>
                    <noscript><button class="rounded-lg border px-3 py-2 text-sm" type="submit">Show</button></noscript>
                </form>
            @endif
        </div>

        <ul class="mt-4 rounded-md bg-white shadow-sm ring-1 ring-gray-100 p-2 sm:p-4">
            @foreach ($roots as $node)
                @include('public.subjects._node', ['node' => $node])
            @endforeach
        </ul>
        <p class="mt-3 text-xs text-gray-500">Items marked “official” follow the wording of the GES/NaCCA curriculum document named above; other items are supplementary.</p>
    @else
        <p class="mt-6 rounded-md bg-white shadow-sm ring-1 ring-gray-100 p-6 text-gray-500">The topics for {{ $subject->name }} are being added. Check back soon.</p>
    @endif
</div>
@endsection
