@extends('layouts.app')

@section('title', 'Practice questions')
@section('description', 'Free WASSCE and BECE style practice questions with answers and explanations. Filter by level, year, subject, topic and difficulty, or take a timed test.')

@php
    use App\Http\Controllers\Public\PracticeController as P;
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038] focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30';
    $examFor = fn ($lvl) => $lvl?->code === 'JHS' ? 'BECE' : 'WASSCE';
@endphp

@section('content')
{{-- Header --}}
<section class="relative overflow-hidden bg-primary-700 bg-crosshatch text-white">
    <div class="pointer-events-none absolute -right-16 -top-20 h-72 w-72 rounded-full bg-gold-500/15 blur-3xl"></div>
    <div class="relative mx-auto max-w-6xl px-4 py-10 sm:py-14">
        <p class="eyebrow">Self-test</p>
        <h1 class="mt-1 text-3xl font-extrabold sm:text-4xl">Practice questions</h1>
        <p class="mt-2 max-w-2xl text-white/75">Build your own practice set: choose the level, year, subject, topic and difficulty, then practise at your own pace or against the clock. Every answer comes with an explanation.</p>
        <dl class="mt-6 flex flex-wrap gap-3 text-sm">
            <div class="rounded-xl bg-white/10 px-4 py-2 ring-1 ring-white/15"><dt class="sr-only">Questions</dt><dd><strong class="font-display text-lg">{{ number_format($counts->sum()) }}</strong> questions</dd></div>
            <div class="rounded-xl bg-white/10 px-4 py-2 ring-1 ring-white/15"><dt class="sr-only">Subjects</dt><dd><strong class="font-display text-lg">{{ $counts->count() }}</strong> subjects</dd></div>
            <div class="rounded-xl bg-white/10 px-4 py-2 ring-1 ring-white/15"><dt class="sr-only">Exams</dt><dd>WASSCE &amp; BECE style</dd></div>
        </dl>
        <a href="{{ route('papers.index') }}" class="mt-5 inline-flex h-11 items-center gap-2 rounded-xl bg-gold-500 px-5 text-[15px] font-bold text-primary-950 no-underline shadow-gold hover:bg-gold-400 hover:text-primary-950"><i class="fas fa-file-lines" aria-hidden="true"></i>Full practice papers <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
    </div>
</section>

