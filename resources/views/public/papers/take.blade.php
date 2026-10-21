@extends('layouts.app')

{{-- One question at a time. Used by practice papers (study/exam) and by quick practice (study only).
     Expects: $mode, $questions, $title, $eyebrow, $backUrl, $backLabel, $checkUrl, $examStyle;
     exam mode also $markUrl and $seconds; optional $examUrl (switch to exam) and $againUrl (new round). --}}
@section('title', $title.($mode === 'exam' ? ' · Exam' : ' · Study'))

@php $exam = $mode === 'exam'; $total = $questions->count(); @endphp

@section('content')
<div class="mx-auto max-w-6xl px-4 py-6 sm:py-10">
    <a href="{{ $backUrl }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>{{ $backLabel }}</a>
    <p class="eyebrow mt-4">{{ $eyebrow }}</p>
    <h1 class="mt-1 text-2xl font-extrabold text-primary-700 sm:text-3xl">{{ $title }}</h1>

    @if ($questions->isEmpty())
        <div class="mt-6 rounded-2xl bg-white p-8 text-center text-gray-500 shadow-card ring-1 ring-[#ece9f3]">No questions here yet. <a href="{{ $backUrl }}">Go back</a>.</div>
    @else

    <form method="POST" action="{{ $markUrl ?? '#' }}" id="paper-form" class="mt-6 grid gap-6 lg:grid-cols-[1fr_17rem]"
          data-total="{{ $total }}" data-mode="{{ $mode }}" data-check="{{ $checkUrl }}" @if ($exam) data-seconds="{{ $seconds }}" @endif>
        @csrf

        <div class="order-2 lg:order-1">
            @foreach ($questions as $question)
                @php $revision = $question->currentRevision; @endphp
                <fieldset id="q{{ $loop->iteration }}" data-question="{{ $loop->iteration }}" data-ulid="{{ $question->ulid }}" class="mb-4 scroll-mt-40 rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
                    <legend class="sr-only">Question {{ $loop->iteration }} of {{ $total }}</legend>
                    <p class="text-xs font-semibold text-gray-400">Question {{ $loop->iteration }} of {{ $total }}</p>
                    <p class="mt-1 whitespace-pre-line font-semibold text-gray-800">{{ $revision->stem }}</p>
                    <div class="mt-3 grid gap-2">
                        @foreach ($revision->payload['options'] ?? [] as $option)
                            <label data-option="{{ $option['id'] }}" class="flex cursor-pointer items-start gap-3 rounded-xl border-[1.5px] border-[#e2e0ea] px-3 py-2.5 text-sm hover:border-gold-500 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                                <input type="radio" name="answers[{{ $question->ulid }}]" value="{{ $option['id'] }}" class="mt-0.5 accent-[#28409e]">
                                <span><strong class="mr-1">{{ $option['id'] }}.</strong> {{ $option['text'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @unless ($exam)
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <button type="button" data-check-button class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-bold text-white hover:bg-primary-700 disabled:opacity-50" disabled>Check answer</button>
                        </div>
                        <div data-feedback class="mt-3 hidden rounded-xl p-3 text-sm" role="status" aria-live="polite"></div>
                    @endunless
                    <div data-pager class="mt-4 hidden justify-between gap-2">
                        <button type="button" data-prev class="rounded-xl px-3 py-2 text-sm font-semibold text-primary-700 ring-1 ring-[#e2e0ea] hover:ring-gold-500 disabled:opacity-40" @disabled($loop->first)><i class="fas fa-chevron-left mr-1" aria-hidden="true"></i>Previous</button>
                        <button type="button" data-next class="rounded-xl px-3 py-2 text-sm font-semibold text-primary-700 ring-1 ring-[#e2e0ea] hover:ring-gold-500 disabled:opacity-40" @disabled($loop->last)>Next<i class="fas fa-chevron-right ml-1" aria-hidden="true"></i></button>
                    </div>
                </fieldset>
            @endforeach
        </div>

        <aside class="order-1 lg:order-2">
            <div class="sticky top-20 space-y-4 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]">
                @if ($exam)
                    <div class="rounded-xl bg-primary-700 p-3 text-center text-white" role="timer" aria-live="off">
                        <p class="text-xs uppercase tracking-wider text-white/70">Time left</p>
                        <p class="font-display text-3xl font-extrabold tabular-nums" data-timer>{{ sprintf('%02d:00', intdiv($seconds, 60)) }}</p>
                    </div>
                @else
                    <div class="rounded-xl bg-primary-50 p-3 text-center">
                        <p class="text-xs uppercase tracking-wider text-primary-700">Score so far</p>
                        <p class="font-display text-3xl font-extrabold tabular-nums text-primary-700"><span data-right>0</span> / <span data-checked>0</span></p>
                    </div>
                @endif
                <div>
                    <div class="flex justify-between text-sm"><span class="font-semibold text-gray-700">Answered</span><span class="tabular-nums text-gray-500"><span data-answered>0</span> / {{ $total }}</span></div>
                    <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100"><div class="h-full w-0 rounded-full bg-gold-500 transition-all" data-progress></div></div>
                </div>
                <nav aria-label="Jump to question" class="grid max-h-64 grid-cols-5 gap-1.5 overflow-y-auto">
                    @foreach ($questions as $question)
                        <a href="#q{{ $loop->iteration }}" data-nav="{{ $loop->iteration }}" class="flex h-9 items-center justify-center rounded-lg bg-gray-100 text-sm font-semibold text-gray-600 no-underline hover:bg-gray-200">{{ $loop->iteration }}</a>
                    @endforeach
                </nav>
                @if ($exam)
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-gold-500 text-[15px] font-bold text-primary-950 shadow-gold hover:bg-gold-400"><i class="fas fa-check" aria-hidden="true"></i> Submit paper</button>
                    <p class="text-center text-xs text-gray-400">Unanswered questions count as wrong.</p>
                @else
                    @isset($examUrl)<a href="{{ $examUrl }}" class="block text-center text-sm font-semibold no-underline">Ready? Sit it as a timed exam</a>@endisset
                    @isset($againUrl)<a href="{{ $againUrl }}" class="block text-center text-sm font-semibold no-underline"><i class="fas fa-rotate mr-1" aria-hidden="true"></i>New set of questions</a>@endisset
                @endif
            </div>
        </aside>
    </form>
    @endif
    <p class="mt-6 text-xs text-gray-400">Questions written by {{ config('app.name') }} in the style of the {{ $examStyle }}. They are not WAEC past questions.</p>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('paper-form');
    if (!form) { return; }
    var total = +form.dataset.total, exam = form.dataset.mode === 'exam', submitted = false;
    var sets = Array.prototype.slice.call(form.querySelectorAll('[data-question]'));
    var current = 0, right = 0, checked = 0;

    // One question at a time, with previous/next and the navigator; without JavaScript all show.
    function show(i) {
        current = Math.max(0, Math.min(total - 1, i));
        sets.forEach(function (fs, n) { fs.classList.toggle('hidden', n !== current); });
        form.querySelectorAll('[data-nav]').forEach(function (a) { a.classList.toggle('ring-2', +a.dataset.nav === current + 1); a.classList.toggle('ring-primary-600', +a.dataset.nav === current + 1); });
    }
    sets.forEach(function (fs, n) {
        fs.querySelector('[data-pager]').classList.replace('hidden', 'flex');
        fs.querySelector('[data-prev]').addEventListener('click', function () { show(n - 1); });
        fs.querySelector('[data-next]').addEventListener('click', function () { show(n + 1); });
    });
    form.querySelectorAll('[data-nav]').forEach(function (a) {
        a.addEventListener('click', function (e) { e.preventDefault(); show(+a.dataset.nav - 1); });
    });

    function refresh() {
        var answered = 0;
        sets.forEach(function (fs) {
            var done = !!fs.querySelector('input:checked');
            if (done) { answered++; }
            var nav = form.querySelector('[data-nav="' + fs.dataset.question + '"]');
            if (!nav.dataset.result) {
                nav.classList.toggle('bg-primary-600', done);
                nav.classList.toggle('text-white', done);
                nav.classList.toggle('bg-gray-100', !done);
            }
            var btn = fs.querySelector('[data-check-button]');
            if (btn && !fs.dataset.done) { btn.disabled = !done; }
        });
        form.querySelector('[data-answered]').textContent = answered;
        form.querySelector('[data-progress]').style.width = (answered / total * 100) + '%';
    }
    form.addEventListener('change', refresh);

    // Study mode: ask the server whether the answer is right and show the explanation.
    sets.forEach(function (fs) {
        var btn = fs.querySelector('[data-check-button]');
        if (!btn) { return; }
        btn.addEventListener('click', function () {
            var choice = fs.querySelector('input:checked');
            if (!choice) { return; }
            btn.disabled = true;
            var body = new FormData();
            body.append('_token', form.querySelector('[name=_token]').value);
            body.append('question', fs.dataset.ulid);
            body.append('answer', choice.value);
            fetch(form.dataset.check, { method: 'POST', body: body, headers: { 'Accept': 'application/json' } })
                .then(function (r) { if (!r.ok) { throw r; } return r.json(); })
                .then(function (res) {
                    fs.dataset.done = '1';
                    fs.querySelectorAll('input').forEach(function (i) { i.disabled = true; });
                    // Drop the "selected" look so the right/wrong colours show.
                    fs.querySelectorAll('[data-option]').forEach(function (l) { l.classList.remove('has-[:checked]:border-primary-600', 'has-[:checked]:bg-primary-50', 'hover:border-gold-500', 'cursor-pointer'); });
                    var good = fs.querySelector('[data-option="' + res.correct + '"]');
                    if (good) { good.classList.add('border-green-600', 'bg-green-50'); }
                    if (!res.right) { choice.closest('label').classList.add('border-red-500', 'bg-red-50'); }
                    var box = fs.querySelector('[data-feedback]');
                    box.classList.remove('hidden');
                    box.classList.add(res.right ? 'bg-green-50' : 'bg-red-50', res.right ? 'text-green-900' : 'text-red-900');
                    box.textContent = (res.right ? 'Correct.' : 'Not quite. The answer is ' + res.correct + '.') + (res.explanation ? ' ' + res.explanation : '');
                    checked++; if (res.right) { right++; }
                    form.querySelector('[data-right]').textContent = right;
                    form.querySelector('[data-checked]').textContent = checked;
                    var nav = form.querySelector('[data-nav="' + fs.dataset.question + '"]');
                    nav.dataset.result = '1';
                    nav.classList.remove('bg-gray-100', 'bg-primary-600');
                    nav.classList.add(res.right ? 'bg-green-600' : 'bg-red-500', 'text-white');
                })
                .catch(function () { btn.disabled = false; alert('Could not check that answer. Please try again.'); });
        });
    });

    if (exam) {
        form.addEventListener('submit', function (e) {
            if (submitted) { return; }
            var left = total - form.querySelectorAll('input:checked').length;
            if (left > 0 && !form.dataset.timeUp && !confirm(left + ' question(s) not answered. Submit anyway?')) { e.preventDefault(); return; }
            submitted = true;
            window.removeEventListener('beforeunload', guard);
        });
        // Leaving the page loses the attempt; warn once something has been answered.
        var guard = function (e) { if (form.querySelector('input:checked')) { e.preventDefault(); e.returnValue = ''; } };
        window.addEventListener('beforeunload', guard);

        var seconds = +form.dataset.seconds || 0, timer = form.querySelector('[data-timer]');
        var end = Date.now() + seconds * 1000;
        var tick = setInterval(function () {
            var left = Math.max(0, Math.round((end - Date.now()) / 1000));
            timer.textContent = String(Math.floor(left / 60)).padStart(2, '0') + ':' + String(left % 60).padStart(2, '0');
            if (left <= 60) { timer.parentElement.classList.replace('bg-primary-700', 'bg-red-600'); }
            if (left === 0) { clearInterval(tick); form.dataset.timeUp = '1'; form.requestSubmit(); }
        }, 1000);
    } else {
        form.addEventListener('submit', function (e) { e.preventDefault(); });
    }

    show(0);
})();
</script>
@endpush
