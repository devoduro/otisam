@extends('layouts.portal')

@section('title', 'My classes')
@section('subtitle', 'Create a class, give students the join code, then set assignments and quizzes.')

@section('actions')
    <x-button :href="route('teach.classes.create')" icon="fa-plus">New class</x-button>
@endsection

@section('content')
@if ($classrooms->isEmpty())
    <div class="panel p-8 text-center">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-2xl text-primary-600"><i class="fas fa-people-group" aria-hidden="true"></i></span>
        <h2 class="mt-4 text-lg font-bold text-gray-800">No classes yet</h2>
        <p class="mx-auto mt-1 max-w-md text-sm text-gray-500">Create a class for each group you teach, for example "SHS 2 Science A, Core Maths". Students join with a 6-character code.</p>
        <x-button class="mt-5" :href="route('teach.classes.create')" icon="fa-plus">Create my first class</x-button>
    </div>
@else
    <ul class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($classrooms as $classroom)
            <li>
                <a href="{{ route('teach.classes.show', $classroom) }}" class="panel block h-full p-5 no-underline transition hover:-translate-y-0.5 hover:shadow-lift {{ $classroom->isArchived() ? 'opacity-60' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-bold text-gray-800">{{ $classroom->name }}</h2>
                        @if ($classroom->isArchived())<span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500">Archived</span>@endif
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ collect([$classroom->subject?->name, $classroom->classLevel?->name])->filter()->implode(' · ') ?: 'No subject set' }}</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-primary-50 px-2.5 py-1 text-primary-700"><i class="fas fa-user-graduate mr-1" aria-hidden="true"></i>{{ $classroom->students_count }} {{ \Illuminate\Support\Str::plural('student', $classroom->students_count) }}</span>
                        <span class="rounded-full bg-gold-50 px-2.5 py-1 text-gold-700"><i class="fas fa-list-check mr-1" aria-hidden="true"></i>{{ $classroom->assignments_count }} set</span>
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 font-mono text-gray-700">Code {{ $classroom->join_code }}</span>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
@endif
@endsection
