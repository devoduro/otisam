@extends('layouts.app')

@section('title', $paper->title.' · Result')

@php $total = $results->count(); $percent = $total ? round($score / $total * 100) : 0; @endphp

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 md:px-6 sm:py-12">
    <a href="{{ route('papers.subject', $subject) }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>{{ $subject->name }} papers</a>

    <div class="mt-4 rounded-2xl bg-white p-6 text-center shadow-card ring-1 ring-[#ece9f3]">
        <p class="eyebrow">{{ $paper->title }} · Exam mode</p>
        <p class="mt-2 font-display text-5xl font-extrabold text-primary-700">{{ $score }} / {{ $total }}</p>
        <p class="mt-1 text-gray-500">{{ $percent }}% · {{ intdiv($seconds, 60) }} min {{ $seconds % 60 }} s</p>
        @if ($late)<p class="mt-2 text-sm text-amber-700">Submitted after the time limit.</p>@endif
        @if ($saved)
            <p class="mt-2 text-sm text-gray-500"><i class="fas fa-circle-check mr-1 text-green-600" aria-hidden="true"></i>Saved to <a href="{{ route('learn.progress') }}">My progress</a>. Your best score on each paper counts towards the <a href="{{ route('leaderboard', ['subject' => $subject->slug]) }}">leaderboard</a>.</p>
        @else
            <p class="mt-2 text-sm text-gray-500">@guest<a href="{{ route('register') }}">Create a free student account</a> to save your scores and join the leaderboard.@endguest</p>
        @endif
        <div class="mt-5 flex flex-wrap justify-center gap-2">
            <x-button :href="route('papers.take', [$subject, $paper->number, 'mode' => 'study'])" variant="outline" icon="fa-book-open">Go through it in study mode</x-button>
            <x-button :href="route('papers.solutions', [$subject, $paper->number])" icon="fa-key">Solutions</x-button>
        </div>
    </div>

    <h2 class="mt-8 text-lg font-bold text-gray-800">Your answers</h2>
    <ol class="mt-3 grid grid-cols-5 gap-2 sm:grid-cols-10">
        @foreach ($results as $r)
            <li class="flex h-10 flex-col items-center justify-center rounded-lg text-xs font-bold {{ $r['right'] ? 'bg-green-100 text-green-800' : ($r['given'] ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-500') }}"
                title="Question {{ $loop->iteration }}: {{ $r['right'] ? 'right' : ($r['given'] ? 'wrong' : 'not answered') }}">
                <span>{{ $loop->iteration }}</span><span class="font-normal">{{ $r['given'] ?? '–' }}{{ $r['right'] ? '' : '→'.$r['correct'] }}</span>
            </li>
        @endforeach
    </ol>
    <p class="mt-3 text-xs text-gray-500">Green: right. Red: your answer → the correct one. Grey: not answered.</p>
</div>
@endsection
