@extends('layouts.app')

@section('title', $plan->title.' · Lesson plan')
@section('description', \Illuminate\Support\Str::limit(collect([$plan->subject?->name, $plan->classLevel?->name, $plan->weekLabel(), $plan->strandLabel()])->filter()->implode(' · '), 155))

@php
    use App\Models\LessonPlans\LessonPlan;
    $user = auth()->user();
    $isOwner = $user && $plan->created_by === $user->id;
    $facts = [
        'Subject' => $plan->subject?->labelWithLevel(),
        'Year' => $plan->classLevel?->name ?? 'Any year',
        'Week' => $plan->weekLabel(),
        'Lessons' => $plan->lessons_per_week ? $plan->lessons_per_week.' × '.($plan->duration_minutes ? $plan->duration_minutes.' min' : 'lesson') : ($plan->duration_minutes ? $plan->duration_minutes.' min' : null),
        'Class size' => $plan->class_size,
    ];
    $sections = [
        'Strand' => $plan->strand,
        'Sub-strand' => $plan->sub_strand,
        'Content standard' => $plan->content_standard,
    ];
    $lists = [
        'Learning indicators' => ['fa-bullseye', $plan->indicators],
        'Core competencies' => ['fa-puzzle-piece', $plan->core_competencies],
        'Resources' => ['fa-box-open', $plan->resources],
    ];
    $after = [
        'Assessment' => ['fa-clipboard-check', $plan->assessment],
        'Differentiation' => ['fa-people-arrows', $plan->differentiation],
        'Homework' => ['fa-house-chimney', $plan->homework],
        'Teacher notes' => ['fa-note-sticky', $plan->teacher_notes],
    ];
