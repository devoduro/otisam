@extends('layouts.portal')

@php $isQuiz = $assignment->type === 'quiz'; @endphp

@section('title', ($assignment->exists ? 'Edit ' : 'New ').($isQuiz ? 'quiz' : 'assignment'))
@section('subtitle', $classroom->name)

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $assignment->exists ? route('teach.assignments.update', $assignment) : route('teach.assignments.store', $classroom) }}" class="panel max-w-3xl space-y-5 p-6">
    @csrf
    @if ($assignment->exists) @method('PUT') @else <input type="hidden" name="type" value="{{ $assignment->type }}"> @endif

    <x-input name="title" label="Title" required maxlength="150" :value="$assignment->title" :placeholder="$isQuiz ? 'e.g. Quiz 3: Quadratic equations' : 'e.g. Homework: Photosynthesis essay'" />

    <div>
        <label for="instructions" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Instructions <span class="font-normal text-gray-500">(optional)</span></label>
        <textarea id="instructions" name="instructions" rows="6" maxlength="10000" class="block w-full rounded-xl border-[1.5px] border-[#e2e0ea] px-3.5 py-3 text-[15px]" placeholder="{{ $isQuiz ? 'e.g. Answer all questions. Calculators allowed.' : 'Tell students what to do and how it will be marked.' }}">{{ old('instructions', $assignment->instructions) }}</textarea>
        @error('instructions')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <x-input name="due_at" type="datetime-local" label="Due" :value="optional($assignment->due_at)->format('Y-m-d\TH:i')" hint="Late work is still accepted until you close it." />
        @if ($isQuiz)
            <x-input name="time_limit_minutes" type="number" label="Time limit (minutes)" min="1" max="300" :value="$assignment->time_limit_minutes" hint="Leave empty for no time limit." />
        @else
            <x-input name="max_marks" type="number" label="Total marks" min="1" max="1000" step="0.5" :value="$assignment->max_marks !== null ? (float) $assignment->max_marks : null" />
        @endif
    </div>

    <div>
        <label for="handout" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Handout <span class="font-normal text-gray-500">(optional: PDF, Word, PowerPoint or image)</span></label>
        @if ($assignment->file_name)<p class="mb-2 text-sm text-gray-600"><i class="fas fa-paperclip mr-1" aria-hidden="true"></i>Current: {{ $assignment->file_name }}. Choose a file to replace it.</p>@endif
        <input id="handout" type="file" name="handout" class="block w-full text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:font-semibold file:text-primary-700">
        @error('handout')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    @if ($isQuiz)
        <label class="flex items-center gap-3 text-sm font-semibold text-gray-700">
            <input type="hidden" name="show_answers" value="0">
            <input type="checkbox" name="show_answers" value="1" class="h-4 w-4 accent-[#28409e]" @checked(old('show_answers', $assignment->show_answers))>
            Show students the correct answers after they submit
        </label>
        <label class="flex items-start gap-3 text-sm font-semibold text-gray-700">
            <input type="hidden" name="shuffle_options" value="0">
            <input type="checkbox" name="shuffle_options" value="1" class="mt-0.5 h-4 w-4 accent-[#28409e]" @checked(old('shuffle_options', $assignment->shuffle_options))>
            <span>Shuffle the answer options for each student
                <span class="block font-normal text-gray-500">Students sitting together see A, B, C, D in a different order, so they cannot share letters.</span></span>
        </label>
        <label class="flex items-start gap-3 text-sm font-semibold text-gray-700">
            <input type="hidden" name="block_copy" value="0">
            <input type="checkbox" name="block_copy" value="1" class="mt-0.5 h-4 w-4 accent-[#28409e]" @checked(old('block_copy', $assignment->block_copy))>
            <span>Stop students copying the questions
                <span class="block font-normal text-gray-500">Turns off selecting, copying and right-click on the quiz page, so questions cannot be pasted into AI or a search engine. Every quiz already warns students not to leave the page, and shows you how often each student did.</span></span>
        </label>
    @endif

    <div class="flex gap-3">
        <x-button icon="fa-save">{{ $assignment->exists ? 'Save' : ($isQuiz ? 'Save and add questions' : 'Save draft') }}</x-button>
        <x-button variant="outline" :href="$assignment->exists ? route('teach.assignments.show', $assignment) : route('teach.classes.show', $classroom)">Cancel</x-button>
    </div>
</form>
@endsection
