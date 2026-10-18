@extends('layouts.portal')

@section('title', 'My video tutorials')
@section('subtitle', $canPublish ? 'Your videos are published straight away.' : 'Add your YouTube lessons. An administrator checks each one before it goes live.')

@section('actions')
    <x-button :href="route('teach.videos.create')" icon="fa-plus">Add a video</x-button>
@endsection

@section('content')
@if ($videos->isEmpty())
    <div class="panel p-8 text-center">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-2xl text-red-600"><i class="fab fa-youtube" aria-hidden="true"></i></span>
        <h2 class="mt-4 text-lg font-bold text-gray-800">Share your lessons</h2>
        <p class="mx-auto mt-1 max-w-md text-sm text-gray-500">Paste a YouTube link to your lesson. Once it is live, students find it in Video lessons and on your teacher profile.</p>
        <x-button class="mt-5" :href="route('teach.videos.create')" icon="fa-plus">Add my first video</x-button>
    </div>
@else
    <ul class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($videos as $video)
            <li class="panel overflow-hidden">
                <span class="relative block aspect-video bg-slate-900">
                    <img src="{{ $video->thumbnailUrl() }}" alt="" loading="lazy" class="h-full w-full object-cover">
                    <span class="absolute left-2 top-2 rounded-full px-2 py-0.5 text-[11px] font-bold {{ ['published' => 'bg-green-100 text-green-800', 'pending' => 'bg-gold-100 text-gold-800', 'draft' => 'bg-gray-100 text-gray-700', 'archived' => 'bg-gray-200 text-gray-700'][$video->status] ?? 'bg-gray-100' }}">{{ \App\Models\Videos\VideoLesson::STATUSES[$video->status] }}</span>
                </span>
                <div class="p-4">
                    <p class="font-semibold text-gray-800">{{ $video->title }}</p>
                    <p class="text-xs text-gray-500">{{ collect([$video->subject?->name, $video->subject?->educationLevel?->code, $video->classLevel?->name])->filter()->implode(' · ') }}</p>
                    <div class="mt-3 flex gap-2 text-sm">
                        @if ($video->status === 'published')<a href="{{ route('videos.show', $video) }}" class="font-semibold no-underline"><i class="fas fa-eye mr-1" aria-hidden="true"></i>View</a>@endif
                        <a href="{{ route('teach.videos.edit', $video) }}" class="font-semibold no-underline"><i class="fas fa-pen mr-1" aria-hidden="true"></i>Edit</a>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endif
@endsection
