@extends('layouts.app')

@section('title', 'Study by class')
@section('description', 'Choose your level and class to find everything for each subject: topics, past questions, practice papers, videos and lesson plans.')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 md:px-6 sm:py-12">
    <h1 class="text-3xl font-extrabold text-gray-800">Study by class</h1>
    <p class="mt-1 text-gray-500">Pick your level and class. Each subject has one page with its topics, past questions, practice papers, videos and lesson plans.</p>

    <div class="mt-6 grid gap-5 sm:grid-cols-2">
        @foreach ($levels as $l)
            <section class="relative overflow-hidden rounded-2xl p-6 text-white shadow-card {{ $l->code === 'JHS' ? 'bg-sky-700' : 'bg-primary-900' }}" style="background-image: linear-gradient(90deg, {{ $l->code === 'JHS' ? 'rgba(7,70,130,.92), rgba(7,70,130,.35)' : 'rgba(13,18,64,.94), rgba(13,18,64,.35)' }}), url('{{ asset('images/study/'.strtolower($l->code).'.svg') }}'); background-size: cover; background-position: right center;">
                <p class="eyebrow !text-gold-300">{{ $l->code === 'JHS' ? 'BECE' : 'WASSCE' }}</p>
                <h2 class="mt-1 text-2xl font-extrabold">{{ $l->name }}</h2>
                <a href="{{ route('study.level', strtolower($l->code)) }}" class="mt-1 inline-block text-sm font-semibold text-white/80 no-underline hover:text-white">All subjects <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($l->classLevels as $y)
                        <a href="{{ route('study.level', [strtolower($l->code), 'class' => $y->id]) }}" class="rounded-xl bg-white px-4 py-2 font-bold text-primary-700 no-underline hover:bg-gold-50 hover:text-gold-700">{{ $y->name }}</a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    @if ($top->isNotEmpty())
        <section class="mt-6 rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-800"><i class="fas fa-trophy mr-1.5 text-gold-500" aria-hidden="true"></i>Top scorers this month</h2>
                <a href="{{ route('leaderboard') }}" class="text-sm font-semibold no-underline">Full table</a>
            </div>
            <ol class="mt-3 grid gap-2 sm:grid-cols-3">
                @foreach ($top as $i => $row)
                    <li class="flex items-center gap-3 rounded-xl bg-gray-50 px-3 py-2"><span class="font-display text-lg font-extrabold text-gold-600">#{{ $i + 1 }}</span><span class="flex-1 font-semibold text-gray-800">{{ $row->name }}</span><span class="text-sm font-bold text-primary-700">{{ number_format($row->points) }} pts</span></li>
                @endforeach
            </ol>
        </section>
    @endif
</div>
@endsection
