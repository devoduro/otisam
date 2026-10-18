@extends('layouts.portal')

@php
    use App\Models\Classroom\Assignment as A;
    $classroom = $assignment->classroom;
    $students = $classroom->students;
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm';
@endphp

@section('title', $assignment->title)
@section('subtitle', A::TYPES[$assignment->type].' · '.$classroom->name.' · '.A::STATUSES[$assignment->status])

@section('actions')
    <x-button variant="outline" :href="route('teach.classes.show', $classroom)" icon="fa-arrow-left">Class</x-button>
    <x-button variant="outline" :href="route('teach.assignments.edit', $assignment)" icon="fa-pen">Edit</x-button>
    @if ($assignment->status === 'draft')
        <form method="POST" action="{{ route('teach.assignments.publish', $assignment) }}">@csrf<x-button variant="accent" icon="fa-paper-plane">Publish to class</x-button></form>
    @else
        <form method="POST" action="{{ route('teach.assignments.close', $assignment) }}">@csrf<x-button variant="outline" :icon="$assignment->status === 'closed' ? 'fa-lock-open' : 'fa-lock'">{{ $assignment->status === 'closed' ? 'Re-open' : 'Close submissions' }}</x-button></form>
    @endif
@endsection

@section('content')
<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        {{-- Details --}}
        <section class="panel p-5">
            <dl class="grid gap-3 text-sm sm:grid-cols-3">
                <div><dt class="text-gray-500">Due</dt><dd class="font-semibold text-gray-800">{{ $assignment->due_at ? $assignment->due_at->format('D j M Y, g:ia') : 'No due date' }}</dd></div>
                @if ($assignment->isQuiz())
                    <div><dt class="text-gray-500">Time limit</dt><dd class="font-semibold text-gray-800">{{ $assignment->time_limit_minutes ? $assignment->time_limit_minutes.' min' : 'None' }}</dd></div>
                    <div><dt class="text-gray-500">Questions</dt><dd class="font-semibold text-gray-800">{{ $assignment->questions->count() }} ({{ (float) $assignment->totalMarks() }} marks)</dd></div>
                    <div><dt class="text-gray-500">Options</dt><dd class="font-semibold text-gray-800">{{ $assignment->shuffle_options ? 'Shuffled' : 'Fixed order' }} · {{ $assignment->block_copy ? 'Copying off' : 'Copying allowed' }}</dd></div>
                @else
                    <div><dt class="text-gray-500">Total marks</dt><dd class="font-semibold text-gray-800">{{ $assignment->max_marks !== null ? (float) $assignment->max_marks : 'Not set' }}</dd></div>
                @endif
            </dl>
            @if ($assignment->instructions)<p class="mt-4 whitespace-pre-line border-t border-gray-100 pt-4 text-sm text-gray-700">{{ $assignment->instructions }}</p>@endif
            @if ($assignment->file_name)<p class="mt-3 text-sm"><a href="{{ route('teach.assignments.handout', $assignment) }}"><i class="fas fa-paperclip mr-1" aria-hidden="true"></i>{{ $assignment->file_name }}</a></p>@endif
        </section>

        {{-- Quiz builder --}}
        @if ($assignment->isQuiz())
            <section class="panel p-5" aria-labelledby="questions-heading">
                <div class="flex items-center justify-between gap-3">
                    <h2 id="questions-heading" class="font-bold text-gray-800">Questions</h2>
                    @if ($locked)<span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600"><i class="fas fa-lock mr-1" aria-hidden="true"></i>Locked: students have taken it</span>@endif
                </div>

                <ol class="mt-3 space-y-3">
                    @forelse ($assignment->questions as $question)
                        @php
                            $revision = $revisions[$question->pivot->question_revision_id] ?? null;
                            $correct = $keys[$question->pivot->question_revision_id]['correct'] ?? null;
                        @endphp
                        <li class="rounded-xl border-[1.5px] border-[#ece9f3] p-3">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm font-semibold text-gray-800">{{ $loop->iteration }}. {{ $revision?->stem }}</p>
                                @unless ($locked)
                                    <form method="POST" action="{{ route('teach.assignments.questions.destroy', [$assignment, $question]) }}">@csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600" title="Remove question"><i class="fas fa-xmark" aria-hidden="true"></i><span class="sr-only">Remove question {{ $loop->iteration }}</span></button>
                                    </form>
                                @endunless
                            </div>
                            <ul class="mt-2 grid gap-1 text-sm sm:grid-cols-2">
                                @foreach ($revision?->payload['options'] ?? [] as $o)
                                    <li class="{{ $o['id'] === $correct ? 'font-semibold text-green-700' : 'text-gray-600' }}">{{ $o['id'] }}. {{ $o['text'] }} @if ($o['id'] === $correct)<i class="fas fa-check" aria-label="correct"></i>@endif</li>
                                @endforeach
                            </ul>
                            <p class="mt-1 text-xs text-gray-400">{{ $question->owner_scope === 'teacher' ? 'Written by you' : 'From the question bank' }}</p>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">No questions yet. Add some from the bank or write your own below.</li>
                    @endforelse
                </ol>

                @unless ($locked)
                    <div class="mt-5 grid gap-4 lg:grid-cols-2">
                        <form method="POST" action="{{ route('teach.assignments.questions.bank', $assignment) }}" class="rounded-xl bg-primary-50 p-4">
                            @csrf
                            <h3 class="font-semibold text-primary-800"><i class="fas fa-database mr-1" aria-hidden="true"></i> Add from the question bank</h3>
                            <div class="mt-3 space-y-3">
                                <select name="subject_id" class="{{ $control }}" required aria-label="Subject">
                                    @include('partials.subject-options', ['levels' => $levels, 'selected' => old('subject_id', $classroom->subject_id)])
                                </select>
                                <div class="grid grid-cols-2 gap-3">
                                    <select name="count" class="{{ $control }}" aria-label="How many questions">
                                        @foreach ([5, 10, 15, 20] as $n)<option value="{{ $n }}">{{ $n }} questions</option>@endforeach
                                    </select>
                                    <select name="difficulty" class="{{ $control }}" aria-label="Difficulty">
                                        <option value="">Mixed difficulty</option>
                                        @foreach (['easy', 'medium', 'hard'] as $d)<option value="{{ $d }}">{{ ucfirst($d) }}</option>@endforeach
                                    </select>
                                </div>
                                <x-button full icon="fa-plus">Add random questions</x-button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('teach.assignments.questions.store', $assignment) }}" class="rounded-xl bg-gold-50 p-4">
                            @csrf
                            <h3 class="font-semibold text-gold-800"><i class="fas fa-pen-to-square mr-1" aria-hidden="true"></i> Write your own question</h3>
                            <div class="mt-3 space-y-2">
                                <select name="subject_id" class="{{ $control }}" required aria-label="Subject">
                                    @include('partials.subject-options', ['levels' => $levels, 'selected' => old('subject_id', $classroom->subject_id)])
                                </select>
                                <textarea name="stem" rows="2" maxlength="2000" required class="{{ $control }}" placeholder="Question" aria-label="Question">{{ old('stem') }}</textarea>
                                @error('stem')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                                @foreach (['A', 'B', 'C', 'D'] as $i => $letter)
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="correct" value="{{ $letter }}" required class="accent-[#28409e]" @checked(old('correct') === $letter) title="Mark {{ $letter }} as the correct answer">
                                        <span class="w-4 text-sm font-bold">{{ $letter }}</span>
                                        <input name="options[]" value="{{ old('options.'.$i) }}" required maxlength="500" class="{{ $control }}" aria-label="Option {{ $letter }}">
                                    </label>
                                @endforeach
                                @error('options.*')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                                <p class="text-xs text-gray-500">Tick the circle beside the correct answer.</p>
                                <textarea name="explanation" rows="2" maxlength="2000" class="{{ $control }}" placeholder="Explanation (optional)" aria-label="Explanation">{{ old('explanation') }}</textarea>
                                <x-button full variant="accent" icon="fa-plus">Add question</x-button>
                            </div>
                        </form>
                    </div>
                @endunless
            </section>
        @endif

        {{-- Submissions --}}
        @if ($assignment->isPublished())
            <section class="panel p-5" aria-labelledby="subs-heading">
                <h2 id="subs-heading" class="font-bold text-gray-800">Submissions ({{ $submissions->filter->isSubmitted()->count() }}/{{ $students->count() }})</h2>
                <div class="mt-3 space-y-3">
                    @forelse ($students as $student)
                        @php $s = $submissions[$student->id] ?? null; @endphp
                        <details class="rounded-xl border-[1.5px] border-[#ece9f3] p-3" @if ($s?->isSubmitted() && ! $s->isGraded() && ! $assignment->isQuiz()) open @endif>
                            <summary class="flex cursor-pointer flex-wrap items-center justify-between gap-2">
                                <span class="font-semibold text-gray-800">{{ $student->name }}</span>
                                <span class="text-sm">
                                    @if (! $s?->isSubmitted())
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">{{ $s?->started_at ? 'Started' : 'Not handed in' }}</span>
                                    @else
                                        @if ($s->is_late)<span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Late</span>@endif
                                        @if ($assignment->isQuiz() && $s->tab_leaves)<span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700" title="Times the student switched away from the quiz page">Left page {{ $s->tab_leaves }}&times;</span>@endif
                                        @if ($s->score !== null)<span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-bold text-green-800">{{ (float) $s->score }}/{{ (float) $s->max_score }}</span>
                                        @else<span class="rounded-full bg-gold-100 px-2 py-0.5 text-xs font-semibold text-gold-800">To mark</span>@endif
                                        <span class="text-xs text-gray-500">{{ $s->submitted_at->format('j M, g:ia') }}</span>
                                    @endif
                                </span>
                            </summary>
                            @if ($s?->isSubmitted())
                                <div class="mt-3 border-t border-gray-100 pt-3 text-sm">
                                    @if ($s->body)<div class="whitespace-pre-line rounded-lg bg-gray-50 p-3 text-gray-700">{{ $s->body }}</div>@endif
                                    @if ($s->file_name)<p class="mt-2"><a href="{{ route('teach.assignments.submission-file', [$assignment, $s]) }}"><i class="fas fa-paperclip mr-1" aria-hidden="true"></i>{{ $s->file_name }}</a></p>@endif
                                    <form method="POST" action="{{ route('teach.assignments.grade', [$assignment, $s]) }}" class="mt-3 grid gap-2 sm:grid-cols-[8rem_1fr_auto] sm:items-start">
                                        @csrf @method('PUT')
                                        @unless ($assignment->isQuiz())
                                            <input type="number" name="score" step="0.5" min="0" @if ($assignment->max_marks) max="{{ (float) $assignment->max_marks }}" @endif value="{{ $s->score !== null ? (float) $s->score : '' }}" class="{{ $control }}" placeholder="Score" aria-label="Score for {{ $student->name }}">
                                        @else
                                            <span class="py-2 text-gray-500">Auto-marked</span>
                                        @endunless
                                        <textarea name="feedback" rows="1" maxlength="3000" class="{{ $control }}" placeholder="Feedback for the student" aria-label="Feedback for {{ $student->name }}">{{ $s->feedback }}</textarea>
                                        <x-button icon="fa-check">Save</x-button>
                                    </form>
                                </div>
                            @endif
                        </details>
                    @empty
                        <p class="text-sm text-gray-500">No students in this class yet.</p>
                    @endforelse
                </div>
            </section>
        @endif
    </div>

    <aside class="space-y-4">
        @if ($assignment->isPublished() && $submissions->filter->isSubmitted()->isNotEmpty())
            @php
                $scored = $submissions->filter(fn ($s) => $s->isSubmitted() && $s->percent() !== null);
            @endphp
            <section class="panel p-5">
                <h2 class="text-sm font-bold text-gray-800">Class results</h2>
                <dl class="mt-3 grid grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-gray-500">Handed in</dt><dd class="text-xl font-extrabold text-primary-700">{{ $submissions->filter->isSubmitted()->count() }}/{{ $students->count() }}</dd></div>
                    <div><dt class="text-gray-500">Average</dt><dd class="text-xl font-extrabold text-primary-700">{{ $scored->isNotEmpty() ? round($scored->avg(fn ($s) => $s->percent())).'%' : '—' }}</dd></div>
                </dl>
            </section>
        @endif
        <form method="POST" action="{{ route('teach.assignments.destroy', $assignment) }}" onsubmit="return confirm('Delete this {{ $assignment->type }} and all submissions?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700"><i class="fas fa-trash mr-1" aria-hidden="true"></i>Delete {{ $assignment->type }}</button>
        </form>
    </aside>
</div>
@endsection
