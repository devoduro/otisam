@extends('layouts.portal')

@section('title', $video->exists ? 'Edit video' : 'Add a video tutorial')
@section('subtitle', $canPublish ? 'Published as soon as you save.' : 'Checked by an administrator before it goes live. Editing a live video sends it for review again.')

@section('content')
@php $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]'; @endphp
<form method="POST" action="{{ $video->exists ? route('teach.videos.update', $video) : route('teach.videos.store') }}" class="panel max-w-3xl space-y-5 p-6">
    @csrf
    @if ($video->exists) @method('PUT') @endif

    <x-input name="youtube" label="YouTube link" required maxlength="255" :value="$video->youtube_id ? 'https://www.youtube.com/watch?v='.$video->youtube_id : null" placeholder="https://www.youtube.com/watch?v=… or https://youtu.be/…" hint="The video stays on YouTube; we embed it." />
    <x-input name="title" label="Title" required maxlength="200" :value="$video->title" placeholder="e.g. Solving quadratic equations by factorisation" />

    <div class="grid gap-5 sm:grid-cols-3">
        <div>
            <label for="subject_id" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Subject</label>
            <select id="subject_id" name="subject_id" required class="{{ $control }}">
                <option value="">Choose…</option>
                @include('partials.subject-options', ['levels' => $levels, 'selected' => old('subject_id', $video->subject_id)])
            </select>
            @error('subject_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="class_level_id" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Class <span class="font-normal text-gray-500">(optional)</span></label>
            <select id="class_level_id" name="class_level_id" class="{{ $control }}">
                <option value="">Any</option>
                @foreach ($levels as $l)
                    <optgroup label="{{ $l->name }}">
                        @foreach ($l->classLevels as $y)<option value="{{ $y->id }}" @selected((int) old('class_level_id', $video->class_level_id) === $y->id)>{{ $y->name }}</option>@endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <x-input name="duration_minutes" type="number" label="Length (minutes)" min="1" max="600" :value="$video->duration_minutes" />
    </div>

    <div>
        <label for="description" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Description <span class="font-normal text-gray-500">(optional)</span></label>
        <textarea id="description" name="description" rows="4" maxlength="5000" class="{{ $control }}" placeholder="What the lesson covers">{{ old('description', $video->description) }}</textarea>
    </div>

    <p class="text-xs text-gray-500">Only add videos you made or have permission to share.</p>

    <div class="flex flex-wrap gap-3">
        <x-button icon="{{ $canPublish ? 'fa-paper-plane' : 'fa-paper-plane' }}">{{ $canPublish ? 'Publish' : 'Send for review' }}</x-button>
        <x-button variant="outline" :href="route('teach.videos.index')">Cancel</x-button>
    </div>
</form>

@if ($video->exists)
    <form method="POST" action="{{ route('teach.videos.destroy', $video) }}" class="mt-4" onsubmit="return confirm('Remove this video from {{ config('app.name') }}? It stays on YouTube.')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700"><i class="fas fa-trash mr-1" aria-hidden="true"></i>Remove</button>
    </form>
@endif
@endsection
