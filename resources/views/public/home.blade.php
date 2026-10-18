@extends('layouts.app')

@section('content')
@php
    // Big category tiles (examsghana.com idea). Only real destinations; anything not built yet says so.
    $bySlug = $categories->keyBy('slug');
    $tiles = [
        ['WASSCE past questions', 'Papers by subject and year, with worked solutions.', 'fa-file-lines', 'blue', route('library.index', ['category' => 'wassce-past-questions']), $bySlug['wassce-past-questions']->resources_count ?? 0],
        ['Worked solutions', 'Step-by-step answers and marking guides.', 'fa-square-check', 'green', route('library.index', ['category' => 'solutions-and-worked-answers']), $bySlug['solutions-and-worked-answers']->resources_count ?? 0],
        ['SHS learning centre', 'Every subject, strand and topic in the curriculum.', 'fa-sitemap', 'purple', route('subjects.index'), null],
        ['Notes & revision', 'Lesson notes, revision notes and worksheets.', 'fa-book-open', 'orange', route('library.index', ['category' => 'revision-notes']), ($bySlug['revision-notes']->resources_count ?? 0) + ($bySlug['lesson-notes']->resources_count ?? 0)],
        ['BECE past questions', 'JHS papers for BECE candidates.', 'fa-school', 'slate', route('library.index', ['category' => 'bece-past-questions']), $bySlug['bece-past-questions']->resources_count ?? 0],
        ['For teachers', 'Lesson plans, schemes and professional exams.', 'fa-chalkboard-user', 'red', route('teachers.register'), null],
    ];
    $tileColours = [
        'blue' => ['bg-tile-blue', 'bg-tile-blue-dark'], 'green' => ['bg-tile-green', 'bg-tile-green-dark'], 'purple' => ['bg-tile-purple', 'bg-tile-purple-dark'],
        'orange' => ['bg-tile-orange', 'bg-tile-orange-dark'], 'slate' => ['bg-tile-slate', 'bg-tile-slate-dark'], 'red' => ['bg-tile-red', 'bg-tile-red-dark'],
    ];
@endphp

