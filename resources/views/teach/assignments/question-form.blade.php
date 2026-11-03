@extends('layouts.portal')

@section('title', 'Edit question '.$number)
@section('subtitle', $assignment->title)

@section('content')
@php
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]';
    $options = old('options', collect($revision->payload['options'] ?? [])->pluck('text')->all());
    $letters = array_slice(['A', 'B', 'C', 'D', 'E'], 0, max(4, count($options)));
@endphp

@if ($locked)
    <p class="panel mb-4 max-w-3xl p-4 text-sm text-gray-600" role="status"><i class="fas fa-lock mr-1" aria-hidden="true"></i> Students have already taken this quiz, so its questions can no longer change.</p>
@endif

<form method="POST" action="{{ route('teach.assignments.questions.update', [$assignment, $question]) }}" class="panel max-w-3xl space-y-5 p-6">
    @csrf @method('PUT')

    @if ($isCopy)
        <p class="rounded-xl bg-primary-50 px-4 py-3 text-sm text-primary-800"><i class="fas fa-circle-info mr-1" aria-hidden="true"></i> This question comes from the shared question bank. Your changes are saved as your own copy for this quiz; the bank keeps the original.</p>
    @endif

    <div>
        <label for="stem" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Question</label>
        <textarea id="stem" name="stem" rows="3" maxlength="2000" required class="{{ $control }}">{{ old('stem', $revision->stem) }}</textarea>
        @error('stem')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <fieldset class="space-y-2">
        <legend class="mb-1.5 text-[13.5px] font-bold text-primary-800">Options <span class="font-normal text-gray-500">(tick the correct answer)</span></legend>
        @foreach ($letters as $i => $letter)
            <label class="flex items-center gap-2">
                <input type="radio" name="correct" value="{{ $letter }}" required class="accent-[#28409e]" @checked(old('correct', $correct) === $letter) title="Mark {{ $letter }} as the correct answer">
                <span class="w-4 text-sm font-bold">{{ $letter }}</span>
                <input name="options[]" value="{{ $options[$i] ?? '' }}" required maxlength="500" class="{{ $control }}" aria-label="Option {{ $letter }}">
            </label>
        @endforeach
        @error('options')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        @error('options.*')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        @error('correct')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </fieldset>

    <div>
        <label for="explanation" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Explanation <span class="font-normal text-gray-500">(optional, shown after submitting if you allow answers)</span></label>
        <textarea id="explanation" name="explanation" rows="2" maxlength="2000" class="{{ $control }}">{{ old('explanation', $revision->explanation) }}</textarea>
    </div>

    <div class="max-w-[12rem]">
        <label for="marks" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Marks</label>
        <input id="marks" name="marks" type="number" min="0.5" max="20" step="0.5" required value="{{ old('marks', $marks) }}" class="{{ $control }}">
        @error('marks')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex gap-3">
        <x-button icon="fa-save" :disabled="$locked">Save question</x-button>
        <x-button variant="outline" :href="route('teach.assignments.show', $assignment)">Cancel</x-button>
    </div>
</form>
@endsection
