@extends('layouts.app')

@section('title', 'Teacher promotion exams')
@section('description', 'Practice papers for GES teacher promotion examinations with sample questions, answers and explanations.')

@php
    use App\Http\Controllers\Public\PromotionController as P;
    $more = max(0, $total - $samples->count());
    // What the "Start" buttons do for this visitor.
    $startLabel = $canPractise ? 'Start' : ($user ? ($profile ? 'Awaiting approval' : 'For teachers only') : 'Sign up to start');
@endphp

@section('content')
<section class="relative overflow-hidden bg-primary-700 bg-crosshatch text-white">
    <div class="pointer-events-none absolute -right-16 -top-20 h-72 w-72 rounded-full bg-gold-500/15 blur-3xl"></div>
    <div class="relative mx-auto max-w-6xl px-4 py-12 sm:py-16">
        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium ring-1 ring-white/15"><i class="fas fa-chalkboard-user text-gold-400" aria-hidden="true"></i> For teachers</p>
        <h1 class="mt-4 text-3xl font-extrabold sm:text-4xl">Teacher promotion exams</h1>
        <p class="mt-3 max-w-2xl text-white/75">Timed practice papers for GES promotion examinations: education acts and agencies, teacher standards, curriculum, educational psychology, assessment and school management. Every answer is explained.</p>
        <dl class="mt-5 flex flex-wrap gap-3 text-sm">
            <div class="rounded-xl bg-white/10 px-4 py-2 ring-1 ring-white/15"><dt class="sr-only">Questions</dt><dd><strong class="font-display text-lg">{{ number_format($allTotal) }}</strong> practice questions</dd></div>
            <div class="rounded-xl bg-white/10 px-4 py-2 ring-1 ring-white/15"><dt class="sr-only">Papers</dt><dd><strong class="font-display text-lg">{{ collect($papers)->where('ready', true)->count() }}</strong> papers open</dd></div>
        </dl>
        <div class="mt-6 flex flex-wrap gap-3">
            @if ($canPractise)
                <a href="{{ route('practice.start', ['subject' => P::SUBJECT, 'count' => 20, 'mode' => 'timed']) }}" class="inline-flex h-12 items-center gap-2 rounded-xl bg-gold-500 px-6 text-[15px] font-bold text-primary-950 no-underline shadow-gold hover:bg-gold-400 hover:text-primary-950"><i class="fas fa-stopwatch" aria-hidden="true"></i> Timed mock (20 questions)</a>
            @elseif (! $user)
                <a href="{{ route('teachers.register') }}" class="inline-flex h-12 items-center gap-2 rounded-xl bg-gold-500 px-6 text-[15px] font-bold text-primary-950 no-underline shadow-gold hover:bg-gold-400 hover:text-primary-950"><i class="fas fa-user-plus" aria-hidden="true"></i> Sign up free to practise</a>
                <a href="{{ route('login') }}" class="inline-flex h-12 items-center gap-2 rounded-xl bg-white/10 px-6 text-[15px] font-bold text-white no-underline ring-1 ring-white/30 hover:bg-white/20 hover:text-white">Sign in</a>
            @endif
            <a href="#samples" class="inline-flex h-12 items-center gap-2 rounded-xl bg-white/10 px-6 text-[15px] font-bold text-white no-underline ring-1 ring-white/30 hover:bg-white/20 hover:text-white"><i class="fas fa-eye" aria-hidden="true"></i> See sample questions</a>
        </div>
    </div>
</section>