<div class="mx-auto max-w-6xl px-4 py-8">
    <x-flash />

    {{-- Set builder --}}
    <form method="GET" action="{{ route('practice.index') }}" data-catalogue-filters class="-mt-16 relative rounded-2xl bg-white p-5 shadow-lift ring-1 ring-[#ece9f3] sm:-mt-20">
        <h2 class="text-lg font-extrabold text-primary-700">Build a practice set</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="p-level" class="mb-1 block text-[13px] font-bold text-primary-800">Level</label>
                <select id="p-level" name="level" class="{{ $control }}" data-resets="year,subject,topic">
                    <option value="">JHS &amp; SHS</option>
                    @foreach ($levels as $l)<option value="{{ strtolower($l->code) }}" @selected($level?->id === $l->id)>{{ $l->name }} ({{ $examFor($l) }})</option>@endforeach
                </select>
            </div>
            <div>
                <label for="p-year" class="mb-1 block text-[13px] font-bold text-primary-800">Year</label>
                <select id="p-year" name="year" class="{{ $control }}">
                    <option value="">All years</option>
                    @foreach (($level ? $level->classLevels : $levels->flatMap->classLevels) as $y)<option value="{{ $y->id }}" @selected((int) ($filters['year'] ?? 0) === $y->id)>{{ $y->name }} (Year {{ $y->sort }})</option>@endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="p-subject" class="mb-1 block text-[13px] font-bold text-primary-800">Subject</label>
                <select id="p-subject" name="subject" class="{{ $control }}" data-resets="topic">
                    <option value="">Choose a subject</option>
                    @foreach ($levels as $l)
                        @continue(! isset($subjects[$l->id]))
                        <optgroup label="{{ $l->name }}">
                            @foreach ($subjects[$l->id] as $s)<option value="{{ $s->slug }}" @selected($subject?->id === $s->id)>{{ $s->name }} ({{ $counts[$s->id] }})</option>@endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="p-topic" class="mb-1 block text-[13px] font-bold text-primary-800">Topic</label>
                <select id="p-topic" name="topic" class="{{ $control }}" @disabled($topics->isEmpty())>
                    <option value="">{{ $subject ? ($topics->isEmpty() ? 'All topics (none tagged yet)' : 'All topics') : 'Choose a subject first' }}</option>
                    @foreach ($topics as $t)<option value="{{ $t->id }}" @selected((int) ($filters['topic'] ?? 0) === $t->id)>{{ $t->code ? $t->code.' ' : '' }}{{ $t->classLevel ? $t->classLevel->name.' · ' : '' }}{{ \Illuminate\Support\Str::limit($t->title, 70) }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="p-difficulty" class="mb-1 block text-[13px] font-bold text-primary-800">Difficulty</label>
                <select id="p-difficulty" name="difficulty" class="{{ $control }}">
                    <option value="">Mixed</option>
                    @foreach (array_keys(P::DIFFICULTIES) as $d)<option value="{{ $d }}" @selected(($filters['difficulty'] ?? '') === $d)>{{ ucfirst($d) }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="p-count" class="mb-1 block text-[13px] font-bold text-primary-800">Questions</label>
                <select id="p-count" name="count" class="{{ $control }}" data-no-submit>
                    @foreach (P::COUNTS as $c)<option value="{{ $c }}" @selected((int) ($filters['count'] ?? 10) === $c)>{{ $c }} questions</option>@endforeach
                </select>
            </div>
        </div>

        <fieldset class="mt-4">
            <legend class="mb-2 text-[13px] font-bold text-primary-800">Mode</legend>
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border-[1.5px] border-[#e2e0ea] p-3 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                    <input type="radio" name="mode" value="practice" class="mt-1 accent-[#28409e]" @checked(($filters['mode'] ?? 'practice') === 'practice')>
                    <span><span class="block font-semibold text-gray-800"><i class="fas fa-book-open-reader mr-1 text-primary-600" aria-hidden="true"></i> Practice</span><span class="text-sm text-gray-500">No time limit. Take your time and learn from the explanations.</span></span>
                </label>
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border-[1.5px] border-[#e2e0ea] p-3 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                    <input type="radio" name="mode" value="timed" class="mt-1 accent-[#28409e]" @checked(($filters['mode'] ?? '') === 'timed')>
                    <span><span class="block font-semibold text-gray-800"><i class="fas fa-stopwatch mr-1 text-gold-600" aria-hidden="true"></i> Timed test</span><span class="text-sm text-gray-500">About {{ P::MINUTES_PER_QUESTION }} minute per question, like the real exam. Submits itself when time is up.</span></span>
                </label>
            </div>
        </fieldset>

        <div class="mt-5 flex flex-wrap items-center gap-3">
            <button type="submit" formaction="{{ route('practice.start') }}" @disabled(! $subject || $available === 0)
                    class="inline-flex h-12 items-center gap-2 rounded-xl bg-gold-500 px-6 text-[15px] font-bold text-primary-950 shadow-gold hover:bg-gold-400 disabled:cursor-not-allowed disabled:opacity-50">
                Start practice <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
            </button>
            <p class="text-sm text-gray-500" aria-live="polite">
                @if (! $subject) Choose a subject to start.
                @elseif ($available === 0) No questions match these filters. Try fewer filters.
                @else {{ $available }} {{ \Illuminate\Support\Str::plural('question', $available) }} match. You will get {{ min($available, (int) ($filters['count'] ?? 10)) }}.
                @endif
            </p>
            @if (collect($filters)->except(['count', 'mode'])->filter()->isNotEmpty())
                <a href="{{ route('practice.index') }}" class="ml-auto text-sm font-semibold no-underline"><i class="fas fa-xmark mr-1" aria-hidden="true"></i>Clear filters</a>
            @endif
        </div>
    </form>

    @if ($recent->isNotEmpty())
        <section class="mt-8" aria-labelledby="recent-heading">
            <h2 id="recent-heading" class="text-sm font-bold uppercase tracking-wide text-gray-500">Your recent scores</h2>
            <ul class="mt-2 flex flex-wrap gap-2">
                @foreach ($recent as $r)
                    @php $pct = $r['total'] ? round($r['score'] / $r['total'] * 100) : 0; @endphp
                    <li><a href="{{ route('practice.index', ['subject' => $r['slug']]) }}" class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-sm no-underline shadow-card ring-1 ring-[#ece9f3]">
                        <span class="font-semibold text-gray-800">{{ $r['subject'] }}</span>
                        <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $pct >= 80 ? 'bg-green-100 text-green-800' : ($pct >= 50 ? 'bg-gold-100 text-gold-800' : 'bg-red-100 text-red-800') }}">{{ $r['score'] }}/{{ $r['total'] }}</span>
                    </a></li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- Quick start by subject --}}
    @forelse ($levels as $l)
        @continue(! isset($subjects[$l->id]))
        <section class="mt-10" aria-labelledby="practice-level-{{ $l->id }}">
            <div class="flex items-baseline justify-between gap-3">
                <h2 id="practice-level-{{ $l->id }}" class="text-xl font-extrabold text-primary-700">{{ $l->name }}</h2>
                <span class="text-sm text-gray-500">{{ $examFor($l) }} style · quick start, 10 mixed questions</span>
            </div>
            <ul class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($subjects[$l->id] as $s)
                    <li>
                        <div class="flex h-full items-center justify-between gap-3 rounded-2xl bg-white px-4 py-3 shadow-card ring-1 ring-[#ece9f3]">
                            <a href="{{ route('practice.index', ['subject' => $s->slug]) }}" class="min-w-0 no-underline" title="Filter {{ $s->name }} questions">
                                <span class="block truncate font-semibold text-gray-800">{{ $s->name }}</span>
                                <span class="text-xs text-gray-500">{{ $counts[$s->id] }} {{ \Illuminate\Support\Str::plural('question', $counts[$s->id]) }}</span>
                            </a>
                            <a href="{{ route('practice.start', ['subject' => $s->slug]) }}" class="inline-flex shrink-0 items-center gap-1 rounded-xl bg-gold-50 px-3 py-1.5 text-sm font-bold text-gold-700 no-underline hover:bg-gold-100">Start <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @empty
    @endforelse

    @if ($subjects->isEmpty())
        <p class="mt-8 rounded-2xl bg-white p-6 text-gray-500 shadow-card ring-1 ring-[#ece9f3]">Practice questions are being prepared. Check back soon.</p>
    @endif

    <p class="mt-10 text-xs text-gray-400">Demo questions are written by Otisam in the style of the WASSCE and BECE. They are not WAEC past questions. Official past papers are in the <a href="{{ route('library.index') }}">library</a>.</p>
</div>
@endsection

@push('scripts')
<script>
    // Filters refresh the page (to update counts and topics); the question count and mode do not.
    document.querySelectorAll('[data-catalogue-filters] select:not([data-no-submit])').forEach(function (select) {
        select.addEventListener('change', function () {
            (select.dataset.resets || '').split(',').filter(Boolean).forEach(function (name) {
                var field = select.form.elements[name];
                if (field) { field.value = ''; }
            });
            select.form.requestSubmit();
        });
    });
</script>
@endpush
