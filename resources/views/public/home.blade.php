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

    <div class="relative mx-auto max-w-5xl px-4 pb-24 pt-14 text-center sm:pb-28 sm:pt-20">
        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium ring-1 ring-white/15">
            <i class="fas fa-graduation-cap text-gold-400" aria-hidden="true"></i> For JHS &amp; SHS students and teachers in Ghana
        </p>
        <h1 class="mt-5 text-3xl font-extrabold sm:text-5xl">Learn it. Practise it. <span class="text-gold-400">Pass it.</span></h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-white/75">WASSCE past questions with worked solutions, notes mapped to the GES/NaCCA curriculum, and study tools that work on any phone.</p>

        {{-- Search with filters: past papers (Library) or practice questions. Practice ignores the text and year. --}}
        <form method="GET" action="{{ route('library.index') }}" role="search" aria-label="Search past questions and practice"
              x-data="{ mode: 'papers', level: '', subjects: @js($searchSubjects), get shown() { return this.subjects.filter(s => !this.level || s.level === this.level); } }"
              :action="mode === 'papers' ? '{{ route('library.index') }}' : '{{ route('practice.index') }}'"
              class="mx-auto mt-8 max-w-3xl rounded-3xl bg-white/95 p-3 text-left shadow-lift ring-1 ring-white/40 backdrop-blur sm:p-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="inline-flex rounded-xl bg-gray-100 p-1 text-sm font-semibold" role="group" aria-label="What are you looking for">
                    <button type="button" @click="mode = 'papers'" :aria-pressed="mode === 'papers'" :class="mode === 'papers' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-primary-700'" class="rounded-lg px-3.5 py-1.5 transition"><i class="fas fa-file-lines mr-1.5" aria-hidden="true"></i>Past papers</button>
                    <button type="button" @click="mode = 'practice'" :aria-pressed="mode === 'practice'" :class="mode === 'practice' ? 'bg-white text-primary-700 shadow-sm' : 'text-gray-500 hover:text-primary-700'" class="rounded-lg px-3.5 py-1.5 transition"><i class="fas fa-list-check mr-1.5" aria-hidden="true"></i>Practice questions</button>
                </div>
                <p class="hidden text-xs text-gray-500 sm:block" x-text="mode === 'papers' ? 'Search BECE and WASSCE papers, solutions and notes' : 'Choose an exam and subject to start practising'"></p>
            </div>

            <div class="mt-3 flex items-center gap-2 rounded-2xl bg-gray-50 ring-1 ring-gray-200 focus-within:ring-2 focus-within:ring-gold-500" :hidden="mode !== 'papers'">
                <label for="hero-search" class="sr-only">Search past questions and notes</label>
                <i class="fas fa-magnifying-glass pl-4 text-gray-400" aria-hidden="true"></i>
                <input id="hero-search" type="search" name="q" :disabled="mode !== 'papers'" placeholder="Search a topic or subject, e.g. quadratic equations" class="min-w-0 flex-1 border-0 bg-transparent px-2 py-3 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-0">
            </div>

            <div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-[1fr_1.4fr_1fr_auto]">
                <div>
                    <label for="hero-level" class="sr-only">Exam</label>
                    <select id="hero-level" name="level" x-model="level" @change="$refs.subject.value = ''" class="block w-full rounded-xl border-0 bg-gray-50 px-3 py-3 text-sm text-gray-800 ring-1 ring-gray-200 focus:ring-2 focus:ring-gold-500">
                        <option value="">BECE &amp; WASSCE</option>
                        <option value="jhs">BECE (JHS)</option>
                        <option value="shs">WASSCE (SHS)</option>
                    </select>
                </div>
                <div>
                    <label for="hero-subject" class="sr-only">Subject</label>
                    <select id="hero-subject" name="subject" x-ref="subject" class="block w-full rounded-xl border-0 bg-gray-50 px-3 py-3 text-sm text-gray-800 ring-1 ring-gray-200 focus:ring-2 focus:ring-gold-500">
                        <option value="">All subjects</option>
                        <template x-for="s in shown" :key="s.slug"><option :value="s.slug" x-text="level ? s.name : s.name + ' (' + s.level.toUpperCase() + ')'"></option></template>
                    </select>
                </div>
                <div :hidden="mode !== 'papers'">
                    <label for="hero-year" class="sr-only">Exam year</label>
                    <select id="hero-year" name="year" :disabled="mode !== 'papers'" class="block w-full rounded-xl border-0 bg-gray-50 px-3 py-3 text-sm text-gray-800 ring-1 ring-gray-200 focus:ring-2 focus:ring-gold-500">
                        <option value="">Any year</option>
                        @foreach ($searchYears as $y)<option value="{{ $y }}">{{ $y }}</option>@endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gold-500 px-6 py-3 text-sm font-bold text-primary-950 shadow-gold hover:bg-gold-400 sm:col-span-2 lg:col-span-1 lg:[grid-column:auto]" :class="mode === 'practice' ? 'lg:col-span-2' : ''">
                    <i class="fas fa-magnifying-glass" aria-hidden="true"></i><span x-text="mode === 'papers' ? 'Search' : 'Practise'">Search</span>
                </button>
            </div>
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
</section>

{{-- Live numbers as two-tone stat tiles (the dashboard style). Real counts only; zero is left out. Counts up once when scrolled into view. --}}
@php
    // [value, title, icon, note, colour]: no red.
    $figures = array_filter([
        [$stats['questions'], 'Practice questions', 'fa-list-check', 'Answers explained', 'blue'],
        [$stats['papers'], 'Practice papers', 'fa-stopwatch', 'Timed or study mode', 'green'],
        [$stats['resources'], 'Past papers & notes', 'fa-file-lines', number_format($stats['free']).' free to read', 'purple'],
        [$stats['subjects'], 'Subjects', 'fa-book-open', 'JHS and SHS', 'orange'],
        [$stats['topics'], 'Curriculum topics', 'fa-sitemap', 'Mapped to syllabus', 'slate'],
        [$stats['promotion'] ?? 0, 'Promotion questions', 'fa-chalkboard-user', 'GES teacher exams', 'blue'],
    ], fn ($f) => $f[0] > 0);
    // The figure, marked up so the count-up script can find it.
    $figure = fn (int $n) => new \Illuminate\Support\HtmlString('<span data-count="'.$n.'">'.number_format($n).'</span>');
@endphp
@if ($figures)
    <section class="relative z-10 mx-auto -mt-14 max-w-6xl px-4 md:px-6" aria-label="Otisam in numbers">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($figures as [$value, $title, $icon, $note, $colour])
                <x-stat-tile :color="$colour" :icon="$icon" :title="$title" :sub="$note" :value="$figure((int) $value)" class="rounded-lg shadow-lg" />
            @endforeach
        </div>
    </section>
@endif

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

@push('scripts')
<script>
    // Counts the hero numbers up once, when they scroll into view. Skipped for reduced-motion users.
    (function () {
        var els = document.querySelectorAll('[data-count]');
        if (!els.length || window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) { return; }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) { return; }
                io.unobserve(entry.target);
                var el = entry.target, end = parseInt(el.dataset.count, 10), start = performance.now(), ms = 900;
                (function tick(now) {
                    var t = Math.min(1, (now - start) / ms), eased = 1 - Math.pow(1 - t, 3);
                    el.textContent = Math.round(end * eased).toLocaleString();
                    if (t < 1) { requestAnimationFrame(tick); }
                })(start);
            });
        }, { threshold: 0.4 });
        els.forEach(function (el) { io.observe(el); });
    })();
</script>
@endpush

