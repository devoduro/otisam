@extends('layouts.app')

@section('title', 'Top scorers'.($subject ? ' · '.$subject->name : ''))
@section('description', 'Students with the most points from practice papers and practice sets on '.config('app.name').'.')

@php
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038]';
    $medals = ['bg-yellow-400 text-yellow-950', 'bg-gray-300 text-gray-800', 'bg-amber-600 text-white'];
@endphp

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 md:px-6 sm:py-12">
    <h1 class="text-3xl font-extrabold text-gray-800"><i class="fas fa-trophy mr-2 text-gold-500" aria-hidden="true"></i>Top scorers</h1>
    <p class="mt-1 text-gray-500">Points: your best score on each practice paper (exam mode), plus correct answers in practice sets. Students only.</p>

    <form method="GET" action="{{ route('leaderboard') }}" data-catalogue-filters class="mt-5 grid gap-3 sm:grid-cols-[1fr_12rem]">
        <div>
            <label for="lb-subject" class="mb-1 block text-[13px] font-bold text-primary-800">Subject</label>
            <select id="lb-subject" name="subject" class="{{ $control }}">
                <option value="">All subjects</option>
                @foreach ($levels as $l)
                    @continue($l->subjects->isEmpty())
                    <optgroup label="{{ $l->name }}">
                        @foreach ($l->subjects as $s)<option value="{{ $s->slug }}" @selected($subject?->id === $s->id)>{{ $s->name }}</option>@endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div>
            <label for="lb-period" class="mb-1 block text-[13px] font-bold text-primary-800">Period</label>
            <select id="lb-period" name="period" class="{{ $control }}">
                <option value="month" @selected($period === 'month')>This month</option>
                <option value="all" @selected($period === 'all')>All time</option>
            </select>
        </div>
        <noscript><x-button>Show</x-button></noscript>
    </form>
    @include('public.partials.auto-filter-script')

    @if ($rows->isEmpty())
        <div class="mt-6 rounded-2xl bg-white p-8 text-center text-gray-500 shadow-card ring-1 ring-[#ece9f3]">
            No scores yet{{ $period === 'month' ? ' this month' : '' }}. <a href="{{ route('papers.index') }}">Sit a practice paper</a> to be first.
        </div>
    @else
        <ol class="mt-6 divide-y divide-gray-100 overflow-hidden rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3]">
            @foreach ($rows as $i => $row)
                <li class="flex items-center gap-4 px-4 py-3 {{ $mine === $i ? 'bg-gold-50' : '' }}">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-extrabold {{ $medals[$i] ?? 'bg-gray-100 text-gray-600' }}">{{ $i + 1 }}</span>
                    <span class="flex-1">
                        <span class="block font-semibold text-gray-800">{{ $row->name }}@if ($mine === $i) <span class="text-xs font-bold text-gold-700">(you)</span>@endif</span>
                        <span class="text-xs text-gray-500">{{ $row->attempts }} {{ \Illuminate\Support\Str::plural('attempt', $row->attempts) }}</span>
                    </span>
                    <span class="font-display text-xl font-extrabold tabular-nums text-primary-700">{{ number_format($row->points) }} <span class="text-xs font-semibold text-gray-400">pts</span></span>
                </li>
            @endforeach
        </ol>
    @endif

    @guest
        <p class="mt-6 text-sm text-gray-500"><a href="{{ route('register') }}">Create a free student account</a> to save your scores and appear here.</p>
    @endguest
</div>
@endsection
