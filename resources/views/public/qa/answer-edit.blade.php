@extends('layouts.app')

@section('title', 'Edit answer')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 md:px-6 sm:py-12">
    <nav aria-label="Breadcrumb" class="text-sm text-gray-500">
        <a href="{{ route('qa.index') }}">Q&amp;A</a> / <a href="{{ route('qa.show', $answer->qa_question_id) }}">{{ \Illuminate\Support\Str::limit($answer->question->title, 60) }}</a>
    </nav>
    <h1 class="mt-2 text-2xl font-bold text-gray-800">Edit your answer</h1>

    <form method="POST" action="{{ route('qa.answers.update', $answer) }}" class="mt-6 space-y-4 rounded-2xl bg-white p-6 shadow-card ring-1 ring-[#ece9f3]">
        @csrf @method('PUT')
        <label for="body" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Answer</label>
        <textarea id="body" name="body" rows="8" required maxlength="5000" class="block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]">{{ old('body', $answer->body) }}</textarea>
        @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        <div class="flex flex-wrap gap-2">
            <x-button icon="fa-floppy-disk">Save changes</x-button>
            <x-button :href="route('qa.show', $answer->qa_question_id).'#answer-'.$answer->id" variant="ghost">Cancel</x-button>
        </div>
    </form>
</div>
@endsection
