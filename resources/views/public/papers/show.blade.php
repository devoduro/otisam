@extends('layouts.app')

@section('title', $paper->title)
@section('description', $paper->exam_style.'-style '.$subject->name.' objective paper: '.$paper->questions_count.' questions, '.$paper->duration_minutes.' minutes.')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 md:px-6 sm:py-12">
    <a href="{{ route('papers.subject', $subject) }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>{{ $subject->name }} papers</a>

    <div class="mt-4 rounded-2xl bg-white p-6 shadow-card ring-1 ring-[#ece9f3]">
        <span class="rounded-full bg-gold-100 px-2 py-0.5 text-[11px] font-bold text-gold-800">{{ $paper->exam_style }} style</span>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">{{ $paper->title }}</h1>
        <p class="mt-1 text-sm text-gray-500">Objective (multiple choice) · {{ $paper->questions_count }} questions · {{ $paper->duration_minutes }} minutes</p>

        <h2 class="mt-6 text-sm font-bold text-primary-800">Choose how to work</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <a href="{{ route('papers.take', [$subject, $paper->number, 'mode' => 'study']) }}" class="block rounded-2xl p-4 no-underline ring-1 ring-[#e2e0ea] hover:ring-2 hover:ring-primary-600">
                <span class="flex items-center gap-2 font-bold text-gray-800"><i class="fas fa-book-open text-primary-600" aria-hidden="true"></i>Study mode</span>
                <span class="mt-1 block text-sm text-gray-500">No timer. Check each answer as you go and read why it is right.</span>
            </a>
            <a href="{{ route('papers.take', [$subject, $paper->number, 'mode' => 'exam']) }}" class="block rounded-2xl p-4 no-underline ring-1 ring-[#e2e0ea] hover:ring-2 hover:ring-primary-600">
                <span class="flex items-center gap-2 font-bold text-gray-800"><i class="fas fa-stopwatch text-primary-600" aria-hidden="true"></i>Exam mode</span>
                <span class="mt-1 block text-sm text-gray-500">{{ $paper->duration_minutes }} minutes. Answers are marked when you submit or time runs out.</span>
            </a>
        </div>
        <p class="mt-4 text-sm"><a href="{{ route('papers.solutions', [$subject, $paper->number]) }}" class="font-semibold no-underline"><i class="fas fa-key mr-1" aria-hidden="true"></i>Solutions</a> @guest<span class="text-gray-500">(sign in free to view)</span>@endguest</p>
    </div>
    <p class="mt-4 text-xs text-gray-400">Questions written by {{ config('app.name') }} in the style of the {{ $paper->exam_style }}. They are not WAEC past questions.</p>
</div>
@endsection
