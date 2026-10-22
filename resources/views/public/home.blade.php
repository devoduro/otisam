@extends('layouts.app')

@section('content')
@php
    // Big category tiles (examsghana.com idea). Only real destinations; anything not built yet says so.
    $bySlug = $categories->keyBy('slug');
    $tiles = [
        ['WASSCE past questions', 'Papers by subject and year, with worked solutions.', 'fa-file-lines', 'blue', route('library.index', ['category' => 'wassce-past-questions']), $bySlug['wassce-past-questions']->resources_count ?? 0, 'papers.svg'],
        ['Worked solutions', 'Step-by-step answers and marking guides.', 'fa-square-check', 'green', route('library.index', ['category' => 'solutions-and-worked-answers']), $bySlug['solutions-and-worked-answers']->resources_count ?? 0, 'solutions.svg'],
        ['SHS learning centre', 'Every subject, strand and topic in the curriculum.', 'fa-sitemap', 'purple', route('subjects.index'), null, 'curriculum.svg'],
        ['Notes & revision', 'Lesson notes, revision notes and worksheets.', 'fa-book-open', 'orange', route('library.index', ['category' => 'revision-notes']), ($bySlug['revision-notes']->resources_count ?? 0) + ($bySlug['lesson-notes']->resources_count ?? 0), 'notes.svg'],
        ['BECE past questions', 'JHS papers for BECE candidates.', 'fa-school', 'slate', route('library.index', ['category' => 'bece-past-questions']), $bySlug['bece-past-questions']->resources_count ?? 0, 'school.svg'],
        ['For teachers', 'Lesson plans, schemes and professional exams.', 'fa-chalkboard-user', 'red', route('teachers.register'), null, 'teachers.svg'],
    ];
    // badge colour, overlay gradient laid over the picture (darker where the text sits).
    $tileColours = [
        'blue' => ['bg-tile-blue-dark', 'from-tile-blue-dark/95 via-tile-blue/80 to-tile-blue/35'],
        'green' => ['bg-tile-green-dark', 'from-tile-green-dark/95 via-tile-green/80 to-tile-green/35'],
        'purple' => ['bg-tile-purple-dark', 'from-tile-purple-dark/95 via-tile-purple/80 to-tile-purple/35'],
        'orange' => ['bg-tile-orange-dark', 'from-tile-orange-dark/95 via-tile-orange/80 to-tile-orange/35'],
        'slate' => ['bg-tile-slate-dark', 'from-tile-slate-dark/95 via-tile-slate/80 to-tile-slate/35'],
        'red' => ['bg-tile-red-dark', 'from-tile-red-dark/95 via-tile-red/80 to-tile-red/35'],
    ];
@endphp