<div class="mx-auto max-w-6xl px-4 py-10">
    <x-flash />

    @if ($profile && ! $canPractise)
        <div class="mb-6 rounded-2xl bg-gold-50 p-4 text-sm text-gold-900 ring-1 ring-gold-200">
            <i class="fas fa-hourglass-half mr-1" aria-hidden="true"></i> Your teacher account is <strong>{{ strtolower($profile->verification_status->label()) }}</strong>. Papers open as soon as an administrator approves it.
            <a href="{{ route('teach.profile.edit') }}" class="ml-1 font-semibold">Complete your profile</a> to speed this up.
        </div>
    @endif

    {{-- Papers --}}
    <section aria-labelledby="papers-heading">
        <h2 id="papers-heading" class="text-2xl font-extrabold text-primary-700">Practice papers</h2>
        <p class="mt-1 text-sm text-gray-500">The rank papers are Otisam's own practice sets, weighted towards the skills each step up tends to need (more leadership, administration, HR, finance and policy at senior ranks). They are not official GES rank syllabi or past papers.</p>
        <ul class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($papers as $paper)
                @php
                    $count = min($paper['count'], $totals[$paper['subject'] ?? P::SUBJECT] ?? 0);
                    $href = null;
                    if ($canPractise) {
                        $href = isset($paper['key'])
                            ? route('promotion.paper', $paper['key'])
                            : route('practice.start', ['subject' => $paper['subject'] ?? P::SUBJECT, 'count' => in_array($paper['count'], \App\Http\Controllers\Public\PracticeController::COUNTS, true) ? $paper['count'] : 20, 'mode' => $paper['mode']]);
                    } elseif (! $user) {
                        $href = route('teachers.register');
                    }
                @endphp
                <li class="flex flex-col rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3] {{ $paper['ready'] ? '' : 'opacity-70' }}">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $paper['mode'] === 'timed' ? 'bg-gold-50 text-gold-700' : 'bg-primary-50 text-primary-700' }}"><i class="fas {{ $paper['mode'] === 'timed' ? 'fa-stopwatch' : 'fa-book-open-reader' }}" aria-hidden="true"></i></span>
                        @unless ($paper['ready'])<span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">Coming soon</span>@endunless
                    </div>
                    <h3 class="mt-3 font-bold text-gray-800">{{ $paper['title'] }}</h3>
                    <p class="text-sm text-gray-500">{{ $paper['for'] }}</p>
                    <p class="mt-2 text-xs font-semibold text-gray-500">
                        {{ $paper['ready'] ? $count : $paper['count'] }} questions
                        @if ($paper['mode'] === 'timed') · {{ $paper['ready'] ? $count : $paper['count'] }} minutes @else · no time limit @endif
                    </p>
                    <div class="mt-auto pt-4">
                        @if (! $paper['ready'])
                            <span class="inline-flex h-10 items-center text-sm font-semibold text-gray-400"><i class="fas fa-lock mr-1.5" aria-hidden="true"></i>Being prepared</span>
                        @elseif ($href)
                            <a href="{{ $href }}" class="inline-flex h-10 items-center gap-2 rounded-xl {{ $canPractise ? 'bg-gold-500 text-primary-950 shadow-gold hover:bg-gold-400' : 'bg-primary-600 text-white shadow-navy hover:bg-primary-700 hover:text-white' }} px-4 text-sm font-bold no-underline">
                                @unless ($canPractise)<i class="fas fa-lock" aria-hidden="true"></i>@endunless {{ $startLabel }} <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                            </a>
                        @else
                            <span class="inline-flex h-10 items-center text-sm font-semibold text-gray-500"><i class="fas fa-lock mr-1.5" aria-hidden="true"></i>{{ $startLabel }}</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Sample questions --}}
    <section id="samples" class="mt-12 scroll-mt-24" aria-labelledby="samples-heading">
        <div class="flex flex-wrap items-baseline justify-between gap-3">
            <h2 id="samples-heading" class="text-2xl font-extrabold text-primary-700">Sample questions</h2>
            <p class="text-sm text-gray-500">{{ $samples->count() }} of {{ $total }} · tap “Show answer” to check yourself</p>
        </div>

        <ol class="mt-4 space-y-4">
            @foreach ($samples as $q)
                @php
                    $revision = $q->currentRevision;
                    $correct = $keys[$q->current_revision_id]['correct'] ?? null;
                @endphp
                <li class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]" data-sample>
                    <p class="font-semibold text-gray-800"><span class="mr-2 inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">{{ $loop->iteration }}</span>{{ $revision->stem }}</p>
                    <ul class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                        @foreach ($revision->payload['options'] ?? [] as $o)
                            <li class="rounded-xl border-[1.5px] border-[#ece9f3] px-3 py-2 text-gray-700" @if ($o['id'] === $correct) data-correct @endif><strong class="mr-1">{{ $o['id'] }}.</strong> {{ $o['text'] }}</li>
                        @endforeach
                    </ul>
                    <details class="mt-3 group">
                        <summary class="inline-flex cursor-pointer list-none items-center gap-1 rounded-xl bg-gold-50 px-3 py-1.5 text-sm font-bold text-gold-700"><i class="fas fa-eye" aria-hidden="true"></i> Show answer</summary>
                        <p class="mt-2 rounded-xl bg-green-50 px-3 py-2 text-sm text-green-900"><strong>Answer: {{ $correct }}.</strong> {{ $revision->explanation }}</p>
                    </details>
                </li>
            @endforeach
        </ol>

        @if ($more > 0)
            {{-- The rest of the bank, behind sign-up --}}
            <div class="relative mt-4 overflow-hidden rounded-2xl">
                <div class="pointer-events-none select-none space-y-3 blur-sm" aria-hidden="true">
                    @foreach (range(1, 2) as $i)
                        <div class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
                            <div class="h-4 w-3/4 rounded bg-gray-200"></div>
                            <div class="mt-3 grid gap-2 sm:grid-cols-2">@foreach (range(1, 4) as $j)<div class="h-9 rounded-xl bg-gray-100"></div>@endforeach</div>
                        </div>
                    @endforeach
                </div>
                <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-b from-white/40 to-white/95 p-4">
                    <div class="max-w-md rounded-2xl bg-white p-6 text-center shadow-lift ring-1 ring-[#ece9f3]" data-locked>
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary-600 text-white"><i class="fas fa-lock" aria-hidden="true"></i></span>
                        <h3 class="mt-3 text-lg font-extrabold text-primary-700">{{ $more }} more questions and timed papers</h3>
                        @if ($canPractise)
                            <p class="mt-1 text-sm text-gray-500">Practise them all with explanations.</p>
                            <x-button class="mt-4" variant="accent" :href="route('practice.start', ['subject' => P::SUBJECT, 'count' => 20])" icon="fa-play">Continue practising</x-button>
                        @elseif ($user)
                            <p class="mt-1 text-sm text-gray-500">{{ $profile ? 'They open as soon as your teacher account is approved.' : 'Promotion practice is for teacher accounts.' }}</p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Create a free teacher account to see every question, take timed mock papers and track your scores.</p>
                            <div class="mt-4 flex flex-wrap justify-center gap-2">
                                <x-button variant="accent" :href="route('teachers.register')" icon="fa-user-plus">Sign up free</x-button>
                                <x-button variant="outline" :href="route('login')">Sign in</x-button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- How it works --}}
    <section class="mt-12 grid gap-4 md:grid-cols-3" aria-label="How it works">
        @foreach ([
            ['fa-user-check', 'Register and get verified', 'Sign up with your phone number, then add your school and GES staff number. An administrator checks it.'],
            ['fa-stopwatch', 'Sit timed practice papers', 'Papers run against the clock like the real exam, about a minute per question.'],
            ['fa-chart-column', 'Learn from every answer', 'See your score straight away, with the correct answer and an explanation for each question.'],
        ] as $i => [$icon, $title, $text])
            <div class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-600 text-white"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Step {{ $i + 1 }}</p>
                <h3 class="font-semibold text-gray-800">{{ $title }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $text }}</p>
            </div>
        @endforeach
    </section>

    <p class="mt-6 text-xs text-gray-500">These are practice and professional-development assessments written by {{ config('app.name') }}. They are not official GES promotion examinations or past questions.</p>
</div>
@endsection
