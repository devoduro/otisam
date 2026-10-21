@extends('layouts.app')

@section('title', $subject->name.($class ? ' · '.$class->name : ''))
@section('description', 'Everything for '.$subject->name.': topics, '.$subject->examStyle().' past questions, practice papers, video lessons and lesson plans.')

@php
    $exam = $subject->examStyle();
    $classId = $class?->id;
    $pastCategory = $level->code === 'JHS' ? 'bece-past-questions' : 'wassce-past-questions';
    $plural = fn (int $n, string $word) => number_format($n).' '.\Illuminate\Support\Str::plural($word, $n);
    // label, description, icon, colour, url, count text (null hides the count)
    $tiles = [
        ['Curriculum topics', 'Strands, sub-strands and topics for the subject.', 'fa-sitemap', 'from-rose-500 to-rose-600', route('subjects.show', $subject), $counts['topics'] ? $plural($counts['topics'], 'topic') : null],
        ['Past questions', $exam.' papers, marking schemes and solutions in the Library.', 'fa-file-lines', 'from-indigo-500 to-indigo-600', route('library.index', ['subject' => $subject->slug, 'category' => $pastCategory]), $counts['past'] ? $plural($counts['past'], 'paper') : null],
        ['Practice papers', 'Full '.$exam.'-style objective papers: study mode or timed exam.', 'fa-stopwatch', 'from-amber-500 to-orange-600', route('papers.subject', $subject), $counts['papers'] ? $plural($counts['papers'], 'paper') : null],
        ['Take a quiz', 'Pick a topic and difficulty, practise at your own pace.', 'fa-list-check', 'from-teal-500 to-teal-600', route('practice.index', array_filter(['subject' => $subject->slug, 'year' => $classId])), $counts['questions'] ? $plural($counts['questions'], 'question') : null],
        ['Timed test', '20 random questions against the clock.', 'fa-gauge-high', 'from-cyan-500 to-sky-600', route('practice.start', array_filter(['subject' => $subject->slug, 'year' => $classId, 'count' => 20, 'mode' => 'timed'])), null],
        ['Video lessons', 'Teachers explain the topics.', 'fa-circle-play', 'from-purple-500 to-purple-600', route('videos.index', array_filter(['subject' => $subject->slug, 'year' => $classId])), $counts['videos'] ? $plural($counts['videos'], 'video') : null],
        ['Lesson plans', 'Weekly plans from teachers.', 'fa-clipboard-list', 'from-emerald-500 to-emerald-600', route('lesson-plans.index', array_filter(['subject' => $subject->slug, 'year' => $classId])), $counts['plans'] ? $plural($counts['plans'], 'plan') : null],
        ['Notes & downloads', 'Lesson notes, revision notes and worksheets.', 'fa-download', 'from-slate-500 to-slate-600', route('downloads.index', ['subject' => $subject->slug]), null],
        ['Top scorers', 'Who scored highest on '.$subject->name.' papers this month.', 'fa-trophy', 'from-yellow-500 to-amber-600', route('leaderboard', ['subject' => $subject->slug]), null],
    ];