{{-- Hero: jhs_portal's dark dashboard hero, with a search-first layout (open-exam-prep.com idea) --}}
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 to-slate-800 text-white">
    <div class="pointer-events-none absolute -right-16 -top-20 h-80 w-80 rounded-full bg-[#42a5dc]/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-24 left-1/4 h-72 w-72 rounded-full bg-indigo-500/15 blur-3xl"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 22px 22px;"></div>

    <div class="relative mx-auto max-w-5xl px-4 py-14 text-center sm:py-20">
        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium ring-1 ring-white/15">
            <i class="fas fa-graduation-cap text-[#7cc4ea]" aria-hidden="true"></i> For SHS students and teachers in Ghana
        </p>
        <h1 class="mt-5 text-3xl font-bold tracking-tight sm:text-5xl">Learn it. Practise it. <span class="text-[#7cc4ea]">Pass it.</span></h1>
        <p class="mx-auto mt-4 max-w-2xl text-slate-300">WASSCE past questions with worked solutions, notes mapped to the GES/NaCCA curriculum, and study tools that work on any phone.</p>

        <form method="GET" action="{{ route('library.index') }}" role="search" class="mx-auto mt-8 flex max-w-2xl items-center gap-2 rounded-xl bg-white p-2 shadow-lg">
            <label for="hero-search" class="sr-only">Search past questions and notes</label>
            <i class="fas fa-magnifying-glass pl-3 text-gray-400" aria-hidden="true"></i>
            <input id="hero-search" type="search" name="q" placeholder="Search a topic, subject or year, e.g. quadratic 2019" class="min-w-0 flex-1 border-0 bg-transparent px-2 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none">
            <button type="submit" class="rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Search</button>
        </form>

        <div class="mt-6 flex flex-wrap justify-center gap-3">
            @auth
                <a href="{{ route(auth()->user()->homeRoute()) }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 no-underline hover:bg-gray-100 hover:text-slate-900"><i class="fas fa-tachometer-alt text-[#42a5dc]" aria-hidden="true"></i> Go to my dashboard</a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 no-underline hover:bg-gray-100 hover:text-slate-900"><i class="fas fa-user-plus text-[#42a5dc]" aria-hidden="true"></i> Create a free account</a>
                <a href="{{ route('subjects.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white no-underline ring-1 ring-white/20 hover:bg-white/20 hover:text-white"><i class="fas fa-sitemap" aria-hidden="true"></i> Browse subjects</a>
            @endauth
        </div>
    </div>

    {{-- Live numbers strip (open-exam-prep.com idea). Only real, non-zero figures are shown. --}}
    @php
        $figures = array_filter([
            [$stats['resources'], 'library items'],
            [$stats['free'], 'free to read'],
            [$stats['subjects'], 'SHS subjects'],
            [$stats['topics'], 'curriculum topics'],
        ], fn ($f) => $f[0] > 0);
    @endphp
    @if ($figures)
        <div class="relative border-t border-white/10 bg-slate-900/40">
            <dl class="mx-auto grid max-w-5xl {{ count($figures) === 1 ? 'grid-cols-1' : 'grid-cols-2' }} gap-4 px-4 py-6 text-center {{ [1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3', 4 => 'sm:grid-cols-4'][count($figures)] }}">
                @foreach ($figures as [$value, $label])
                    <div><dt class="sr-only">{{ $label }}</dt><dd><span class="block text-2xl font-bold">{{ number_format($value) }}</span><span class="text-xs text-slate-400">{{ $label }}</span></dd></div>
                @endforeach
            </dl>
        </div>
    @endif
</section>

{{-- Category tiles in jhs_portal's two-tone stat-tile style --}}
<section class="mx-auto max-w-7xl px-4 py-12 md:px-6" aria-labelledby="browse-heading">
    <div class="flex flex-wrap items-end justify-between gap-2">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-primary-600">Start here</p>
            <h2 id="browse-heading" class="text-2xl font-bold text-gray-800">What do you want to study?</h2>
        </div>
        <a href="{{ route('library.index') }}" class="text-sm font-medium">Open the full library <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i></a>
    </div>
    <ul class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($tiles as [$title, $text, $icon, $colour, $href, $count])
            <li>
                <a href="{{ $href }}" class="group flex h-full overflow-hidden rounded-md text-white no-underline shadow-sm transition hover:-translate-y-0.5 hover:text-white hover:shadow-md">
                    <span class="flex w-16 shrink-0 items-center justify-center {{ $tileColours[$colour][1] }}"><i class="fas {{ $icon }} text-2xl" aria-hidden="true"></i></span>
                    <span class="flex flex-1 flex-col justify-center px-4 py-4 {{ $tileColours[$colour][0] }}">
                        <span class="flex items-center justify-between gap-2">
                            <span class="text-base font-semibold leading-tight">{{ $title }}</span>
                            @if ($count !== null)
                                <span class="whitespace-nowrap rounded-full bg-white/90 px-2 py-0.5 text-[11px] font-semibold text-gray-700">{{ $count > 0 ? number_format($count).' items' : 'Coming soon' }}</span>
                            @endif
                        </span>
                        <span class="mt-1 text-xs opacity-90">{{ $text }}</span>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>
</section>

{{-- Subjects with counts (open-exam-prep "browse by domain") --}}
<section class="border-y border-gray-200 bg-white" aria-labelledby="subjects-heading">
    <div class="mx-auto max-w-7xl px-4 py-12 md:px-6">
        <div class="flex flex-wrap items-end justify-between gap-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-primary-600">Learning centre</p>
                <h2 id="subjects-heading" class="text-2xl font-bold text-gray-800">Browse by subject</h2>
            </div>
            <a href="{{ route('subjects.index') }}" class="text-sm font-medium">All subjects <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i></a>
        </div>
        <ul class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($subjects as $subject)
                <li>
                    <a href="{{ route('subjects.show', $subject) }}" class="flex h-full items-start gap-3 rounded-md bg-gray-50 p-4 no-underline ring-1 ring-gray-100 transition hover:bg-primary-50 hover:ring-primary-200">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $subject->category === 'core' ? 'bg-primary-100 text-primary-600' : 'bg-green-100 text-green-700' }}"><i class="fas fa-book" aria-hidden="true"></i></span>
                        <span class="min-w-0">
                            <span class="block font-semibold text-gray-800">{{ $subject->name }}</span>
                            <span class="block text-xs text-gray-500">
                                {{ $subject->category === 'core' ? 'Core' : 'Elective' }}
                                @if ($subject->nodes_count) · {{ number_format($subject->nodes_count) }} topics @endif
                                @if ($subject->resources_count) · {{ number_format($subject->resources_count) }} items @endif
                            </span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>

@if ($latest->isNotEmpty())
    <section class="mx-auto max-w-7xl px-4 py-12 md:px-6" aria-labelledby="latest-heading">
        <h2 id="latest-heading" class="text-2xl font-bold text-gray-800">New in the library</h2>
        <ul class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($latest as $resource)
                @include('public.library._card')
            @endforeach
        </ul>
    </section>
@endif

{{-- How it works --}}
<section class="mx-auto max-w-7xl px-4 py-12 md:px-6" aria-labelledby="how-heading">
    <h2 id="how-heading" class="text-2xl font-bold text-gray-800">How it works</h2>
    <ol class="mt-6 grid gap-4 md:grid-cols-3">
        @foreach ([
            ['fa-user-plus', 'Sign up with your phone', 'Free account in a minute. We text you a code; no email needed.'],
            ['fa-magnifying-glass', 'Find your topic', 'Search past questions by subject, year or topic, or follow the curriculum strand by strand.'],
            ['fa-crown', 'Go premium when ready', $cheapestPlan ? 'Unlock every worked solution from '.$cheapestPlan->price().' with mobile money or card.' : 'Unlock every worked solution with mobile money or card.'],
        ] as $i => [$icon, $title, $text])
            <li class="rounded-md bg-white p-5 shadow-sm ring-1 ring-gray-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-600 text-white"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Step {{ $i + 1 }}</p>
                <h3 class="font-semibold text-gray-800">{{ $title }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $text }}</p>
            </li>
        @endforeach
    </ol>
</section>

{{-- Teachers band --}}
<section class="mx-auto max-w-7xl px-4 pb-14 md:px-6">
    <div class="flex flex-col items-start justify-between gap-4 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 p-7 text-white shadow-lg md:flex-row md:items-center">
        <div>
            <p class="text-sm font-medium text-[#7cc4ea]">For teachers</p>
            <h2 class="mt-1 text-2xl font-bold">Lesson plans, question writing and professional exams</h2>
            <p class="mt-1 max-w-xl text-sm text-slate-300">Register as a teacher. Once an administrator verifies you, teacher resources unlock. Promotion and professional practice exams are on the way.</p>
        </div>
        <a href="{{ route('teachers.register') }}" class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 no-underline hover:bg-gray-100 hover:text-slate-900"><i class="fas fa-chalkboard-user text-[#42a5dc]" aria-hidden="true"></i> Register as a teacher</a>
    </div>
</section>
@endsection
