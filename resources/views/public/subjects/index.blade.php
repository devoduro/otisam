@extends('layouts.app')

@section('title', 'Subjects')
@section('description', 'Browse JHS and SHS subjects, their curriculum topics, past questions, video lessons and practice questions.')

@php
    use App\Http\Controllers\Public\SubjectController as SC;
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038] focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30';
    $programmes = ($level ? collect([$level]) : $allLevels)->flatMap->programmes;
    $badges = [
        'library' => ['fa-book-open', 'bg-primary-50 text-primary-700', 'library.index', 'past questions & notes'],
        'videos' => ['fa-circle-play', 'bg-red-50 text-red-700', 'videos.index', 'videos'],
        'practice' => ['fa-list-check', 'bg-gold-50 text-gold-700', 'practice.index', 'practice questions'],
    ];
@endphp

@section('content')
<section class="relative overflow-hidden bg-primary-700 bg-crosshatch text-white">
    <div class="relative mx-auto max-w-6xl px-4 pb-20 pt-10">
        <p class="eyebrow">GES / NaCCA curriculum</p>
        <h1 class="mt-1 text-3xl font-extrabold sm:text-4xl">Subjects</h1>
        <p class="mt-2 max-w-2xl text-white/75">Every JHS and SHS subject, with its topics, past questions, video lessons and practice questions in one place.</p>
    </div>
</section>

<div class="mx-auto max-w-6xl px-4 pb-10">
    <form method="GET" action="{{ route('subjects.index') }}" data-catalogue-filters class="relative -mt-14 rounded-2xl bg-white p-4 shadow-lift ring-1 ring-[#ece9f3]" role="search">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <div class="sm:col-span-2">
                <label for="s-q" class="mb-1 block text-[13px] font-bold text-primary-800">Search</label>
                <input id="s-q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="e.g. Physics, Twi, Computing" class="{{ $control }}">
            </div>
            <div>
                <label for="s-level" class="mb-1 block text-[13px] font-bold text-primary-800">Level</label>
                <select id="s-level" name="level" class="{{ $control }}" data-resets="programme">
                    <option value="">JHS &amp; SHS</option>
                    @foreach ($allLevels as $l)<option value="{{ strtolower($l->code) }}" @selected($level?->id === $l->id)>{{ $l->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="s-category" class="mb-1 block text-[13px] font-bold text-primary-800">Core or elective</label>
                <select id="s-category" name="category" class="{{ $control }}">
                    <option value="">Both</option>
                    <option value="core" @selected(($filters['category'] ?? '') === 'core')>Core subjects</option>
                    <option value="elective" @selected(($filters['category'] ?? '') === 'elective')>Electives</option>
                </select>
            </div>
            <div>
                <label for="s-programme" class="mb-1 block text-[13px] font-bold text-primary-800">Programme</label>
                <select id="s-programme" name="programme" class="{{ $control }}" @disabled($programmes->isEmpty())>
                    <option value="">Any programme</option>
                    @foreach ($programmes as $p)<option value="{{ $p->id }}" @selected((int) ($filters['programme'] ?? 0) === $p->id)>{{ $p->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="s-has" class="mb-1 block text-[13px] font-bold text-primary-800">Has</label>
                <select id="s-has" name="has" class="{{ $control }}">
                    <option value="">Anything</option>
                    @foreach (SC::HAS as $k => $label)<option value="{{ $k }}" @selected(($filters['has'] ?? '') === $k)>{{ $label }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-3">
            <x-button icon="fa-filter">Apply</x-button>
            <label for="s-sort" class="text-sm font-semibold text-gray-600">Sort</label>
            <select id="s-sort" name="sort" class="rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2 text-sm">
                <option value="curriculum" @selected(($filters['sort'] ?? 'curriculum') === 'curriculum')>Curriculum order</option>
                <option value="name" @selected(($filters['sort'] ?? '') === 'name')>A to Z</option>
                <option value="content" @selected(($filters['sort'] ?? '') === 'content')>Most material</option>
            </select>
            <span class="text-sm text-gray-500" aria-live="polite" data-count>{{ $total }} {{ \Illuminate\Support\Str::plural('subject', $total) }}</span>
            @if (collect($filters)->except('sort')->filter()->isNotEmpty())<a href="{{ route('subjects.index') }}" class="ml-auto text-sm font-semibold no-underline"><i class="fas fa-xmark mr-1" aria-hidden="true"></i>Clear all</a>@endif
        </div>
    </form>
    @include('public.partials.auto-filter-script')

    @forelse ($levels as $l)
        <section class="mt-10" aria-labelledby="level-{{ $l->id }}">
            <h2 id="level-{{ $l->id }}" class="text-xl font-extrabold text-primary-700">{{ $l->name }}</h2>
            @foreach (['core' => 'Core subjects', 'elective' => 'Electives'] as $category => $heading)
                @php $group = $l->subjects->where('category', $category); @endphp
                @continue($group->isEmpty())
                <h3 class="mt-4 text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $heading }}</h3>
                <ul class="mt-2 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($group as $subject)
                        <li class="flex flex-col rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3] transition hover:-translate-y-0.5 hover:ring-gold-500">
                            <a href="{{ route('subjects.show', $subject) }}" class="flex items-start justify-between gap-2 font-semibold text-gray-800 no-underline">
                                <span>{{ $subject->name }}</span>
                                <span aria-hidden="true" class="text-gold-600">→</span>
                            </a>
                            @if ($subject->description)<p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ $subject->description }}</p>@endif
                            <div class="mt-3 flex flex-wrap gap-1.5 text-xs font-semibold">
                                @foreach ($badges as $key => [$icon, $tone, $route, $label])
                                    @php $n = $counts[$key][$subject->id] ?? 0; @endphp
                                    @if ($n)
                                        <a href="{{ route($route, ['subject' => $subject->slug]) }}" class="rounded-full px-2 py-0.5 no-underline {{ $tone }}" title="{{ $n }} {{ $label }}"><i class="fas {{ $icon }} mr-1" aria-hidden="true"></i>{{ $n }}<span class="sr-only"> {{ $label }}</span></a>
                                    @endif
                                @endforeach
                                @if ($counts['topics'][$subject->id] ?? 0)<span class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-600"><i class="fas fa-sitemap mr-1" aria-hidden="true"></i>{{ $counts['topics'][$subject->id] }} topics</span>@endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </section>
    @empty
        <p class="mt-8 rounded-2xl bg-white p-6 text-gray-500 shadow-card ring-1 ring-[#ece9f3]">No subjects match. <a href="{{ route('subjects.index') }}">Clear the filters</a>.</p>
    @endforelse
</div>
@endsection
