@extends('layouts.app')

@section('title', 'Video lessons')
@section('description', 'Short video lessons for SHS subjects, organised by subject and topic.')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 md:px-6 sm:py-12">
    <h1 class="text-3xl font-extrabold text-gray-800">Video lessons</h1>
    <p class="mt-1 text-gray-500">Watch a teacher explain the topic, then practise with past questions.</p>

    @include('public.partials.catalogue-filters', [
        'action' => route('videos.index'),
        'searchPlaceholder' => 'e.g. photosynthesis, simultaneous equations',
        'sorts' => ['newest' => 'Newest first', 'oldest' => 'Oldest first', 'popular' => 'Most watched', 'shortest' => 'Shortest first'],
    ])
    <p class="mt-3 text-sm text-gray-500" aria-live="polite">{{ number_format($videos->total()) }} {{ \Illuminate\Support\Str::plural('video', $videos->total()) }}@if ($subject) in {{ $subject->name }}@endif</p>

    @if ($videos->isEmpty())
        <div class="mt-6 rounded-2xl bg-white p-8 text-center shadow-card ring-1 ring-[#ece9f3]">
            <i class="fas fa-circle-play text-4xl text-gray-300" aria-hidden="true"></i>
            <p class="mt-3 text-gray-500">{{ array_filter($filters) ? 'No video matches. Try another search.' : 'Video lessons are being prepared. Check back soon.' }}</p>
        </div>
    @else
        <ul class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($videos as $video)
                <li>
                    <a href="{{ route('videos.show', $video) }}" class="group block h-full overflow-hidden rounded-2xl bg-white no-underline shadow-card ring-1 ring-[#ece9f3] transition hover:-translate-y-0.5 hover:shadow-md">
                        <span class="relative block aspect-video bg-slate-900">
                            <img src="{{ $video->thumbnailUrl() }}" alt="" loading="lazy" class="h-full w-full object-cover opacity-90">
                            <span class="absolute inset-0 flex items-center justify-center"><span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 text-primary-600 shadow"><i class="fas fa-play" aria-hidden="true"></i></span></span>
                            @if ($video->duration_minutes)<span class="absolute bottom-2 right-2 rounded bg-black/70 px-1.5 py-0.5 text-[11px] font-semibold text-white">{{ $video->duration_minutes }} min</span>@endif
                            @unless ($video->isFree())<span class="absolute left-2 top-2 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800"><i class="fas fa-crown mr-1" aria-hidden="true"></i>Premium</span>@endunless
                        </span>
                        <span class="block p-4">
                            <span class="block font-semibold text-gray-800">{{ $video->title }}</span>
                            <span class="mt-1 block text-xs text-gray-500">{{ collect([$video->subject?->name, $video->classLevel?->name, $video->instructor])->filter()->join(' · ') }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $videos->links('pagination::tailwind') }}</div>
    @endif
</div>
@endsection