@endphp

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 md:px-6 sm:py-12">
    <nav aria-label="Breadcrumb" class="text-sm text-gray-500">
        <a href="{{ route('lesson-plans.index') }}">Lesson plans</a>
        @if ($plan->subject) / <a href="{{ route('lesson-plans.index', ['subject' => $plan->subject->slug]) }}">{{ $plan->subject->name }}</a>@endif
    </nav>

    @unless ($plan->isVisible())
        <p class="mt-4 rounded-md bg-amber-50 p-3 text-sm" role="status">
            {{ $isOwner ? 'Only you can see this plan' : 'Staff view: this plan is not public' }} ({{ LessonPlan::STATUSES[$plan->status] ?? $plan->status }}).
            @if ($plan->review_note) Reviewer's note: {{ $plan->review_note }} @endif
        </p>
    @endunless

    <div class="mt-2 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl">{{ $plan->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">By {{ $plan->authorName() }}@if ($plan->published_at) · {{ $plan->published_at->format('j M Y') }}@endif</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button :href="route('lesson-plans.print', $plan)" variant="outline" icon="fa-print" target="_blank" rel="noopener">Print / PDF</x-button>
            <x-button :href="route('lesson-plans.download', $plan)" variant="outline" icon="fa-file-word">Download (Word)</x-button>
            @if ($isOwner)
                <x-button :href="route('teach.lesson-plans.edit', $plan)" icon="fa-pen">Edit</x-button>
            @elseif ($user?->isApprovedTeacher())
                <x-button :href="route('teach.lesson-plans.create', ['from' => $plan->slug])" icon="fa-copy">Use as my starting point</x-button>
            @endif
        </div>
    </div>

    <dl class="mt-6 grid gap-3 rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3] sm:grid-cols-3 lg:grid-cols-5">
        @foreach ($facts as $label => $value)
            @continue(blank($value))
            <div><dt class="text-xs font-bold uppercase tracking-wide text-gray-400">{{ $label }}</dt><dd class="mt-0.5 font-semibold text-gray-800">{{ $value }}</dd></div>
        @endforeach
    </dl>

    @if (collect($sections)->filter()->isNotEmpty())
        <dl class="mt-4 space-y-3 rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
            @foreach ($sections as $label => $value)
                @continue(blank($value))
                <div class="grid gap-1 sm:grid-cols-[11rem_1fr]"><dt class="text-sm font-bold text-primary-800">{{ $label }}</dt><dd class="whitespace-pre-line text-gray-700">{{ $value }}</dd></div>
            @endforeach
            @if ($plan->key_vocabulary)
                <div class="grid gap-1 sm:grid-cols-[11rem_1fr]"><dt class="text-sm font-bold text-primary-800">Key words</dt>
                    <dd class="flex flex-wrap gap-1.5">@foreach (preg_split('/\s*[,;]\s*/', $plan->key_vocabulary, -1, PREG_SPLIT_NO_EMPTY) as $word)<span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-sm text-gray-700">{{ $word }}</span>@endforeach</dd></div>
            @endif
        </dl>
    @endif

    <div class="mt-4 grid gap-4 md:grid-cols-3">
        @foreach ($lists as $label => [$icon, $text])
            @continue(blank($text))
            <section class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
                <h2 class="text-sm font-bold text-primary-800"><i class="fas {{ $icon }} mr-1.5 text-gold-600" aria-hidden="true"></i>{{ $label }}</h2>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-gray-700">@foreach (LessonPlan::lines($text) as $line)<li>{{ $line }}</li>@endforeach</ul>
            </section>
        @endforeach
    </div>

    <h2 class="mt-8 text-lg font-bold text-gray-800">Lesson flow</h2>
    <ol class="mt-3 space-y-3">
        @foreach (LessonPlan::PHASES as $field => [$label, $hint])
            @continue(blank($plan->{$field}))
            <li class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
                <h3 class="flex items-center gap-2 font-bold text-gray-800"><span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary-600 text-sm text-white">{{ $loop->iteration }}</span>{{ $label }}</h3>
                <div class="mt-2 whitespace-pre-line text-gray-700">{{ $plan->{$field} }}</div>
            </li>
        @endforeach
    </ol>

    <div class="mt-4 grid gap-4 md:grid-cols-2">
        @foreach ($after as $label => [$icon, $text])
            @continue(blank($text))
            <section class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
                <h2 class="text-sm font-bold text-primary-800"><i class="fas {{ $icon }} mr-1.5 text-gold-600" aria-hidden="true"></i>{{ $label }}</h2>
                <div class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ $text }}</div>
            </section>
        @endforeach
    </div>

    <div class="mt-6 flex flex-wrap gap-2 text-sm">
        @if ($plan->topic && $plan->topic->version?->isPublic() && $plan->subject)
            <a href="{{ route('subjects.topic', [$plan->subject, $plan->topic]) }}" class="rounded-full border border-gray-300 bg-white px-3 py-1 no-underline hover:border-primary-600"><i class="fas fa-sitemap mr-1" aria-hidden="true"></i> {{ \Illuminate\Support\Str::limit($plan->topic->title, 50) }}</a>
        @endif
        @if ($plan->subject)
            <a href="{{ route('practice.show', $plan->subject) }}" class="rounded-full border border-gray-300 bg-white px-3 py-1 no-underline hover:border-primary-600"><i class="fas fa-list-check mr-1" aria-hidden="true"></i> {{ $plan->subject->name }} practice questions</a>
            <a href="{{ route('videos.index', ['subject' => $plan->subject->slug]) }}" class="rounded-full border border-gray-300 bg-white px-3 py-1 no-underline hover:border-primary-600"><i class="fas fa-circle-play mr-1" aria-hidden="true"></i> Video lessons</a>
        @endif
    </div>

    @if ($related->isNotEmpty())
        <h2 class="mt-10 text-lg font-bold text-gray-800">More {{ $plan->subject?->name }} plans</h2>
        <ul class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($related as $item)
                <li>
                    <a href="{{ route('lesson-plans.show', $item) }}" class="block rounded-2xl bg-white p-4 no-underline shadow-card ring-1 ring-[#ece9f3] hover:ring-primary-200">
                        @if ($item->weekLabel())<span class="text-xs font-bold text-primary-600">{{ $item->weekLabel() }}</span>@endif
                        <span class="block text-sm font-medium text-gray-800">{{ \Illuminate\Support\Str::limit($item->title, 70) }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <p class="mt-10 text-xs text-gray-500">Plans are written by {{ config('app.name') }} teachers and are a starting point: adapt them to your learners. <a href="{{ route('copyright') }}">Report a problem with this plan</a>.</p>
</div>
@endsection
