@extends('layouts.app')

@section('title', 'Past Questions: BECE & WASSCE')
@section('description', 'BECE and WASSCE past questions by subject and year, plus exam-style practice papers with answers, for JHS and SHS students in Ghana.')

@section('content')
@php $tab = $subject?->educationLevel?->code ?? strtoupper($filters['level'] ?? ''); @endphp
<div class="mx-auto max-w-6xl px-4 py-8 sm:py-12">
    <h1 class="text-3xl font-extrabold text-gray-800">Past Questions</h1>
    <p class="mt-1 text-gray-500">BECE and WASSCE papers by subject and year, with exam-style practice papers to sit online.</p>

    <nav aria-label="Exam" class="mt-5 flex flex-wrap gap-2 text-sm">
        @foreach (['' => 'BECE & WASSCE', 'JHS' => 'BECE (JHS)', 'SHS' => 'WASSCE (SHS)'] as $code => $label)
            <a href="{{ route('library.index', array_filter(['level' => strtolower($code)])) }}" @if ($tab === $code && ! $subject) aria-current="page" @endif
               class="rounded-full px-4 py-1.5 font-semibold no-underline {{ $tab === $code ? 'bg-primary-600 text-white' : 'bg-white text-primary-700 ring-1 ring-[#e2e0ea] hover:ring-gold-500' }}">{{ $label }}</a>
        @endforeach
    </nav>

    @if ($paperSubjects->isNotEmpty())
        @foreach ($paperSubjects as $levelName => $cards)
            <h2 class="mt-8 text-lg font-extrabold text-gray-800">{{ $levelName }}</h2>
            <ul class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($cards as $s)
                    <li>
                        <a href="{{ route('library.index', ['subject' => $s->slug]) }}" class="flex h-full flex-col justify-between rounded-2xl bg-white p-4 no-underline shadow-card ring-1 ring-[#ece9f3] transition hover:-translate-y-0.5 hover:shadow-md">
                            <span class="font-semibold text-gray-800">{{ $s->name }}</span>
                            <span class="mt-2 flex flex-wrap gap-1.5 text-[11px] font-semibold">
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-600">{{ $s->past_count ? $s->past_count.' past '.\Illuminate\Support\Str::plural('paper', $s->past_count) : 'Past papers coming' }}</span>
                                @if ($s->papers_count)<span class="rounded-full bg-gold-100 px-2 py-0.5 text-gold-800">{{ $s->papers_count }} practice {{ \Illuminate\Support\Str::plural('paper', $s->papers_count) }}</span>@endif
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    @elseif ($subject)
        <div class="mt-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">{{ $examName }} · {{ $subject->educationLevel?->name }}</p>
                <h2 class="mt-1 text-2xl font-extrabold text-gray-800">{{ $subject->name }}</h2>
            </div>
            <a href="{{ route('library.index', ['level' => strtolower($subject->educationLevel?->code ?? '')]) }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>All {{ $examName }} subjects</a>
        </div>

        <section aria-labelledby="years-title" class="mt-5 rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
            <h3 id="years-title" class="font-bold text-gray-800">{{ $examName }} past papers by year</h3>
            <p class="text-sm text-gray-500">Years with papers open them below. The others are not available on {{ config('app.name') }} yet.</p>
            <ul class="mt-4 grid grid-cols-4 gap-2 sm:grid-cols-8 lg:grid-cols-12">
                @foreach ($examYears as $y)
                    <li>
                        @if (isset($yearCounts[$y]))
                            <a href="{{ route('library.index', ['subject' => $subject->slug, 'year' => $y]) }}" @if ((int) ($filters['year'] ?? 0) === $y) aria-current="true" @endif
                               class="flex h-14 flex-col items-center justify-center rounded-xl text-sm font-bold no-underline {{ (int) ($filters['year'] ?? 0) === $y ? 'bg-primary-600 text-white' : 'bg-primary-50 text-primary-700 hover:bg-primary-100' }}">{{ $y }}<span class="text-[10px] font-semibold opacity-75">{{ $yearCounts[$y] }} {{ \Illuminate\Support\Str::plural('paper', $yearCounts[$y]) }}</span></a>
                        @else
                            <span class="flex h-14 flex-col items-center justify-center rounded-xl bg-gray-50 text-sm font-semibold text-gray-400" title="{{ $examName }} {{ $y }}: not available yet">{{ $y }}<span class="text-[10px] font-normal">not yet</span></span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>

        @if ($subjectPapers->isNotEmpty())
            <section aria-labelledby="practice-papers-title" class="mt-6">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h3 id="practice-papers-title" class="text-lg font-bold text-gray-800">{{ $examName }}-style practice papers</h3>
                        <p class="text-sm text-gray-500">Full objective papers written by {{ config('app.name') }} in the style of the {{ $examName }}, with answers and explanations. They are not WAEC papers.</p>
                    </div>
                    @if ($subject->educationLevel && ! $subject->isProfessional())
                        <a href="{{ route('study.subject', [strtolower($subject->educationLevel->code), $subject]) }}" class="text-sm font-semibold no-underline"><i class="fas fa-layer-group mr-1" aria-hidden="true"></i>Practice by topic</a>
                    @endif
                </div>
                @include('public.papers._list', ['papers' => $subjectPapers])
            </section>
        @endif
    @endif

    <details class="mt-10 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]" @if (array_filter(\Illuminate\Support\Arr::except($filters, ['subject', 'level']))) open @endif>
        <summary class="cursor-pointer font-semibold text-primary-700"><i class="fas fa-magnifying-glass mr-1" aria-hidden="true"></i>Search all papers, solutions and notes</summary>
    @php $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038] focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30'; @endphp
    <form method="GET" class="mt-4" role="search" data-catalogue-filters>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <label for="q" class="mb-1 block text-[13px] font-bold text-primary-800">Search</label>
                <input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="{{ $control }}" placeholder="e.g. quadratic, osmosis, 2019">
            </div>
            <div>
                <label for="level" class="mb-1 block text-[13px] font-bold text-primary-800">Level</label>
                <select id="level" name="level" class="{{ $control }}" data-resets="class,subject">
                    <option value="">JHS &amp; SHS</option>
                    @foreach ($levels as $l)<option value="{{ strtolower($l->code) }}" @selected($level?->id === $l->id)>{{ $l->name }} ({{ $l->code }})</option>@endforeach
                </select>
            </div>
            <div>
                <label for="class" class="mb-1 block text-[13px] font-bold text-primary-800">Class</label>
                <select id="class" name="class" class="{{ $control }}">
                    <option value="">All classes</option>
                    @foreach (($level ? collect([$level]) : $levels) as $l)
                        @foreach ($l->classLevels as $c)<option value="{{ $c->id }}" @selected((int) ($filters['class'] ?? 0) === $c->id)>{{ $c->name }}</option>@endforeach
                    @endforeach
                </select>
            </div>
            <div>
                <label for="subject" class="mb-1 block text-[13px] font-bold text-primary-800">Subject</label>
                <select id="subject" name="subject" class="{{ $control }}">
                    <option value="">All subjects</option>
                    @foreach (($level ? collect([$level]) : $levels) as $l)
                        <optgroup label="{{ $l->name }}">
                            @foreach ($l->subjects as $s)<option value="{{ $s->slug }}" @selected(($filters['subject'] ?? '') === $s->slug)>{{ $s->name }}</option>@endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="exam" class="mb-1 block text-[13px] font-bold text-primary-800">Exam</label>
                <select id="exam" name="exam" class="{{ $control }}">
                    <option value="">Any exam</option>
                    @foreach ($examTypes as $e)<option value="{{ $e->id }}" @selected((int) ($filters['exam'] ?? 0) === $e->id)>{{ $e->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="year" class="mb-1 block text-[13px] font-bold text-primary-800">Exam year</label>
                <select id="year" name="year" class="{{ $control }}">
                    <option value="">Any year</option>
                    @foreach ($years as $y)<option value="{{ $y }}" @selected((int) ($filters['year'] ?? 0) === $y)>{{ $y }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="category" class="mb-1 block text-[13px] font-bold text-primary-800">Type</label>
                <select id="category" name="category" class="{{ $control }}">
                    <option value="">All types</option>
                    @foreach ($categories as $c)<option value="{{ $c->slug }}" @selected(($filters['category'] ?? '') === $c->slug)>{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="access" class="mb-1 block text-[13px] font-bold text-primary-800">Access</label>
                <select id="access" name="access" class="{{ $control }}">
                    <option value="">Free and premium</option>
                    <option value="free" @selected(($filters['access'] ?? '') === 'free')>Free only</option>
                    <option value="premium" @selected(($filters['access'] ?? '') === 'premium')>Premium only</option>
                </select>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <x-button icon="fa-filter">Show results</x-button>
            @if (array_filter($filters))<a href="{{ route('library.index') }}" class="rounded-xl px-3 py-2 text-sm font-semibold no-underline hover:bg-gray-100"><i class="fas fa-xmark mr-1" aria-hidden="true"></i>Clear all</a>@endif
        </div>
    </form>
    @include('public.partials.auto-filter-script')
    </details>

    @if ($practiceCount > 0)
        <a href="{{ route('practice.index', ['subject' => $subject->slug]) }}" class="mt-6 flex items-center justify-between gap-3 rounded-2xl bg-gold-50 p-4 text-primary-700 no-underline shadow-card ring-1 ring-[#ece9f3] hover:bg-gold-100">
            <span><i class="fas fa-list-check mr-2" aria-hidden="true"></i>{{ number_format($practiceCount) }} practice {{ \Illuminate\Support\Str::plural('question', $practiceCount) }} with answers for {{ $subject->name }}</span>
            <span class="font-semibold whitespace-nowrap">Practise now &rarr;</span>
        </a>
    @endif

    <h2 class="mt-8 text-xl font-extrabold text-gray-800">{{ $subject ? $subject->name.' papers, solutions and notes' : 'Papers, solutions and notes' }}</h2>
    <p class="mt-1 text-sm text-gray-500" role="status">{{ $resources->total() }} {{ \Illuminate\Support\Str::plural('item', $resources->total()) }}</p>

    @if ($resources->isEmpty())
        <div class="mt-3 rounded-2xl bg-white p-6 shadow-card ring-1 ring-[#ece9f3]">
            <p class="text-gray-500">{{ array_filter($filters) ? 'No documents match these filters yet. Try fewer filters.' : 'Past papers, notes and marking schemes are being added to the Library.' }} In the meantime, practise with answers and explanations:</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <a href="{{ route('practice.index', array_filter(['subject' => $subject?->slug])) }}" class="rounded-xl bg-gold-50 p-4 font-semibold text-primary-700 no-underline ring-1 ring-[#ece9f3] hover:bg-gold-100"><i class="fas fa-list-check mr-2" aria-hidden="true"></i>Practice questions</a>
                <a href="{{ $subject ? route('papers.subject', $subject) : route('papers.index') }}" class="rounded-xl bg-gold-50 p-4 font-semibold text-primary-700 no-underline ring-1 ring-[#ece9f3] hover:bg-gold-100"><i class="fas fa-stopwatch mr-2" aria-hidden="true"></i>WASSCE/BECE-style papers</a>
                <a href="{{ route('study.index') }}" class="rounded-xl bg-gold-50 p-4 font-semibold text-primary-700 no-underline ring-1 ring-[#ece9f3] hover:bg-gold-100"><i class="fas fa-layer-group mr-2" aria-hidden="true"></i>Study by class</a>
            </div>
        </div>
    @else
        <ul class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($resources as $resource)
                @include('public.library._card')
            @endforeach
        </ul>
        <div class="mt-6">{{ $resources->links('pagination::tailwind') }}</div>
    @endif
</div>
@endsection
