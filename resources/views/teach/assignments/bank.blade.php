@extends('layouts.portal')

@section('title', 'Choose questions: '.$assignment->title)
@section('subtitle', 'Tick the questions you want from the question bank. The quiz has '.$quizCount.' of '.\App\Services\Classroom\AssignmentService::MAX_QUIZ_QUESTIONS.' questions.')

@section('actions')
    <x-button variant="outline" :href="route('teach.assignments.show', $assignment)" icon="fa-arrow-left">Back to quiz</x-button>
@endsection

@section('content')
@php $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]'; @endphp

@if ($locked)
    <p class="panel mb-4 p-4 text-sm text-gray-600" role="status"><i class="fas fa-lock mr-1" aria-hidden="true"></i> Students have already taken this quiz, so no more questions can be added.</p>
@endif

{{-- Filters --}}
<form method="GET" action="{{ route('teach.assignments.bank', $assignment) }}" class="panel grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end" data-bank-filters>
    <div>
        <label for="level" class="mb-1 block text-xs font-bold text-primary-800">Level</label>
        <select id="level" name="level" class="{{ $control }}" data-autosubmit="year subject_id topic_id">
            @foreach ($levels as $l)<option value="{{ strtolower($l->code) }}" @selected($level?->id === $l->id)>{{ $l->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label for="year" class="mb-1 block text-xs font-bold text-primary-800">Class / year</label>
        <select id="year" name="year" class="{{ $control }}" data-autosubmit="topic_id">
            <option value="">All years</option>
            @foreach ($level?->classLevels ?? [] as $y)<option value="{{ $y->id }}" @selected($year?->id === $y->id)>{{ $y->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label for="subject_id" class="mb-1 block text-xs font-bold text-primary-800">Subject</label>
        <select id="subject_id" name="subject_id" class="{{ $control }}" data-autosubmit="topic_id">
            <option value="">Choose a subject</option>
            @foreach ($level?->subjects ?? [] as $s)<option value="{{ $s->id }}" @selected($subject?->id === $s->id)>{{ $s->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label for="topic_id" class="mb-1 block text-xs font-bold text-primary-800">Topic</label>
        <select id="topic_id" name="topic_id" class="{{ $control }}" @disabled(! $subject)>
            <option value="">All topics</option>
            @foreach ($topics as $t)<option value="{{ $t['id'] }}" @selected($topic?->id === $t['id'])>{{ $t['title'] }}{{ $t['year'] ? ' ('.$t['year'].')' : '' }} · {{ $t['count'] }}</option>@endforeach
        </select>
    </div>
    <div>
        <label for="difficulty" class="mb-1 block text-xs font-bold text-primary-800">Difficulty</label>
        <select id="difficulty" name="difficulty" class="{{ $control }}">
            <option value="">Any</option>
            @foreach (['easy', 'medium', 'hard'] as $d)<option value="{{ $d }}" @selected(($filters['difficulty'] ?? '') === $d)>{{ ucfirst($d) }}</option>@endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label for="q" class="mb-1 block text-xs font-bold text-primary-800">Words in the question</label>
        <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" class="{{ $control }}" placeholder="e.g. fraction, photosynthesis">
    </div>
    <x-button icon="fa-filter">Show questions</x-button>
</form>

@if (! $subject)
    <p class="panel mt-4 p-6 text-center text-sm text-gray-500">Choose a subject to see its questions.</p>
@else
    <form method="POST" action="{{ route('teach.assignments.questions.selected', $assignment) }}" class="mt-4" data-pick-form>
        @csrf
        @error('question_ids')<p class="mb-3 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror

        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-gray-600">{{ $questions->total() }} question(s) in {{ $subject->name }}{{ $year ? ' · '.$year->name : '' }}{{ $topic ? ' · '.$topic->title : '' }}. Correct answers are shown in green. You can change any question after adding it.</p>
            @unless ($locked)
                <div class="flex items-center gap-2">
                    <button type="button" class="text-sm font-semibold text-primary-700" data-select-page>Tick all on this page</button>
                    <x-button icon="fa-plus" data-add-button>Add selected (<span data-selected-count>0</span>)</x-button>
                </div>
            @endunless
        </div>

        <ol class="space-y-3">
            @forelse ($questions as $question)
                @php
                    $revision = $question->currentRevision;
                    $correct = $keys[$question->current_revision_id]['correct'] ?? null;
                    $added = isset($inQuiz[$question->id]);
                @endphp
                <li class="panel p-4 {{ $added ? 'opacity-60' : '' }}">
                    <label class="flex cursor-pointer items-start gap-3">
                        @if ($added)
                            <span class="mt-0.5 rounded-full bg-green-100 px-2 py-0.5 text-xs font-bold text-green-800">In quiz</span>
                        @else
                            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" class="mt-1 h-4 w-4 accent-[#28409e]" @disabled($locked) @checked(in_array($question->id, old('question_ids', [])))>
                        @endif
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-gray-800">{{ $revision?->stem }}</span>
                            <span class="mt-2 grid gap-1 text-sm sm:grid-cols-2">
                                @foreach ($revision?->payload['options'] ?? [] as $o)
                                    <span class="{{ $o['id'] === $correct ? 'font-semibold text-green-700' : 'text-gray-600' }}">{{ $o['id'] }}. {{ $o['text'] }}@if ($o['id'] === $correct) <i class="fas fa-check" aria-label="correct"></i>@endif</span>
                                @endforeach
                            </span>
                            <span class="mt-1 block text-xs text-gray-400">{{ ['', 'Easy', 'Easy', 'Medium', 'Hard', 'Hard'][$question->difficulty] ?? '' }} · {{ $question->classLevel?->name ?? 'Any year' }}</span>
                        </span>
                    </label>
                </li>
            @empty
                <li class="panel p-6 text-center text-sm text-gray-500">No questions match. Try another topic or fewer words.</li>
            @endforelse
        </ol>
    </form>
    <div class="mt-4">{{ $questions->links('pagination::tailwind') }}</div>
@endif
@endsection

@push('scripts')
<script>
(function () {
    // Changing the level, year or subject reloads the page so the lists below it match; the
    // choices that depend on it are cleared.
    var filters = document.querySelector('[data-bank-filters]');
    filters?.querySelectorAll('[data-autosubmit]').forEach(function (select) {
        select.addEventListener('change', function () {
            select.dataset.autosubmit.split(' ').forEach(function (name) {
                var field = filters.querySelector('[name="' + name + '"]');
                if (field) { field.value = ''; }
            });
            filters.submit();
        });
    });

    var form = document.querySelector('[data-pick-form]');
    if (!form) { return; }
    var count = form.querySelector('[data-selected-count]');
    function refresh() { if (count) { count.textContent = form.querySelectorAll('input[name="question_ids[]"]:checked').length; } }
    form.addEventListener('change', refresh);
    form.querySelector('[data-select-page]')?.addEventListener('click', function () {
        var boxes = form.querySelectorAll('input[name="question_ids[]"]:not(:disabled)');
        var all = Array.prototype.every.call(boxes, function (b) { return b.checked; });
        boxes.forEach(function (b) { b.checked = !all; });
        this.textContent = all ? 'Tick all on this page' : 'Untick all';
        refresh();
    });
    refresh();
})();
</script>
@endpush
