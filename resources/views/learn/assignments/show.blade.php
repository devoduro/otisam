@extends('layouts.portal')

@php
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]';
    $submitted = (bool) $submission?->isSubmitted();
@endphp

@section('title', $assignment->title)
@section('subtitle', ($assignment->isQuiz() ? 'Quiz' : 'Assignment').' · '.$assignment->classroom->name.' · '.$assignment->classroom->teacher->name)

@section('actions')
    <x-button variant="outline" :href="route('learn.classes.show', $assignment->classroom)" icon="fa-arrow-left">Class</x-button>
@endsection

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <section class="panel p-5">
            <dl class="grid gap-3 text-sm sm:grid-cols-3">
                <div><dt class="text-gray-500">Due</dt><dd class="font-semibold {{ $assignment->isOverdue() && ! $submitted ? 'text-red-600' : 'text-gray-800' }}">{{ $assignment->due_at ? $assignment->due_at->format('D j M, g:ia') : 'No due date' }}</dd></div>
                @if ($assignment->isQuiz())
                    <div><dt class="text-gray-500">Questions</dt><dd class="font-semibold text-gray-800">{{ $assignment->questions->count() }}</dd></div>
                    <div><dt class="text-gray-500">Time limit</dt><dd class="font-semibold text-gray-800">{{ $assignment->time_limit_minutes ? $assignment->time_limit_minutes.' minutes' : 'None' }}</dd></div>
                @elseif ($assignment->max_marks)
                    <div><dt class="text-gray-500">Marks</dt><dd class="font-semibold text-gray-800">{{ (float) $assignment->max_marks }}</dd></div>
                @endif
            </dl>
            @if ($assignment->instructions)<p class="mt-4 whitespace-pre-line border-t border-gray-100 pt-4 text-gray-700">{{ $assignment->instructions }}</p>@endif
            @if ($assignment->file_name)
                <a href="{{ route('learn.assignments.handout', $assignment) }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-50 px-4 py-2 text-sm font-semibold no-underline"><i class="fas fa-paperclip" aria-hidden="true"></i> {{ $assignment->file_name }}</a>
            @endif
        </section>

        @if ($assignment->isQuiz())
            @if (! $submitted)
                <section class="panel p-6 text-center">
                    @if ($assignment->isOpen())
                        <p class="text-gray-600">{{ $submission?->started_at ? 'You have started this quiz.' : 'When you are ready, start the quiz.' }} @if ($assignment->time_limit_minutes) The {{ $assignment->time_limit_minutes }}-minute timer {{ $submission?->started_at ? 'is running' : 'starts when you begin' }}.@endif</p>
                        <x-button class="mt-4" variant="accent" :href="route('learn.assignments.take', $assignment)" icon="fa-play">{{ $submission?->started_at ? 'Continue quiz' : 'Start quiz' }}</x-button>
                    @else
                        <p class="text-gray-600">This quiz is closed.</p>
                    @endif
                </section>
            @else
                <section class="relative overflow-hidden rounded-2xl bg-primary-700 bg-crosshatch p-6 text-white shadow-lift">
                    <p class="eyebrow">Your score</p>
                    <p class="mt-1 font-display text-4xl font-extrabold">{{ (float) $submission->score }} / {{ (float) $submission->max_score }}</p>
                    <p class="mt-1 text-white/75">Submitted {{ $submission->submitted_at->format('D j M, g:ia') }}{{ $submission->is_late ? ' (late)' : '' }}.</p>
                    @if ($submission->feedback)<p class="mt-3 rounded-xl bg-white/10 p-3 text-sm"><strong>Teacher:</strong> {{ $submission->feedback }}</p>@endif
                </section>

                @if ($review && $assignment->show_answers)
                    <ol class="space-y-3">
                        @foreach ($assignment->questions as $question)
                            @php
                                $revision = $review['revisions'][$question->pivot->question_revision_id] ?? null;
                                $correct = $review['keys'][$question->pivot->question_revision_id]['correct'] ?? null;
                                $given = $submission->answers[$question->ulid] ?? null;
                            @endphp
                            <li class="panel p-4 {{ $given === $correct ? 'ring-green-200' : 'ring-red-200' }}">
                                <p class="font-semibold text-gray-800"><i class="fas {{ $given === $correct ? 'fa-circle-check text-green-600' : 'fa-circle-xmark text-red-600' }} mr-1" aria-hidden="true"></i>{{ $loop->iteration }}. {{ $revision?->stem }}</p>
                                <ul class="mt-2 grid gap-1 text-sm sm:grid-cols-2">
                                    @foreach ($assignment->optionsFor($revision, $submission, $question->ulid) as $o)
                                        <li class="rounded-lg px-2 py-1 {{ $o['id'] === $correct ? 'bg-green-50 font-semibold text-green-800' : ($o['id'] === $given ? 'bg-red-50 text-red-800' : 'text-gray-600') }}">{{ $o['label'] }}. {{ $o['text'] }}@if ($o['id'] === $given) <span class="text-xs">(your answer)</span>@endif</li>
                                    @endforeach
                                </ul>
                                @if ($revision?->explanation)<p class="mt-2 rounded-lg bg-primary-50 px-3 py-2 text-sm text-primary-800"><strong>Explanation:</strong> {{ $revision->explanation }}</p>@endif
                            </li>
                        @endforeach
                    </ol>
                @elseif ($review)
                    <p class="panel p-4 text-sm text-gray-500">Your teacher has chosen not to show the answers for this quiz.</p>
                @endif
            @endif
        @else
            {{-- Written assignment --}}
            @if ($submission?->isGraded())
                <section class="relative overflow-hidden rounded-2xl bg-primary-700 bg-crosshatch p-6 text-white shadow-lift">
                    <p class="eyebrow">Marked</p>
                    @if ($submission->score !== null)<p class="mt-1 font-display text-4xl font-extrabold">{{ (float) $submission->score }}{{ $submission->max_score ? ' / '.(float) $submission->max_score : '' }}</p>@endif
                    @if ($submission->feedback)<p class="mt-3 whitespace-pre-line rounded-xl bg-white/10 p-3 text-sm"><strong>Teacher:</strong> {{ $submission->feedback }}</p>@endif
                </section>
            @endif

            <section class="panel p-5">
                <h2 class="font-bold text-gray-800">{{ $submitted ? 'Your work' : 'Hand in your work' }}</h2>
                @if ($submitted)
                    <p class="mt-1 text-sm text-gray-500">Handed in {{ $submission->submitted_at->format('D j M, g:ia') }}{{ $submission->is_late ? ' (late)' : '' }}.</p>
                @endif

                @if ($assignment->isOpen() && ! $submission?->isGraded())
                    <form method="POST" enctype="multipart/form-data" action="{{ route('learn.assignments.submit-work', $assignment) }}" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="body" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Your answer</label>
                            <textarea id="body" name="body" rows="8" maxlength="20000" class="{{ $control }}" placeholder="Type your answer here, or attach a file (e.g. a photo of your exercise book).">{{ old('body', $submission?->body) }}</textarea>
                            @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="file" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Attach a file <span class="font-normal text-gray-500">(optional: photo, PDF or Word)</span></label>
                            @if ($submission?->file_name)<p class="mb-2 text-sm"><a href="{{ route('learn.assignments.my-file', $assignment) }}"><i class="fas fa-paperclip mr-1" aria-hidden="true"></i>{{ $submission->file_name }}</a></p>@endif
                            <input id="file" type="file" name="file" accept=".pdf,.docx,.pptx,.xlsx,.jpg,.jpeg,.png,.webp" class="block w-full text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:font-semibold file:text-primary-700">
                            @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <x-button variant="accent" icon="fa-paper-plane">{{ $submitted ? 'Update my work' : 'Hand in' }}</x-button>
                    </form>
                @else
                    @if ($submission?->body)<div class="mt-3 whitespace-pre-line rounded-xl bg-gray-50 p-3 text-sm text-gray-700">{{ $submission->body }}</div>@endif
                    @if ($submission?->file_name)<p class="mt-2 text-sm"><a href="{{ route('learn.assignments.my-file', $assignment) }}"><i class="fas fa-paperclip mr-1" aria-hidden="true"></i>{{ $submission->file_name }}</a></p>@endif
                    @if (! $assignment->isOpen() && ! $submitted)<p class="mt-2 text-sm text-gray-500">This assignment is closed.</p>@endif
                @endif
            </section>
        @endif
    </div>
</div>
@endsection
