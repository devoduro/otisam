@extends('layouts.app')

@section('title', $node->title . ' · ' . $subject->name)

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:py-12">
    <nav aria-label="Breadcrumb" class="text-sm text-gray-500">
        <ol class="flex flex-wrap gap-1">
            <li><a href="{{ route('subjects.index') }}">Subjects</a> /</li>
            <li><a href="{{ route('subjects.show', [$subject, 'version' => $node->curriculum_version_id]) }}">{{ $subject->name }}</a> /</li>
            @foreach ($ancestors as $ancestor)
                <li><a href="{{ route('subjects.topic', [$subject, $ancestor]) }}">{{ \Illuminate\Support\Str::limit($ancestor->title, 40) }}</a> /</li>
            @endforeach
            <li aria-current="page">{{ \Illuminate\Support\Str::limit($node->title, 40) }}</li>
        </ol>
    </nav>

    <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-gray-500">
        {{ $node->typeLabel() }}{{ $node->code ? ' ' . $node->code : '' }}{{ $node->classLevel ? ' · ' . $node->classLevel->name : '' }}
    </p>
    <h1 class="mt-1 text-2xl font-extrabold text-gray-800 sm:text-3xl">{{ $node->title }}</h1>

    @if ($node->statement)
        <p class="mt-4 whitespace-pre-line rounded-xl border-l-4 border-primary-600 bg-white p-4">{{ $node->statement }}</p>
    @endif

    <p class="mt-3 text-sm">
        @if ($node->is_official)
            <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">Official curriculum</span>
            <span class="text-gray-500">{{ $node->version->name }}{{ $node->source_ref ? ', ' . $node->source_ref : '' }}</span>
        @else
            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700">Supplementary</span>
        @endif
    </p>

    @if ($node->children->isNotEmpty())
        <h2 class="mt-8 text-lg font-bold text-gray-800">In this {{ strtolower($node->typeLabel()) }}</h2>
        <ul class="mt-2 divide-y divide-gray-200 rounded-md bg-white shadow-sm ring-1 ring-gray-100">
            @foreach ($node->children as $child)
                <li>
                    <a href="{{ route('subjects.topic', [$subject, $child]) }}" class="flex items-start justify-between gap-3 px-4 py-3 no-underline hover:bg-primary-50">
                        <span>
                            <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $child->typeLabel() }}{{ $child->code ? ' ' . $child->code : '' }}</span>
                            <span class="text-gray-800">{{ $child->title }}</span>
                        </span>
                        <span aria-hidden="true" class="text-accent-600">→</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <section class="mt-8">
        <h2 class="text-lg font-bold text-gray-800">Library material for this topic</h2>
        @if ($resources->isEmpty())
            <p class="mt-2 rounded-xl border border-dashed border-gray-300 bg-white p-5 text-sm text-gray-500">Notes, past questions and solutions for this topic will appear here as they are published.</p>
        @else
            <ul class="mt-2 grid gap-3 sm:grid-cols-2">
                @foreach ($resources as $resource)
                    @include('public.library._card')
                @endforeach
            </ul>
        @endif
    </section>
</div>
@endsection
