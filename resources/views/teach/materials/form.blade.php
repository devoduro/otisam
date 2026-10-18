@extends('layouts.portal')

@php
    use App\Models\Classroom\TeacherMaterial as M;
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]';
@endphp

@section('title', $material->exists ? 'Edit material' : 'Upload lesson material')

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $material->exists ? route('teach.materials.update', $material) : route('teach.materials.store') }}" class="panel max-w-3xl space-y-5 p-6">
    @csrf
    @if ($material->exists) @method('PUT') @endif

    <div class="grid gap-5 sm:grid-cols-3">
        <div class="sm:col-span-2"><x-input name="title" label="Title" required maxlength="150" :value="$material->title" placeholder="e.g. SHS 1 Physics: Motion lesson plan, Week 3" /></div>
        <div>
            <label for="kind" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Type</label>
            <select id="kind" name="kind" class="{{ $control }}">
                @foreach (M::KINDS as $k => $label)<option value="{{ $k }}" @selected(old('kind', $material->kind) === $k)>{{ $label }}</option>@endforeach
            </select>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="subject_id" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Subject <span class="font-normal text-gray-500">(optional)</span></label>
            <select id="subject_id" name="subject_id" class="{{ $control }}">
                <option value="">Any subject</option>
                @include('partials.subject-options', ['levels' => $levels, 'selected' => old('subject_id', $material->subject_id)])
            </select>
        </div>
        <div>
            <label for="class_level_id" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Year <span class="font-normal text-gray-500">(optional)</span></label>
            <select id="class_level_id" name="class_level_id" class="{{ $control }}">
                <option value="">Any year</option>
                @foreach ($levels as $l)
                    <optgroup label="{{ $l->name }}">
                        @foreach ($l->classLevels as $y)<option value="{{ $y->id }}" @selected((int) old('class_level_id', $material->class_level_id) === $y->id)>{{ $y->name }}</option>@endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label for="description" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Description <span class="font-normal text-gray-500">(optional)</span></label>
        <textarea id="description" name="description" rows="3" maxlength="3000" class="{{ $control }}" placeholder="Topic, week, learning indicators covered…">{{ old('description', $material->description) }}</textarea>
    </div>

    <div>
        <label for="file" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">File {{ $material->exists ? '(choose one only to replace it)' : '' }}</label>
        @if ($material->exists)<p class="mb-2 text-sm text-gray-600"><i class="fas fa-paperclip mr-1" aria-hidden="true"></i>{{ $material->file_name }}</p>@endif
        <input id="file" type="file" name="file" @if (! $material->exists) required @endif class="block w-full text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:font-semibold file:text-primary-700">
        <p class="mt-1 text-xs text-gray-500">PDF, Word, PowerPoint, Excel or an image, up to {{ config('platform.library.max_upload_mb') }} MB. Files are checked for safety.</p>
        @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <fieldset>
        <legend class="mb-2 text-[13.5px] font-bold text-primary-800">Who can see it</legend>
        <div class="grid gap-3 sm:grid-cols-3">
            @foreach (M::VISIBILITY as $v => $label)
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border-[1.5px] border-[#e2e0ea] p-3 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                    <input type="radio" name="visibility" value="{{ $v }}" class="mt-1 accent-[#28409e]" @checked(old('visibility', $material->visibility) === $v)>
                    <span><span class="block font-semibold text-gray-800">{{ $label }}</span><span class="text-sm text-gray-500">{{ ['private' => 'Stored for you only.', 'teachers' => 'Other approved teachers can find and download it.', 'public' => 'Listed on the public Downloads page and your teacher profile. Visitors sign in to download.'][$v] }}</span></span>
                </label>
            @endforeach
        </div>
    </fieldset>

    <p class="text-xs text-gray-500">Only share material you wrote or have the right to share. Do not upload WAEC papers or textbook pages.</p>

    <div class="flex flex-wrap items-center gap-3">
        <x-button icon="fa-upload">{{ $material->exists ? 'Save' : 'Upload' }}</x-button>
        <x-button variant="outline" :href="route('teach.materials.index', ['scope' => 'mine'])">Cancel</x-button>
    </div>
</form>

@if ($material->exists)
    <form method="POST" action="{{ route('teach.materials.destroy', $material) }}" class="mt-4" onsubmit="return confirm('Delete this material?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700"><i class="fas fa-trash mr-1" aria-hidden="true"></i>Delete</button>
    </form>
@endif
@endsection
