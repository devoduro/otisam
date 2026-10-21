@extends('layouts.app')

@section('title', $subject->name.' practice papers')
@section('description', $subject->examStyle().'-style '.$subject->name.' objective papers with study mode, timed exams and solutions.')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 md:px-6 sm:py-12">
    <a href="{{ route('papers.index') }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>All subjects</a>
    <p class="eyebrow mt-4">{{ $subject->educationLevel->name }} · {{ $subject->examStyle() }} style</p>
    <h1 class="mt-1 text-3xl font-extrabold text-gray-800">{{ $subject->name }}</h1>
    <p class="mt-1 text-gray-500">{{ $papers->count() }} {{ \Illuminate\Support\Str::plural('paper', $papers->count()) }}. Each question appears in one paper only.</p>

    @if ($papers->isEmpty())
        <div class="mt-6 rounded-2xl bg-white p-8 text-center text-gray-500 shadow-card ring-1 ring-[#ece9f3]">No papers for this subject yet. <a href="{{ route('practice.start', ['subject' => $subject->slug]) }}">Practise random questions</a> instead.</div>
    @else
        @include('public.papers._list')
    @endif
</div>
@endsection
