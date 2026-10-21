@extends('layouts.app')

@section('title', $level->name.($class ? ' · '.$class->name : '').' subjects')
@section('description', 'Subjects for '.($class?->name ?? $level->name).': topics, past questions, practice papers, videos and lesson plans.')

@php $q = fn (array $over) => route('study.level', [strtolower($level->code)] + array_filter(['class' => $class?->id, 'category' => $category] + $over)); @endphp

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 md:px-6 sm:py-12">
    <a href="{{ route('study.index') }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>Study by class</a>
    <h1 class="mt-2 text-3xl font-extrabold text-gray-800">{{ $level->name }}</h1>
    <p class="mt-1 text-gray-500">Choose your class and a subject.</p>

    <div class="mt-5 flex flex-wrap items-center gap-3">
        <nav aria-label="Class" class="flex flex-wrap gap-1 rounded-xl bg-white p-1 ring-1 ring-[#e2e0ea]">
            <a href="{{ $q(['class' => null]) }}" @if (! $class) aria-current="page" @endif class="rounded-lg px-3 py-1.5 text-sm font-semibold no-underline {{ ! $class ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-gray-100' }}">All classes</a>
            @foreach ($level->classLevels as $y)
                <a href="{{ $q(['class' => $y->id]) }}" @if ($class?->id === $y->id) aria-current="page" @endif class="rounded-lg px-3 py-1.5 text-sm font-semibold no-underline {{ $class?->id === $y->id ? 'bg-primary-600 text-white' : 'text-primary-700 hover:bg-gray-100' }}">{{ $y->name }}</a>
            @endforeach
        </nav>
        <nav aria-label="Subject type" class="flex gap-1 rounded-xl bg-white p-1 ring-1 ring-[#e2e0ea]">
            @foreach (['core' => 'Core', 'elective' => 'Elective'] as $value => $label)
                <a href="{{ $q(['category' => $value]) }}" @if ($category === $value) aria-current="page" @endif class="rounded-lg px-3 py-1.5 text-sm font-semibold no-underline {{ $category === $value ? 'bg-gold-500 text-primary-950' : 'text-primary-700 hover:bg-gray-100' }}">{{ $label }} <span class="font-normal opacity-70">{{ $counts[$value] ?? 0 }}</span></a>
            @endforeach
        </nav>
    </div>

    @if ($subjects->isEmpty())
        <div class="mt-6 rounded-2xl bg-white p-8 text-center text-gray-500 shadow-card ring-1 ring-[#ece9f3]">No {{ $category }} subjects at this level yet.</div>
    @else
        <ul class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($subjects as $s)
                <li>
                    <a href="{{ route('study.subject', [strtolower($level->code), $s, 'class' => $class?->id]) }}" class="flex h-full min-h-24 flex-col justify-between rounded-2xl bg-white p-4 no-underline shadow-card ring-1 ring-[#ece9f3] transition hover:-translate-y-0.5 hover:shadow-md">
                        <span class="font-semibold text-gray-800">{{ $s->name }}</span>
                        <span class="mt-2 text-xs font-semibold text-primary-600">Open <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i></span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
