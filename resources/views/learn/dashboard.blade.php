@extends('layouts.portal')

@section('title', 'Dashboard')
@section('hide_heading', '1')

@section('content')
@php
    $first = \Illuminate\Support\Str::before($user->name, ' ') ?: $user->name;
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp
<div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    <div class="md:col-span-3">
        <x-dashboard-hero :title="$greeting.', '.$first" :eyebrow="now()->format('l, j F Y')" subtitle="Pick a subject, read worked solutions and revise topic by topic.">
            <x-slot:chips>
                @if ($profile?->classLevel)<span><i class="fas fa-graduation-cap"></i> {{ $profile->classLevel->name }}</span>@endif
                @if ($profile?->programme)<span><i class="fas fa-layer-group"></i> {{ $profile->programme->name }}</span>@endif
                @if ($subscription)
                    <span><i class="fas fa-crown text-yellow-300"></i> Premium until {{ $subscription->accessEndsAt()->format('j M Y') }}</span>
                @else
                    <span><i class="fas fa-user"></i> Free account</span>
                @endif
            </x-slot:chips>
            <a class="primary" href="{{ route('library.index') }}"><i class="fas fa-book-open"></i> Open the library</a>
            @unless ($subscription)
                <a href="{{ route('plans.index') }}"><i class="fas fa-crown"></i> Go premium</a>
            @endunless
        </x-dashboard-hero>
    </div>

    <!-- Quick find (jhs_portal style) -->
    <div class="md:col-span-3">
        <form method="GET" action="{{ route('library.index') }}" class="flex flex-col gap-3 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3] sm:flex-row sm:items-center" role="search">
            <label for="quick-find" class="text-sm font-semibold text-gray-700 sm:w-48"><i class="fas fa-magnifying-glass mr-1 text-gold-500" aria-hidden="true"></i> Find past questions</label>
            <input id="quick-find" type="search" name="q" placeholder="e.g. quadratic equations, 2019, osmosis" class="flex-1 rounded-xl border-[1.5px] border-[#e2e0ea] px-3 py-2.5 text-sm focus:border-gold-500 focus:outline-none focus:ring-2 focus:ring-gold-500/30">
            <button type="submit" class="rounded-md bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Search</button>
        </form>
    </div>

    <a href="{{ route('learn.classes.index') }}" class="md:col-span-3 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white p-4 no-underline shadow-card ring-1 {{ $classwork['todo'] ? 'ring-gold-300' : 'ring-[#ece9f3]' }}" data-classwork>
        <span class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $classwork['todo'] ? 'bg-gold-100 text-gold-700' : 'bg-primary-50 text-primary-600' }}"><i class="fas fa-people-group" aria-hidden="true"></i></span>
            <span>
                <span class="block font-semibold text-gray-800">
                    @if ($classwork['classes'] === 0) Join your teacher's class
                    @elseif ($classwork['todo']) {{ $classwork['todo'] }} {{ \Illuminate\Support\Str::plural('assignment', $classwork['todo']) }} or quizzes to do
                    @else All class work handed in
                    @endif
                </span>
                <span class="text-sm text-gray-500">{{ $classwork['classes'] === 0 ? 'Ask your teacher for the class code.' : $classwork['classes'].' '.\Illuminate\Support\Str::plural('class', $classwork['classes']) }}</span>
            </span>
        </span>
        <span class="text-sm font-bold text-gold-700">My classes <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></span>
    </a>

    <div class="md:col-span-3 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-stat-tile color="blue" icon="fa-book-open" title="For your subjects" sub="Items in the library" :value="number_format($stats['resources'])" :href="route('library.index')" />
        <x-stat-tile color="green" icon="fa-gift" title="Free to read" sub="No subscription needed" :value="number_format($stats['free'])" :href="route('library.index', ['access' => 'free'])" />
        <x-stat-tile color="orange" icon="fa-file-lines" title="Past papers" sub="With exam year" :value="number_format($stats['papers'])" :href="route('library.index')" />
        <x-stat-tile :color="$subscription ? 'purple' : 'slate'" icon="fa-crown" title="Premium" :sub="$subscription ? 'Active' : 'Not active'"
            :value="$subscription ? $subscription->accessEndsAt()->format('j M') : 'Free'" :badge="$subscription ? 'until' : 'Upgrade'" :href="route('account.subscription')" />
    </div>

    <div class="md:col-span-2 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">New in the library</h2>
            <a href="{{ route('library.index') }}" class="text-xs font-medium">See all <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i></a>
        </div>
        @if ($latest->isEmpty())
            <p class="flex h-40 items-center justify-center text-center text-sm text-gray-400">Material for your subjects is being added. Check back soon.</p>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($latest as $item)
                    <li>
                        <a href="{{ route('library.show', $item) }}" class="flex items-center justify-between gap-3 py-3 no-underline">
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-medium text-gray-800">{{ $item->title }}</span>
                                <span class="block truncate text-xs text-gray-500">{{ collect([$item->subject?->name, $item->examLabel()])->filter()->join(' · ') }}</span>
                            </span>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $item->isFree() ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-800' }}">{{ $item->isFree() ? 'Free' : 'Premium' }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]">
        <h2 class="mb-3 text-sm font-semibold text-gray-700">My subjects</h2>
        @forelse ($subjects as $subject)
            <a href="{{ route('subjects.show', $subject) }}" class="flex items-center justify-between rounded-md px-2 py-2 text-sm text-gray-700 no-underline hover:bg-primary-50 hover:text-primary-600">
                <span><i class="fas fa-book mr-2 text-gray-400" aria-hidden="true"></i>{{ $subject->name }}</span>
                <i class="fas fa-chevron-right text-xs text-gray-300" aria-hidden="true"></i>
            </a>
        @empty
            <p class="text-sm text-gray-400">Subjects will appear here once your programme is set up.</p>
        @endforelse
    </div>
</div>
@endsection
