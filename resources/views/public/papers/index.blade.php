@extends('layouts.app')

@section('title', 'WASSCE & BECE practice papers')
@section('description', 'Full objective practice papers for SHS (WASSCE style) and JHS (BECE style) subjects. Study mode with explanations, or a timed exam.')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 md:px-6 sm:py-12">
    <a href="{{ route('practice.index') }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>Practice</a>
    <h1 class="mt-2 text-3xl font-extrabold text-gray-800">Practice papers</h1>
    <p class="mt-1 max-w-3xl text-gray-500">Full objective papers laid out like the WASSCE (SHS) and BECE (JHS). Study at your own pace with explanations, or sit a timed exam. Every question is written by {{ config('app.name') }}; these are not WAEC past papers.</p>

    <nav aria-label="Level" class="mt-5 flex flex-wrap gap-2 text-sm">
        @foreach (['' => 'All', 'shs' => 'SHS · WASSCE style', 'jhs' => 'JHS · BECE style'] as $value => $label)
            <a href="{{ route('papers.index', array_filter(['level' => $value])) }}" @if (($level ?? '') === $value) aria-current="page" @endif
               class="rounded-full px-4 py-1.5 font-semibold no-underline {{ ($level ?? '') === $value ? 'bg-primary-600 text-white' : 'bg-white text-primary-700 ring-1 ring-[#e2e0ea] hover:ring-gold-500' }}">{{ $label }}</a>
        @endforeach
    </nav>

    @forelse ($levels as $l)
        <h2 class="mt-8 text-lg font-bold text-gray-800">{{ $l->name }}</h2>
        <ul class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($l->subjects as $s)
                <li>
                    <a href="{{ route('papers.subject', $s) }}" class="flex items-center justify-between gap-3 rounded-2xl bg-white p-4 no-underline shadow-card ring-1 ring-[#ece9f3] transition hover:-translate-y-0.5 hover:shadow-md">
                        <span>
                            <span class="block font-semibold text-gray-800">{{ $s->name }}</span>
                            <span class="block text-xs text-gray-500">{{ $counts[$s->id] }} {{ \Illuminate\Support\Str::plural('paper', $counts[$s->id]) }} · {{ $s->examStyle() }} style</span>
                        </span>
                        <i class="fas fa-chevron-right text-gray-300" aria-hidden="true"></i>
                    </a>
                </li>
            @endforeach
        </ul>
    @empty
        <div class="mt-6 rounded-2xl bg-white p-8 text-center text-gray-500 shadow-card ring-1 ring-[#ece9f3]">Practice papers are being prepared. Check back soon.</div>
    @endforelse
</div>
@endsection