@endphp

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 md:px-6 sm:py-12">
    <a href="{{ route('study.level', [strtolower($level->code), 'class' => $classId, 'category' => $subject->category]) }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>{{ $class?->name ?? $level->name }} subjects</a>
    <p class="eyebrow mt-4">{{ collect([$class?->name ?? $level->name, $subject->category === 'core' ? 'Core' : 'Elective', $exam])->implode(' · ') }}</p>
    <div class="mt-1 flex flex-wrap items-end justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-gray-800">{{ $subject->name }}</h1>
        <dl class="flex flex-wrap gap-2 text-sm">
            @foreach (['papers' => 'practice papers', 'questions' => 'questions', 'topics' => 'topics'] as $key => $label)
                <div class="rounded-xl bg-white px-4 py-2 shadow-card ring-1 ring-[#ece9f3]"><dt class="sr-only">{{ $label }}</dt><dd><strong class="font-display text-lg text-primary-700">{{ number_format($counts[$key]) }}</strong> <span class="text-gray-500">{{ $label }}</span></dd></div>
            @endforeach
        </dl>
    </div>

    <nav aria-label="Class" class="mt-4 flex flex-wrap gap-1">
        <a href="{{ route('study.subject', [strtolower($level->code), $subject]) }}" @if (! $class) aria-current="page" @endif class="rounded-full px-3 py-1 text-sm font-semibold no-underline {{ ! $class ? 'bg-primary-600 text-white' : 'bg-white text-primary-700 ring-1 ring-[#e2e0ea]' }}">All classes</a>
        @foreach ($level->classLevels as $y)
            <a href="{{ route('study.subject', [strtolower($level->code), $subject, 'class' => $y->id]) }}" @if ($classId === $y->id) aria-current="page" @endif class="rounded-full px-3 py-1 text-sm font-semibold no-underline {{ $classId === $y->id ? 'bg-primary-600 text-white' : 'bg-white text-primary-700 ring-1 ring-[#e2e0ea]' }}">{{ $y->name }}</a>
        @endforeach
    </nav>

    @if ($counts['questions'])
        <section class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-primary-700 bg-crosshatch p-5 text-white shadow-card">
            <div>
                <h2 class="text-lg font-bold">Quick practice</h2>
                <p class="text-sm text-white/75">{{ \App\Http\Controllers\Public\StudyController::QUICK_COUNT }} random questions{{ $class ? ' for '.$class->name : '' }}, one at a time, with the answer and explanation straight after each.</p>
            </div>
            <a href="{{ route('study.quick', [strtolower($level->code), $subject, 'class' => $classId]) }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-gold-500 px-5 font-bold text-primary-950 no-underline shadow-gold hover:bg-gold-400 hover:text-primary-950"><i class="fas fa-bolt" aria-hidden="true"></i>Start practising</a>
        </section>
    @endif

    <ul class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($tiles as [$label, $text, $icon, $colour, $url, $count])
            <li>
                <a href="{{ $url }}" class="group flex h-full gap-4 rounded-2xl bg-white p-5 no-underline shadow-card ring-1 ring-[#ece9f3] transition hover:-translate-y-0.5 hover:shadow-md">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $colour }} text-xl text-white"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                    <span>
                        <span class="block font-bold text-gray-800 group-hover:text-primary-700">{{ $label }}</span>
                        <span class="mt-0.5 block text-sm text-gray-500">{{ $text }}</span>
                        @if ($count)<span class="mt-1.5 inline-block rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-600">{{ $count }}</span>@endif
                    </span>
                </a>
            </li>
        @endforeach
    </ul>

    @if ($topics->isNotEmpty())
        <h2 class="mt-10 text-lg font-bold text-gray-800">Practice by topic</h2>
        <p class="text-sm text-gray-500">{{ $topics->count() }} {{ \Illuminate\Support\Str::plural('topic', $topics->count()) }}{{ $class ? ' for '.$class->name : '' }}. Quick practice shows the answer after each question; a test marks them all at the end.</p>
        <ul class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($topics as $t)
                <li class="flex flex-col justify-between gap-3 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $t->title }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($t->questions_count) }} {{ \Illuminate\Support\Str::plural('question', $t->questions_count) }}@if ($t->classLevel) · {{ $t->classLevel->name }}@endif</p>
                    </div>
                    <div class="flex gap-2 text-sm">
                        <a href="{{ route('study.quick', [strtolower($level->code), $subject, 'topic' => $t->id, 'class' => $classId]) }}" class="rounded-lg bg-primary-600 px-3 py-1.5 font-semibold text-white no-underline hover:bg-primary-700 hover:text-white"><i class="fas fa-bolt mr-1" aria-hidden="true"></i>Practise</a>
                        <a href="{{ route('practice.start', array_filter(['subject' => $subject->slug, 'topic' => $t->id, 'year' => $classId, 'count' => 10])) }}" class="rounded-lg px-3 py-1.5 font-semibold text-primary-700 no-underline ring-1 ring-[#e2e0ea] hover:ring-gold-500">Test</a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
