@extends('layouts.app')

@section('title', $subject->name.' practice')
@section('description', 'Practise '.$subject->name.' objective questions with instant marking.')

@php $exam = $subject->examStyle(); @endphp

@section('content')
<div class="mx-auto max-w-6xl px-4 py-6 sm:py-10">
    <a href="{{ $subject->isProfessional() ? route('promotion') : route('practice.index', array_filter($filters)) }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i> {{ $subject->isProfessional() ? 'Promotion exams' : 'Change filters' }}</a>
    <p class="eyebrow mt-4">{{ $subject->educationLevel->name }} · {{ $exam }} style{{ ($filters['difficulty'] ?? null) ? ' · '.ucfirst($filters['difficulty']) : '' }}</p>
    <h1 class="mt-1 text-3xl font-extrabold text-primary-700">{{ $subject->name }}</h1>

    @if ($questions->isEmpty())
        <div class="mt-6 rounded-2xl bg-white p-6 shadow-card ring-1 ring-[#ece9f3]">
            <p class="text-gray-600">No practice questions match these filters yet.</p>
            <x-button class="mt-4" variant="outline" :href="route('practice.index', ['subject' => $subject->slug])" icon="fa-filter">Try other filters</x-button>
        </div>
    @else
        <form method="POST" action="{{ route('practice.mark') }}" id="practice-form" class="mt-6 grid gap-6 lg:grid-cols-[1fr_17rem]" data-total="{{ $questions->count() }}" @if ($minutes) data-seconds="{{ $minutes * 60 }}" @endif>
            @csrf

            <div class="order-2 space-y-4 lg:order-1">
                @foreach ($questions as $question)
                    @php $revision = $question->currentRevision; @endphp
                    <fieldset id="q{{ $loop->iteration }}" class="scroll-mt-40 rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]" data-question="{{ $loop->iteration }}">
                        <legend class="sr-only">Question {{ $loop->iteration }}</legend>
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-semibold text-gray-800"><span class="mr-2 inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">{{ $loop->iteration }}</span>{{ $revision->stem }}</p>
                            <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-500">{{ $question->difficulty <= 2 ? 'Easy' : ($question->difficulty === 3 ? 'Medium' : 'Hard') }}</span>
                        </div>
                        @if ($question->type === 'fill_in')
                            <label class="mt-3 block text-sm font-semibold text-gray-600" for="a{{ $loop->iteration }}">Your answer (fill in the blank)</label>
                            <input id="a{{ $loop->iteration }}" type="text" name="answers[{{ $question->ulid }}]" maxlength="120" autocomplete="off" class="mt-1 h-11 w-full rounded-xl border-[1.5px] border-[#e2e0ea] px-3 text-sm focus:border-primary-600 focus:outline-none sm:max-w-md" data-text>
                        @elseif ($question->type === 'theory')
                            <p class="mt-1 text-xs font-semibold text-gray-400">Theory question, {{ $question->default_marks + 0 }} marks. Write your answer, then compare it with the model answer after marking. It is not scored by the computer.</p>
                            <label class="sr-only" for="a{{ $loop->iteration }}">Your answer</label>
                            <textarea id="a{{ $loop->iteration }}" name="answers[{{ $question->ulid }}]" rows="5" maxlength="3000" class="mt-2 w-full rounded-xl border-[1.5px] border-[#e2e0ea] px-3 py-2 text-sm focus:border-primary-600 focus:outline-none" data-text></textarea>
                        @else
                        <div class="mt-3 grid gap-2 sm:grid-cols-2">
                            @foreach ($revision->payload['options'] ?? [] as $option)
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border-[1.5px] border-[#e2e0ea] px-3 py-2.5 text-sm hover:border-gold-500 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                                    <input type="radio" name="answers[{{ $question->ulid }}]" value="{{ $option['id'] }}" class="mt-0.5 accent-[#28409e]">
                                    <span><strong class="mr-1">{{ $option['id'] }}.</strong> {{ $option['text'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @endif
                    </fieldset>
                @endforeach
            </div>

            {{-- Progress, timer and navigator: sticky beside the questions on wide screens, on top on phones --}}
            <aside class="order-1 lg:order-2">
                <div class="sticky top-20 space-y-4 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]">
                    @if ($minutes)
                        <div class="rounded-xl bg-primary-700 p-3 text-center text-white" role="timer" aria-live="off">
                            <p class="text-xs uppercase tracking-wider text-white/70">Time left</p>
                            <p class="font-display text-3xl font-extrabold tabular-nums" data-timer>{{ sprintf('%02d:00', $minutes) }}</p>
                        </div>
                    @endif
                    <div>
                        <div class="flex justify-between text-sm"><span class="font-semibold text-gray-700">Answered</span><span class="tabular-nums text-gray-500"><span data-answered>0</span> / {{ $questions->count() }}</span></div>
                        <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100"><div class="h-full w-0 rounded-full bg-gold-500 transition-all" data-progress></div></div>
                    </div>
                    <nav aria-label="Jump to question" class="grid grid-cols-5 gap-1.5">
                        @foreach ($questions as $question)
                            <a href="#q{{ $loop->iteration }}" data-nav="{{ $loop->iteration }}" class="flex h-9 items-center justify-center rounded-lg bg-gray-100 text-sm font-semibold text-gray-600 no-underline hover:bg-gray-200">{{ $loop->iteration }}</a>
                        @endforeach
                    </nav>
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-gold-500 text-[15px] font-bold text-primary-950 shadow-gold hover:bg-gold-400"><i class="fas fa-check" aria-hidden="true"></i> Mark my answers</button>
                    <p class="text-center text-xs text-gray-400">Unanswered questions count as wrong. Theory questions are not scored.</p>
                </div>
            </aside>
        </form>
        <p class="mt-6 text-xs text-gray-400">Demo questions written by Otisam in the style of the {{ $exam }}. They are not WAEC past questions.</p>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('practice-form');
    if (!form) { return; }
    var total = +form.dataset.total, submitted = false;

    function answeredIn(fs) {
        if (fs.querySelector('input:checked')) { return true; }
        var t = fs.querySelector('[data-text]');
        return !!(t && t.value.trim() !== '');
    }
    function unanswered() {
        var n = 0;
        form.querySelectorAll('[data-question]').forEach(function (fs) { if (!answeredIn(fs)) { n++; } });
        return n;
    }

    function refresh() {
        var answered = 0;
        form.querySelectorAll('[data-question]').forEach(function (fs) {
            var done = answeredIn(fs);
            if (done) { answered++; }
            var nav = form.querySelector('[data-nav="' + fs.dataset.question + '"]');
            nav.classList.toggle('bg-primary-600', done);
            nav.classList.toggle('text-white', done);
            nav.classList.toggle('bg-gray-100', !done);
        });
        form.querySelector('[data-answered]').textContent = answered;
        form.querySelector('[data-progress]').style.width = (answered / total * 100) + '%';
    }
    form.addEventListener('change', refresh);
    form.addEventListener('input', refresh);

    form.addEventListener('submit', function (e) {
        if (submitted) { return; }
        var left = unanswered();
        if (left > 0 && !form.dataset.timeUp && !confirm(left + ' question(s) not answered. Mark anyway?')) { e.preventDefault(); return; }
        submitted = true;
        window.removeEventListener('beforeunload', guard);
    });

    // Leaving the page loses the set; warn once something has been answered.
    function guard(e) { if (unanswered() < total) { e.preventDefault(); e.returnValue = ''; } }
    window.addEventListener('beforeunload', guard);

    var seconds = +form.dataset.seconds || 0, timer = form.querySelector('[data-timer]');
    if (seconds && timer) {
        var end = Date.now() + seconds * 1000;
        var tick = setInterval(function () {
            var left = Math.max(0, Math.round((end - Date.now()) / 1000));
            timer.textContent = String(Math.floor(left / 60)).padStart(2, '0') + ':' + String(left % 60).padStart(2, '0');
            if (left <= 60) { timer.parentElement.classList.replace('bg-primary-700', 'bg-red-600'); }
            if (left === 0) { clearInterval(tick); form.dataset.timeUp = '1'; form.requestSubmit(); }
        }, 1000);
    }
})();
</script>
@endpush
