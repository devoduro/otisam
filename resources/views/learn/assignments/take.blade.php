@extends('layouts.portal')

@section('title', $assignment->title)
@section('subtitle', 'Quiz · '.$assignment->classroom->name)

@section('content')
<form method="POST" action="{{ route('learn.assignments.submit-quiz', $assignment) }}" id="quiz-form" class="grid gap-6 lg:grid-cols-[1fr_16rem]"
      data-total="{{ $assignment->questions->count() }}" @if ($secondsLeft !== null) data-seconds="{{ $secondsLeft }}" @endif
      data-left-url="{{ route('learn.assignments.left', $assignment) }}" @if ($assignment->block_copy) data-block-copy style="-webkit-user-select:none;user-select:none" @endif>
    @csrf
    <div class="order-2 space-y-4 lg:order-1">
        <div class="rounded-xl bg-primary-50 px-4 py-3 text-sm text-primary-800" role="note">
            <p class="font-semibold"><i class="fas fa-triangle-exclamation mr-1" aria-hidden="true"></i> Stay on this page until you submit.</p>
            <p class="mt-1">Do not open other tabs or apps to search for answers or ask AI. Each time you leave this page it is recorded{{ $submission->tab_leaves ? ' (so far: '.$submission->tab_leaves.')' : '' }} and your teacher can see it.@if ($assignment->block_copy) Copying the questions is turned off for this quiz.@endif</p>
        </div>
        <div class="hidden rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white" role="alert" data-left-warning>
            You left the quiz page. This has been recorded for your teacher. Stay on this page until you submit.
        </div>
        @foreach ($assignment->questions as $question)
            @php $revision = $revisions[$question->pivot->question_revision_id] ?? null; @endphp
            <fieldset id="q{{ $loop->iteration }}" class="panel scroll-mt-24 p-5" data-question="{{ $loop->iteration }}">
                <legend class="sr-only">Question {{ $loop->iteration }}</legend>
                <p class="font-semibold text-gray-800"><span class="mr-2 inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-700">{{ $loop->iteration }}</span>{{ $revision?->stem }}</p>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    @foreach ($assignment->optionsFor($revision, $submission, $question->ulid) as $option)
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border-[1.5px] border-[#e2e0ea] px-3 py-2.5 text-sm hover:border-gold-500 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                            <input type="radio" name="answers[{{ $question->ulid }}]" value="{{ $option['id'] }}" class="mt-0.5 accent-[#28409e]">
                            <span><strong class="mr-1">{{ $option['label'] }}.</strong> {{ $option['text'] }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @endforeach
    </div>

    <aside class="order-1 lg:order-2">
        <div class="panel sticky top-4 space-y-4 p-4">
            @if ($secondsLeft !== null)
                <div class="rounded-xl bg-primary-700 p-3 text-center text-white" role="timer">
                    <p class="text-xs uppercase tracking-wider text-white/70">Time left</p>
                    <p class="font-display text-3xl font-extrabold tabular-nums" data-timer>{{ sprintf('%02d:%02d', intdiv($secondsLeft, 60), $secondsLeft % 60) }}</p>
                </div>
            @endif
            <div>
                <div class="flex justify-between text-sm"><span class="font-semibold text-gray-700">Answered</span><span class="tabular-nums text-gray-500"><span data-answered>0</span> / {{ $assignment->questions->count() }}</span></div>
                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100"><div class="h-full w-0 rounded-full bg-gold-500 transition-all" data-progress></div></div>
            </div>
            <nav aria-label="Jump to question" class="grid grid-cols-5 gap-1.5">
                @foreach ($assignment->questions as $question)
                    <a href="#q{{ $loop->iteration }}" data-nav="{{ $loop->iteration }}" class="flex h-9 items-center justify-center rounded-lg bg-gray-100 text-sm font-semibold text-gray-600 no-underline">{{ $loop->iteration }}</a>
                @endforeach
            </nav>
            <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-gold-500 font-bold text-primary-950 shadow-gold hover:bg-gold-400"><i class="fas fa-paper-plane" aria-hidden="true"></i> Submit quiz</button>
            <p class="text-center text-xs text-gray-400">You can submit only once.</p>
        </div>
    </aside>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('quiz-form'), total = +form.dataset.total, done = false, asking = false;
    function refresh() {
        var answered = 0;
        form.querySelectorAll('[data-question]').forEach(function (fs) {
            var ok = !!fs.querySelector('input:checked');
            if (ok) { answered++; }
            var nav = form.querySelector('[data-nav="' + fs.dataset.question + '"]');
            nav.classList.toggle('bg-primary-600', ok); nav.classList.toggle('text-white', ok); nav.classList.toggle('bg-gray-100', !ok);
        });
        form.querySelector('[data-answered]').textContent = answered;
        form.querySelector('[data-progress]').style.width = (answered / total * 100) + '%';
    }
    form.addEventListener('change', refresh);
    form.addEventListener('submit', function (e) {
        if (done) { return; }
        var left = total - form.querySelectorAll('input:checked').length;
        asking = true; // The confirm dialog can blur the window; that is not leaving the page.
        var sure = form.dataset.timeUp || confirm(left > 0 ? left + ' question(s) not answered. Submit anyway? You cannot change your answers afterwards.' : 'Submit your quiz? You cannot change your answers afterwards.');
        asking = false;
        if (!sure) { e.preventDefault(); return; }
        done = true;
    });
    // Leaving the page (another tab, window or app) is reported once per visit away.
    var away = false, warning = form.querySelector('[data-left-warning]'), token = form.querySelector('input[name="_token"]').value;
    function leave() {
        if (away || done || asking) { return; }
        away = true;
        var data = new FormData();
        data.append('_token', token);
        if (!(navigator.sendBeacon && navigator.sendBeacon(form.dataset.leftUrl, data))) {
            fetch(form.dataset.leftUrl, { method: 'POST', body: data, credentials: 'same-origin', keepalive: true }).catch(function () {});
        }
    }
    function back() {
        if (!away) { return; }
        away = false;
        warning.classList.remove('hidden');
        warning.scrollIntoView({ block: 'center' });
    }
    document.addEventListener('visibilitychange', function () { document.hidden ? leave() : back(); });
    window.addEventListener('blur', leave);
    window.addEventListener('focus', back);

    if (form.hasAttribute('data-block-copy')) {
        ['copy', 'cut', 'contextmenu', 'dragstart', 'selectstart'].forEach(function (type) {
            document.addEventListener(type, function (e) { e.preventDefault(); });
        });
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'x', 'p', 's', 'u'].indexOf(e.key.toLowerCase()) !== -1) { e.preventDefault(); }
        });
    }

    var seconds = +form.dataset.seconds || 0, timer = form.querySelector('[data-timer]');
    if (timer) {
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
