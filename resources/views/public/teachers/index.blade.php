@extends('layouts.app')

@section('title', 'Teachers')
@section('description', 'Meet the teachers on '.config('app.name').': where they teach, their subjects, video tutorials and lesson materials.')

@php $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038] focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30'; @endphp

@section('content')
<section class="relative overflow-hidden bg-primary-700 bg-crosshatch text-white">
    <div class="pointer-events-none absolute -right-16 -top-20 h-72 w-72 rounded-full bg-gold-500/15 blur-3xl"></div>
    <div class="relative mx-auto flex max-w-6xl flex-col gap-6 px-4 pb-20 pt-10 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="eyebrow">Our teachers</p>
            <h1 class="mt-1 text-3xl font-extrabold sm:text-4xl">Teachers</h1>
            <p class="mt-2 max-w-2xl text-white/75">Verified teachers from schools across Ghana, sharing video tutorials and lesson materials.</p>
            <dl class="mt-4 flex flex-wrap gap-3 text-sm">
                <div class="rounded-xl bg-white/10 px-4 py-2 ring-1 ring-white/15"><dt class="sr-only">Teachers</dt><dd><strong class="font-display text-lg">{{ $teachers->count() }}</strong> teachers</dd></div>
                <div class="rounded-xl bg-white/10 px-4 py-2 ring-1 ring-white/15"><dt class="sr-only">Schools</dt><dd><strong class="font-display text-lg">{{ $schools }}</strong> schools</dd></div>
            </dl>
            <p class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                <span class="font-semibold text-white/70">Teacher tools:</span>
                <a href="{{ route('subject-apps.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 font-semibold text-primary-700 no-underline hover:bg-gold-50 hover:text-gold-700"><i class="fas fa-robot" aria-hidden="true"></i>Subject apps</a>
                <a href="{{ route('lesson-plans.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 font-semibold text-primary-700 no-underline hover:bg-gold-50 hover:text-gold-700"><i class="fas fa-clipboard-list" aria-hidden="true"></i>Lesson plans</a>
            </p>
        </div>
        @guest
            <a href="{{ route('teachers.register') }}" class="inline-flex h-12 shrink-0 items-center gap-2 rounded-xl bg-gold-500 px-6 text-[15px] font-bold text-primary-950 no-underline shadow-gold hover:bg-gold-400 hover:text-primary-950">Are you a teacher? Join free <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
        @endguest
    </div>
</section>

<div class="mx-auto max-w-6xl px-4 pb-10">
    <form method="GET" action="{{ route('teachers.index') }}" data-catalogue-filters role="search" class="relative -mt-14 rounded-2xl bg-white p-4 shadow-lift ring-1 ring-[#ece9f3]">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div class="sm:col-span-2">
                <label for="t-q" class="mb-1 block text-[13px] font-bold text-primary-800">Search</label>
                <input id="t-q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Teacher or school name" class="{{ $control }}">
            </div>
            <div>
                <label for="t-region" class="mb-1 block text-[13px] font-bold text-primary-800">Region</label>
                <select id="t-region" name="region" class="{{ $control }}">
                    <option value="">All regions</option>
                    @foreach ($regions as $r)<option value="{{ $r }}" @selected(($filters['region'] ?? '') === $r)>{{ $r }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="t-subject" class="mb-1 block text-[13px] font-bold text-primary-800">Subject</label>
                <select id="t-subject" name="subject" class="{{ $control }}">
                    <option value="">All subjects</option>
                    @foreach ($subjects as $s)<option value="{{ $s }}" @selected(mb_strtolower($filters['subject'] ?? '') === mb_strtolower($s))>{{ $s }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="t-has" class="mb-1 block text-[13px] font-bold text-primary-800">Shares</label>
                <select id="t-has" name="has" class="{{ $control }}">
                    <option value="">Anything</option>
                    <option value="videos" @selected(($filters['has'] ?? '') === 'videos')>Video tutorials</option>
                    <option value="materials" @selected(($filters['has'] ?? '') === 'materials')>Lesson materials</option>
                </select>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-3">
            <x-button icon="fa-filter">Apply</x-button>
            <label for="t-sort" class="text-sm font-semibold text-gray-600">Sort</label>
            <select id="t-sort" name="sort" class="rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2 text-sm">
                <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Newest</option>
                <option value="name" @selected(($filters['sort'] ?? '') === 'name')>A to Z</option>
                <option value="videos" @selected(($filters['sort'] ?? '') === 'videos')>Most videos</option>
            </select>
            @if (collect($filters)->except('sort')->filter()->isNotEmpty())<a href="{{ route('teachers.index') }}" class="ml-auto text-sm font-semibold no-underline"><i class="fas fa-xmark mr-1" aria-hidden="true"></i>Clear all</a>@endif
        </div>
    </form>
    @include('public.partials.auto-filter-script')

    @if ($teachers->isEmpty())
        <div class="mt-8 rounded-2xl bg-white p-8 text-center shadow-card ring-1 ring-[#ece9f3]">
            <i class="fas fa-chalkboard-user text-4xl text-gray-300" aria-hidden="true"></i>
            <p class="mt-3 text-gray-500">{{ array_filter($filters) ? 'No teacher matches. Try fewer filters.' : 'Teacher profiles will appear here soon.' }}</p>
        </div>
    @else
        <ul class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($teachers as $t)
                <li>
                    <a href="{{ route('teachers.show', $t->slug) }}" class="flex h-full flex-col rounded-2xl bg-white p-5 no-underline shadow-card ring-1 ring-[#ece9f3] transition hover:-translate-y-0.5 hover:ring-gold-500">
                        <div class="flex items-center gap-4">
                            @if ($t->photoUrl())
                                <img src="{{ $t->photoUrl() }}" alt="" class="h-16 w-16 shrink-0 rounded-full object-cover ring-2 ring-gold-400" loading="lazy">
                            @else
                                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-primary-600 font-display text-xl font-extrabold text-white">{{ $t->initials() }}</span>
                            @endif
                            <div class="min-w-0">
                                <p class="truncate font-bold text-gray-800">{{ $t->user->name }} <i class="fas fa-circle-check text-sm text-primary-600" title="Verified teacher" aria-label="Verified teacher"></i></p>
                                @if ($t->headline)<p class="line-clamp-2 text-sm text-gray-500">{{ $t->headline }}</p>@endif
                            </div>
                        </div>
                        <p class="mt-4 text-sm text-gray-700"><i class="fas fa-school mr-1.5 text-gold-600" aria-hidden="true"></i>{{ $t->school_name ?? 'School not given' }}</p>
                        <p class="mt-1 text-sm text-gray-500"><i class="fas fa-location-dot mr-1.5 text-gold-600" aria-hidden="true"></i>{{ collect([$t->district, $t->region])->filter()->implode(', ') ?: 'Ghana' }}</p>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach (array_slice($t->subjectList(), 0, 4) as $s)<span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-semibold text-primary-700">{{ $s }}</span>@endforeach
                        </div>
                        <div class="mt-auto flex gap-4 border-t border-gray-100 pt-3 text-xs font-semibold text-gray-500" style="margin-top: 1rem">
                            <span><i class="fas fa-circle-play mr-1 text-red-500" aria-hidden="true"></i>{{ $videoCounts[$t->user_id] ?? 0 }} videos</span>
                            <span><i class="fas fa-folder-open mr-1 text-gold-600" aria-hidden="true"></i>{{ $materialCounts[$t->user_id] ?? 0 }} materials</span>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