{{-- Hero: jhs_portal's dark dashboard hero, with a search-first layout (open-exam-prep.com idea) --}}
<section class="relative overflow-hidden bg-primary-800 text-white">
    {{-- Background picture (Admin → Homepage, else the bundled classroom scene) under a dark overlay so the text stays readable. --}}
    <img src="{{ $heroImage }}" alt="" class="pointer-events-none absolute inset-0 h-full w-full object-cover" fetchpriority="high">
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-primary-900/90 via-primary-800/85 to-primary-700/75"></div>
    <div class="pointer-events-none absolute -right-16 -top-20 h-80 w-80 rounded-full bg-gold-500/15 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-24 left-1/4 h-72 w-72 rounded-full bg-primary-400/20 blur-3xl"></div>
    <div class="pointer-events-none absolute inset-0 bg-crosshatch"></div>

    <div class="relative mx-auto max-w-5xl px-4 py-14 text-center sm:py-20">
        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium ring-1 ring-white/15">
            <i class="fas fa-graduation-cap text-gold-400" aria-hidden="true"></i> For JHS &amp; SHS students and teachers in Ghana
        </p>
        <h1 class="mt-5 text-3xl font-extrabold sm:text-5xl">Learn it. Practise it. <span class="text-gold-400">Pass it.</span></h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-white/75">WASSCE past questions with worked solutions, notes mapped to the GES/NaCCA curriculum, and study tools that work on any phone.</p>

        <form method="GET" action="{{ route('library.index') }}" role="search" class="mx-auto mt-8 flex max-w-2xl items-center gap-2 rounded-2xl bg-white p-2 shadow-lift">
            <label for="hero-search" class="sr-only">Search past questions and notes</label>
            <i class="fas fa-magnifying-glass pl-3 text-gray-400" aria-hidden="true"></i>
            <input id="hero-search" type="search" name="q" placeholder="Search a topic, subject or year, e.g. quadratic 2019" class="min-w-0 flex-1 border-0 bg-transparent px-2 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:outline-none">
            <button type="submit" class="rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-primary-950 shadow-gold hover:bg-gold-400">Search</button>
        </form>

        <div class="mt-6 flex flex-wrap justify-center gap-3">
            @auth
                <a href="{{ route(auth()->user()->homeRoute()) }}" class="inline-flex h-12 items-center gap-2 rounded-xl bg-gold-500 px-6 text-[15px] font-bold text-primary-950 no-underline shadow-gold hover:bg-gold-400 hover:text-primary-950"><i class="fas fa-tachometer-alt" aria-hidden="true"></i> Go to my dashboard <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
            @else
                <a href="{{ route('register') }}" class="inline-flex h-12 items-center gap-2 rounded-xl bg-gold-500 px-6 text-[15px] font-bold text-primary-950 no-underline shadow-gold hover:bg-gold-400 hover:text-primary-950"><i class="fas fa-user-plus" aria-hidden="true"></i> Create a free account <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
                <a href="{{ route('subjects.index') }}" class="inline-flex h-12 items-center gap-2 rounded-xl bg-white/10 px-6 text-[15px] font-bold text-white no-underline ring-1 ring-white/30 hover:bg-white/20 hover:text-white"><i class="fas fa-sitemap" aria-hidden="true"></i> Browse subjects</a>
            @endauth
        </div>
    </div>

    {{-- Live numbers strip (open-exam-prep.com idea). Only real, non-zero figures are shown. --}}
    @php
        $figures = array_filter([
            [$stats['resources'], 'library items'],
            [$stats['free'], 'free to read'],
            [$stats['subjects'], 'subjects'],
            [$stats['topics'], 'curriculum topics'],
        ], fn ($f) => $f[0] > 0);
    @endphp
    @if ($figures)
        <div class="relative border-t border-white/10 bg-primary-950/40">
            <dl class="mx-auto grid max-w-5xl {{ count($figures) === 1 ? 'grid-cols-1' : 'grid-cols-2' }} gap-4 px-4 py-6 text-center {{ [1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3', 4 => 'sm:grid-cols-4'][count($figures)] }}">
                @foreach ($figures as [$value, $label])
                    <div><dt class="sr-only">{{ $label }}</dt><dd><span class="block text-2xl font-bold">{{ number_format($value) }}</span><span class="text-xs text-white/60">{{ $label }}</span></dd></div>
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
        @foreach ($tiles as [$title, $text, $icon, $colour, $href, $count, $image])
            <li>
                <a href="{{ $href }}" class="group relative flex h-full min-h-40 overflow-hidden rounded-2xl bg-primary-900 text-white no-underline shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:text-white hover:shadow-lg">
                    {{-- Picture under a colour overlay: strongest behind the text, lighter on the right so the artwork shows. --}}
                    <img src="{{ asset('images/tiles/'.$image) }}" alt="" loading="lazy" class="pointer-events-none absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="pointer-events-none absolute inset-0 bg-gradient-to-r {{ $tileColours[$colour][1] }}"></span>
                    <span class="relative flex flex-1 flex-col justify-between gap-4 p-5">
                        <span class="flex items-start justify-between gap-2">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $tileColours[$colour][0] }} text-xl shadow-md ring-1 ring-white/25"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                            @if ($count !== null)
                                <span class="whitespace-nowrap rounded-full bg-white/90 px-2.5 py-0.5 text-[11px] font-semibold text-gray-700">{{ $count > 0 ? number_format($count).' items' : 'Coming soon' }}</span>
                            @endif
                        </span>
                        <span>
                            <span class="block text-lg font-bold leading-tight drop-shadow-sm">{{ $title }}</span>
                            <span class="mt-1 block max-w-[16rem] text-xs text-white/90">{{ $text }}</span>
                        </span>
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
            <li class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
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
    <div class="flex flex-col items-start justify-between gap-4 rounded-[1.75rem] bg-primary-700 bg-crosshatch p-8 text-white shadow-lift md:flex-row md:p-12 md:items-center">
        <div>
            <p class="text-sm font-medium text-gold-400">For teachers</p>
            <h2 class="mt-1 text-2xl font-extrabold sm:text-3xl">Lesson plans, question writing and professional exams</h2>
            <p class="mt-2 max-w-xl text-white/75">Register as a teacher. Once an administrator verifies you, teacher resources unlock. Promotion and professional practice exams are on the way.</p>
        </div>
        <a href="{{ route('teachers.register') }}" class="inline-flex h-12 shrink-0 items-center gap-2 rounded-xl bg-gold-500 px-6 text-[15px] font-bold text-primary-950 no-underline shadow-gold hover:bg-gold-400 hover:text-primary-950">Register as a teacher <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
    </div>
</section>
@endsection
