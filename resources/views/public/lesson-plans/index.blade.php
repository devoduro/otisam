@extends('layouts.app')

@section('title', 'Lesson plans')
@section('description', 'Weekly lesson plans for JHS and SHS teachers, organised by year, subject, strand and week. Free to read, print and edit.')

@section('content')
@php
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038] focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30';
    $canWrite = auth()->user()?->isTeacher();
@endphp
<div class="mx-auto max-w-7xl px-4 py-8 md:px-6 sm:py-12">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800">Lesson plans</h1>
            <p class="mt-1 text-gray-500">Weekly plans written by teachers: starter, main activities, reflection and assessment. Print one or save it to edit.</p>
            <p class="mt-1 text-sm"><a href="{{ route('subject-apps.index') }}" class="font-semibold no-underline"><i class="fas fa-robot mr-1" aria-hidden="true"></i>AI planning apps for your subject</a></p>
        </div>
        @if ($canWrite)
            <x-button :href="route('teach.lesson-plans.create')" icon="fa-plus">Write a lesson plan</x-button>
        @else
            <a href="{{ route('teachers.register') }}" class="text-sm font-semibold no-underline"><i class="fas fa-chalkboard-user mr-1" aria-hidden="true"></i>Teachers: share your plans</a>
        @endif
    </div>

    @include('public.partials.catalogue-filters', [
        'action' => route('lesson-plans.index'),
        'searchPlaceholder' => 'e.g. fractions, photosynthesis, essay writing',
        'sorts' => ['week' => 'By week', 'newest' => 'Newest first', 'popular' => 'Most viewed'],
        'extra' => new \Illuminate\Support\HtmlString(
            '<div><label for="f-week" class="mb-1 block text-[13px] font-bold text-primary-800">Week</label>'
            .'<select id="f-week" name="week" class="'.$control.'"><option value="">Any week</option>'
            .collect(range(1, 24))->map(fn ($w) => '<option value="'.$w.'"'.((int) ($filters['week'] ?? 0) === $w ? ' selected' : '').'>Week '.$w.'</option>')->implode('')
            .'</select></div>'),
    ])
    <p class="mt-3 text-sm text-gray-500" aria-live="polite">{{ number_format($plans->total()) }} {{ \Illuminate\Support\Str::plural('lesson plan', $plans->total()) }}@if ($subject) in {{ $subject->name }}@endif</p>

    @if ($plans->isEmpty())
        <div class="mt-6 rounded-2xl bg-white p-8 text-center shadow-card ring-1 ring-[#ece9f3]">
            <i class="fas fa-clipboard-list text-4xl text-gray-300" aria-hidden="true"></i>
            <p class="mt-3 text-gray-500">{{ collect($filters)->except('sort')->filter()->isNotEmpty() ? 'No lesson plan matches. Try another filter.' : 'Lesson plans are being written. Check back soon.' }}</p>
        </div>
    @else
        <ul class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($plans as $plan)
                <li>
                    <a href="{{ route('lesson-plans.show', $plan) }}" class="group flex h-full flex-col rounded-2xl bg-white p-5 no-underline shadow-card ring-1 ring-[#ece9f3] transition hover:-translate-y-0.5 hover:shadow-md">
                        <span class="flex flex-wrap items-center gap-2 text-[11px] font-bold uppercase tracking-wide">
                            @if ($plan->weekLabel())<span class="rounded-full bg-primary-50 px-2 py-0.5 text-primary-700">{{ $plan->weekLabel() }}</span>@endif
                            <span class="rounded-full bg-gold-100 px-2 py-0.5 text-gold-800">{{ collect([$plan->subject?->educationLevel?->code, $plan->classLevel?->name])->filter()->implode(' · ') ?: 'All years' }}</span>
                        </span>
                        <span class="mt-3 block font-semibold text-gray-800 group-hover:text-primary-700">{{ $plan->title }}</span>
                        <span class="mt-1 block text-sm text-gray-500">{{ $plan->subject?->name }}@if ($plan->strandLabel()) · {{ \Illuminate\Support\Str::limit($plan->strandLabel(), 70) }}@endif</span>
                        <span class="mt-auto pt-4 text-xs text-gray-400">By {{ $plan->authorName() }}@if ($plan->lessons_per_week) · {{ $plan->lessons_per_week }} {{ \Illuminate\Support\Str::plural('lesson', $plan->lessons_per_week) }}@endif @if ($plan->duration_minutes) × {{ $plan->duration_minutes }} min @endif</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $plans->links('pagination::tailwind') }}</div>
    @endif
</div>
@endsection
