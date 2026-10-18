@extends('layouts.app')

@section('title', 'Subjects')
@section('description', 'Browse SHS subjects and their curriculum topics.')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:py-12">
    <h1 class="text-3xl font-extrabold text-gray-800">Subjects</h1>
    <p class="mt-1 text-gray-500">Pick a subject to see its strands and topics.</p>

    @forelse ($levels as $level)
        <section class="mt-8" aria-labelledby="level-{{ $level->id }}">
            <h2 id="level-{{ $level->id }}" class="text-lg font-bold text-gray-800">{{ $level->name }}</h2>
            @foreach (['core' => 'Core subjects', 'elective' => 'Electives'] as $category => $heading)
                @php $group = $level->subjects->where('category', $category); @endphp
                @continue($group->isEmpty())
                <h3 class="mt-4 text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $heading }}</h3>
                <ul class="mt-2 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($group as $subject)
                        <li>
                            <a href="{{ route('subjects.show', $subject) }}" class="flex min-h-14 items-center justify-between rounded-md bg-white shadow-sm ring-1 ring-gray-100 px-4 py-3 font-semibold text-gray-800 no-underline hover:border-primary-600 hover:bg-primary-50">
                                <span>{{ $subject->name }}</span>
                                <span aria-hidden="true" class="text-accent-600">→</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </section>
    @empty
        <p class="mt-8 rounded-md bg-white shadow-sm ring-1 ring-gray-100 p-6 text-gray-500">Subjects will appear here soon.</p>
    @endforelse
</div>
@endsection
