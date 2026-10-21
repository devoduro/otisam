@extends('layouts.app')

@section('title', $question->exists ? 'Edit question' : 'Ask a question')

@section('content')
@php
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]';
    $label = 'mb-1.5 block text-[13.5px] font-bold text-primary-800';
    $optional = '<span class="font-normal text-gray-500">(optional)</span>';
@endphp
<div class="mx-auto max-w-3xl px-4 py-8 md:px-6 sm:py-12">
    <nav aria-label="Breadcrumb" class="text-sm text-gray-500"><a href="{{ route('qa.index') }}">Q&amp;A</a></nav>
    <h1 class="mt-2 text-2xl font-bold text-gray-800">{{ $question->exists ? 'Edit your question' : 'Ask a question' }}</h1>
    <p class="mt-1 text-sm text-gray-500">Say what you have tried and where you are stuck. Plain text only; do not post exam papers or other people's work.</p>

    <form method="POST" action="{{ $question->exists ? route('qa.update', $question) : route('qa.store') }}"
          x-data="{ level: '{{ (int) old('education_level_id', $question->education_level_id) ?: '' }}' }"
          class="mt-6 space-y-5 rounded-2xl bg-white p-6 shadow-card ring-1 ring-[#ece9f3]">
        @csrf
        @if ($question->exists) @method('PUT') @endif

        <x-input name="title" label="Question" required minlength="10" maxlength="200" :value="$question->title" placeholder="e.g. How do I find the gradient of a line from two points?" />

        <div class="grid gap-5 sm:grid-cols-3">
            <div>
                <label for="education_level_id" class="{{ $label }}">Level</label>
                <select id="education_level_id" name="education_level_id" required x-model="level" class="{{ $control }}">
                    <option value="">Choose…</option>
                    @foreach ($levels as $l)<option value="{{ $l->id }}" @selected((int) old('education_level_id', $question->education_level_id) === $l->id)>{{ $l->name }}</option>@endforeach
                </select>
                @error('education_level_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="class_level_id" class="{{ $label }}">Class {!! $optional !!}</label>
                <select id="class_level_id" name="class_level_id" class="{{ $control }}">
                    <option value="">Any class</option>
                    @foreach ($classLevels as $c)
                        <option value="{{ $c->id }}" x-show="level == '{{ $c->education_level_id }}'" :disabled="level != '{{ $c->education_level_id }}'" @selected((int) old('class_level_id', $question->class_level_id) === $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('class_level_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="subject_id" class="{{ $label }}">Subject {!! $optional !!}</label>
                <select id="subject_id" name="subject_id" class="{{ $control }}">
                    <option value="">General</option>
                    @foreach ($subjects as $s)
                        <option value="{{ $s->id }}" x-show="level == '{{ $s->education_level_id }}'" :disabled="level != '{{ $s->education_level_id }}'" @selected((int) old('subject_id', $question->subject_id) === $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
                @error('subject_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="body" class="{{ $label }}">Details</label>
            <textarea id="body" name="body" rows="8" required minlength="10" maxlength="5000" class="{{ $control }}" placeholder="Write out the full question and show your working so far.">{{ old('body', $question->body) }}</textarea>
            @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-wrap gap-2">
            <x-button icon="fa-paper-plane">{{ $question->exists ? 'Save changes' : 'Post question' }}</x-button>
            <x-button :href="$question->exists ? route('qa.show', $question) : route('qa.index')" variant="ghost">Cancel</x-button>
        </div>
    </form>
</div>
@endsection
