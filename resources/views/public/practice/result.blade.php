@extends('layouts.app')

@section('title', $subject->name.' practice: your score')

@section('content')
@php
    $total = $results->count();
    $percent = $total ? round($score / $total * 100) : 0;
    $wrong = $total - $score;
    $time = sprintf('%d min %02d s', intdiv($seconds, 60), $seconds % 60);
@endphp
<div class="mx-auto max-w-4xl px-4 py-8 sm:py-12">
    <a href="{{ route('practice.index') }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i> Practice home</a>

    <div class="relative mt-4 overflow-hidden rounded-2xl bg-primary-700 bg-crosshatch p-6 text-white shadow-lift">
        <div class="flex flex-wrap items-center gap-6">
            {{-- Score ring --}}
            <div class="relative h-28 w-28 shrink-0" role="img" aria-label="Score {{ $percent }} percent">
                <svg viewBox="0 0 36 36" class="h-full w-full -rotate-90"><circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="3.5"/><circle cx="18" cy="18" r="15.9" fill="none" stroke="#ffaf1b" stroke-width="3.5" stroke-linecap="round" stroke-dasharray="{{ $percent }} 100"/></svg>
                <span class="absolute inset-0 flex items-center justify-center font-display text-2xl font-extrabold">{{ $percent }}%</span>
            </div>
            <div class="min-w-0">
                <p class="eyebrow">{{ $subject->name }} · {{ $subject->examStyle() }} style</p>
                <p class="mt-1 font-display text-4xl font-extrabold" data-score>{{ $score }} / {{ $total }}</p>
                <p class="mt-1 text-white/75">{{ $percent >= 80 ? 'Excellent work!' : ($percent >= 50 ? 'Good effort. Review the ones you missed below.' : 'Keep practising. The explanations below will help.') }}</p>
            </div>
        </div>
        <dl class="mt-5 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
            <div class="rounded-xl bg-white/10 p-3"><dt class="text-white/60">Correct</dt><dd class="text-lg font-bold">{{ $score }}</dd></div>
            <div class="rounded-xl bg-white/10 p-3"><dt class="text-white/60">Wrong / skipped</dt><dd class="text-lg font-bold">{{ $wrong }}</dd></div>
            <div class="rounded-xl bg-white/10 p-3"><dt class="text-white/60">Time taken</dt><dd class="text-lg font-bold">{{ $time }}</dd></div>
            @foreach ($byDifficulty as $label => $row)
                @if ($loop->first)<div class="rounded-xl bg-white/10 p-3"><dt class="text-white/60">By difficulty</dt><dd class="text-sm font-semibold">@endif
                {{ ucfirst($label) }} {{ $row['right'] }}/{{ $row['total'] }}@unless ($loop->last) · @endunless
                @if ($loop->last)</dd></div>@endif
            @endforeach
        </dl>
        @if ($late)<p class="mt-3 rounded-xl bg-red-500/20 px-3 py-2 text-sm"><i class="fas fa-clock mr-1" aria-hidden="true"></i> Submitted after the time limit.</p>@endif
        <div class="mt-5 flex flex-wrap gap-3">
            <a href="{{ route('practice.start', array_filter($retry)) }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-gold-500 px-5 text-sm font-bold text-primary-950 no-underline shadow-gold hover:bg-gold-400 hover:text-primary-950"><i class="fas fa-rotate" aria-hidden="true"></i> New set, same filters</a>
            <a href="{{ route('practice.index', array_filter($retry)) }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-white/10 px-5 text-sm font-bold text-white no-underline ring-1 ring-white/30 hover:bg-white/20 hover:text-white"><i class="fas fa-filter" aria-hidden="true"></i> Change filters</a>
            <a href="{{ route('library.index', ['subject' => $subject->slug]) }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-white/10 px-5 text-sm font-bold text-white no-underline ring-1 ring-white/30 hover:bg-white/20 hover:text-white"><i class="fas fa-book-open" aria-hidden="true"></i> Past questions</a>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-between gap-3">
        <h2 class="text-xl font-extrabold text-primary-700">Review</h2>
        @if ($wrong > 0 && $score > 0)
            <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-gray-700"><input type="checkbox" class="accent-[#28409e]" data-only-wrong> Show only the ones I missed</label>
        @endif
    </div>

    <ol class="mt-3 space-y-4">
        @foreach ($results as $row)
            @php $revision = $row['question']->currentRevision; @endphp
            <li class="rounded-2xl bg-white p-5 shadow-card ring-1 {{ $row['right'] ? 'ring-green-200' : 'ring-red-200' }}" @if ($row['right']) data-right @endif>
                <p class="font-semibold text-gray-800">
                    <i class="fas {{ $row['right'] ? 'fa-circle-check text-green-600' : 'fa-circle-xmark text-red-600' }} mr-1" aria-hidden="true"></i>
                    <span class="sr-only">{{ $row['right'] ? 'Correct.' : 'Wrong.' }}</span>
                    {{ $loop->iteration }}. {{ $revision->stem }}
                </p>
                <ul class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                    @foreach ($revision->payload['options'] ?? [] as $option)
                        @php
                            $isCorrect = $option['id'] === $row['correct'];
                            $isGiven = $option['id'] === $row['given'];
                        @endphp
                        <li class="rounded-xl border-[1.5px] px-3 py-2 {{ $isCorrect ? 'border-green-500 bg-green-50 text-green-900' : ($isGiven ? 'border-red-400 bg-red-50 text-red-900' : 'border-[#ece9f3] text-gray-600') }}">
                            <strong>{{ $option['id'] }}.</strong> {{ $option['text'] }}
                            @if ($isCorrect)<span class="ml-1 text-xs font-bold">(correct)</span>@elseif ($isGiven)<span class="ml-1 text-xs font-bold">(your answer)</span>@endif
                        </li>
                    @endforeach
                </ul>
                @if ($row['given'] === null)<p class="mt-2 text-sm text-gray-500">You did not answer this one.</p>@endif
                @if ($revision->explanation)
                    <p class="mt-3 rounded-xl bg-primary-50 px-3 py-2 text-sm text-primary-800"><strong>Explanation:</strong> {{ $revision->explanation }}</p>
                @endif
            </li>
        @endforeach
    </ol>
</div>
@endsection

@push('scripts')
<script>
    document.querySelector('[data-only-wrong]')?.addEventListener('change', function (e) {
        document.querySelectorAll('li[data-right]').forEach(function (li) { li.hidden = e.target.checked; });
    });
</script>
@endpush
