@extends('layouts.app')

@section('title', $video->title)
@section('description', \Illuminate\Support\Str::limit($video->description ?: $video->title, 155))

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 md:px-6 sm:py-12">
    <nav aria-label="Breadcrumb" class="text-sm text-gray-500">
        <a href="{{ route('videos.index') }}">Video lessons</a>
        @if ($video->subject) / <a href="{{ route('videos.index', ['subject' => $video->subject->slug]) }}">{{ $video->subject->name }}</a>@endif
    </nav>

    @unless ($video->isVisible())
        <p class="mt-4 rounded-md bg-amber-50 p-3 text-sm" role="status">Staff view: this video is not public ({{ \App\Models\Videos\VideoLesson::STATUSES[$video->status] }}).</p>
    @endunless

    <h1 class="mt-2 text-2xl font-bold text-gray-800 sm:text-3xl">{{ $video->title }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ collect([$video->subject?->name, $video->classLevel?->name, $video->instructor, $video->duration_minutes ? $video->duration_minutes.' min' : null])->filter()->join(' · ') }}</p>

    <div class="mt-5 overflow-hidden rounded-md bg-slate-900 shadow-sm">
        @if ($canWatch)
            {{-- Loads YouTube only when the viewer presses play: saves data and avoids YouTube cookies until then. --}}
            <div x-data="{ playing: false }" class="relative aspect-video">
                <template x-if="playing">
                    <iframe class="absolute inset-0 h-full w-full" src="{{ $video->embedUrl() }}" title="{{ $video->title }}" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                </template>
                <button type="button" x-show="!playing" @click="playing = true" class="group absolute inset-0 h-full w-full" aria-label="Play {{ $video->title }}">
                    <img src="{{ $video->thumbnailUrl() }}" alt="" class="h-full w-full object-cover opacity-80">
                    <span class="absolute inset-0 flex items-center justify-center"><span class="flex h-16 w-16 items-center justify-center rounded-full bg-white text-primary-600 shadow-lg transition group-hover:scale-105"><i class="fas fa-play text-xl" aria-hidden="true"></i></span></span>
                </button>
            </div>
        @else
            <div class="relative aspect-video">
                <img src="{{ $video->thumbnailUrl() }}" alt="" class="h-full w-full object-cover opacity-40">
                <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-white">
                    <i class="fas fa-crown text-3xl text-yellow-300" aria-hidden="true"></i>
                    <p class="mt-3 text-lg font-semibold">Premium video lesson</p>
                    <p class="mt-1 text-sm text-slate-200">Get premium access to watch this lesson.</p>
                    <x-button :href="route('plans.index')" variant="accent" class="mt-4">See premium plans</x-button>
                </div>
            </div>
        @endif
    </div>

    @if ($video->description)
        <div class="mt-6 rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
            <h2 class="text-sm font-semibold text-gray-700">In this lesson</h2>
            <p class="mt-2 whitespace-pre-line text-gray-700">{{ $video->description }}</p>
        </div>
    @endif

    <div class="mt-4 flex flex-wrap gap-2 text-sm">
        @if ($video->topic && $video->topic->version?->isPublic() && $video->subject)
            <a href="{{ route('subjects.topic', [$video->subject, $video->topic]) }}" class="rounded-full border border-gray-300 bg-white px-3 py-1 no-underline hover:border-primary-600"><i class="fas fa-sitemap mr-1" aria-hidden="true"></i> {{ \Illuminate\Support\Str::limit($video->topic->title, 50) }}</a>
        @endif
        @if ($video->subject)
            <a href="{{ route('library.index', ['subject' => $video->subject->slug]) }}" class="rounded-full border border-gray-300 bg-white px-3 py-1 no-underline hover:border-primary-600"><i class="fas fa-book-open mr-1" aria-hidden="true"></i> {{ $video->subject->name }} past questions</a>
        @endif
    </div>

    @if ($related->isNotEmpty())
        <h2 class="mt-10 text-lg font-bold text-gray-800">More video lessons</h2>
        <ul class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($related as $item)
                <li>
                    <a href="{{ route('videos.show', $item) }}" class="flex gap-3 rounded-2xl bg-white p-2 no-underline shadow-card ring-1 ring-[#ece9f3] hover:ring-primary-200">
                        <img src="{{ $item->thumbnailUrl() }}" alt="" loading="lazy" class="h-16 w-28 shrink-0 rounded object-cover">
                        <span class="text-sm font-medium text-gray-800">{{ \Illuminate\Support\Str::limit($item->title, 70) }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <p class="mt-10 text-xs text-gray-500">Videos are played from YouTube. <a href="{{ route('copyright') }}">Report a problem with this video</a>.</p>
</div>
@endsection
