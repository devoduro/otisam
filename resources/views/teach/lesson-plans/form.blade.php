@extends('layouts.portal')

@section('title', $plan->exists ? 'Edit lesson plan' : 'New lesson plan')
@section('subtitle', 'Only the title, subject and main activities are required. Write in your own words; do not paste text from other sites or official documents.')

@section('content')
@php
    use App\Models\LessonPlans\LessonPlan;
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]';
    $label = 'mb-1.5 block text-[13.5px] font-bold text-primary-800';
    $optional = '<span class="font-normal text-gray-500">(optional)</span>';
    $shared = in_array(old('share', in_array($plan->status, ['pending', 'published'], true) ? '1' : ''), ['1', 1, true], true);
    $area = function (string $name, string $title, int $rows, string $placeholder = '', bool $required = false, ?string $hint = null) use ($plan, $control, $label, $optional) {
        return view('teach.lesson-plans.textarea', compact('name', 'title', 'rows', 'placeholder', 'required', 'hint', 'plan', 'control', 'label', 'optional'))->render();
    };
@endphp

@if ($plan->review_note)
    <p class="mb-4 max-w-4xl rounded-xl bg-red-50 p-3 text-sm text-red-700" role="status"><i class="fas fa-circle-exclamation mr-1" aria-hidden="true"></i>Reviewer's note: {{ $plan->review_note }}</p>
@endif

<form method="POST" action="{{ $plan->exists ? route('teach.lesson-plans.update', $plan) : route('teach.lesson-plans.store') }}" class="max-w-4xl space-y-6">
    @csrf
    @if ($plan->exists) @method('PUT') @endif

    <section class="panel space-y-5 p-6">
        <h2 class="text-base font-bold text-gray-800">1. Class and week</h2>
        <x-input name="title" label="Title" required maxlength="200" :value="$plan->title" placeholder="e.g. Adding and subtracting fractions with unlike denominators" />

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="subject_id" class="{{ $label }}">Subject</label>
                <select id="subject_id" name="subject_id" required class="{{ $control }}">
                    <option value="">Choose…</option>
                    @include('partials.subject-options', ['levels' => $levels, 'selected' => old('subject_id', $plan->subject_id)])
                </select>
                @error('subject_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="class_level_id" class="{{ $label }}">Year {!! $optional !!}</label>
                <select id="class_level_id" name="class_level_id" class="{{ $control }}">
                    <option value="">Any year</option>
                    @foreach ($levels as $l)
                        <optgroup label="{{ $l->name }}">
                            @foreach ($l->classLevels as $y)<option value="{{ $y->id }}" @selected((int) old('class_level_id', $plan->class_level_id) === $y->id)>{{ $y->name }}</option>@endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('class_level_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-5">
            <x-input name="term" type="number" label="Term" min="1" max="3" :value="$plan->term" />
            <x-input name="week" type="number" label="Week" min="1" max="40" :value="$plan->week" />
            <x-input name="lessons_per_week" type="number" label="Lessons" min="1" max="10" :value="$plan->lessons_per_week" hint="this week" />
            <x-input name="duration_minutes" type="number" label="Minutes" min="10" max="300" :value="$plan->duration_minutes" hint="per lesson" />
            <x-input name="class_size" type="number" label="Class size" min="1" max="300" :value="$plan->class_size" />
        </div>
    </section>

    <section class="panel space-y-5 p-6">
        <h2 class="text-base font-bold text-gray-800">2. Curriculum focus</h2>
        <div>
            <label for="curriculum_node_id" class="{{ $label }}">Topic from the curriculum {!! $optional !!}</label>
            <select id="curriculum_node_id" name="curriculum_node_id" class="{{ $control }}" data-selected="{{ old('curriculum_node_id', $plan->curriculum_node_id) }}">
                <option value="">None</option>
            </select>
            <p class="mt-1 text-xs text-gray-500">Links the plan to the subject's topic page and lets teachers filter by topic.</p>
            @error('curriculum_node_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <x-input name="strand" label="Strand" maxlength="200" :value="$plan->strand" placeholder="e.g. Number" />
            <x-input name="sub_strand" label="Sub-strand" maxlength="200" :value="$plan->sub_strand" placeholder="e.g. Fractions, decimals and percentages" />
        </div>
        {!! $area('content_standard', 'Content standard', 2, 'What learners should know and be able to do by the end of this unit') !!}
        {!! $area('indicators', 'Learning indicators', 3, 'One per line', false, 'One per line. What learners will show they can do this week.') !!}
        <div class="grid gap-5 sm:grid-cols-2">
            {!! $area('core_competencies', 'Core competencies', 3, 'e.g. Critical thinking', false, 'One per line.') !!}
            {!! $area('resources', 'Resources', 3, 'e.g. Fraction strips cut from card', false, 'One per line.') !!}
        </div>
        <x-input name="key_vocabulary" label="Key words" maxlength="500" :value="$plan->key_vocabulary" placeholder="Separate with commas" />
    </section>

    <section class="panel space-y-5 p-6">
        <h2 class="text-base font-bold text-gray-800">3. Lesson flow</h2>
        @foreach (LessonPlan::PHASES as $field => [$title, $hint])
            {!! $area($field, $title, $field === 'main_activities' ? 8 : 4, '', $field === 'main_activities', $hint) !!}
        @endforeach
    </section>

    <section class="panel space-y-5 p-6">
        <h2 class="text-base font-bold text-gray-800">4. Assessment and follow-up</h2>
        {!! $area('assessment', 'Assessment', 4, 'Questions or tasks that show whether learners met the indicators') !!}
        {!! $area('differentiation', 'Differentiation', 3, 'Support for learners who struggle; extension for fast finishers') !!}
        <div class="grid gap-5 sm:grid-cols-2">
            {!! $area('homework', 'Homework', 3) !!}
            {!! $area('teacher_notes', 'Teacher notes', 3, 'Reminders, what to change next time') !!}
        </div>
    </section>

    <section class="panel p-6">
        <label class="flex items-start gap-3">
            <input type="hidden" name="share" value="0">
            <input type="checkbox" name="share" value="1" @checked($shared) class="mt-1 h-5 w-5 rounded border-gray-300">
            <span>
                <span class="block font-semibold text-gray-800">Share in Lesson plans</span>
                <span class="block text-sm text-gray-500">{{ $canPublish ? 'Other teachers can read, print and copy it straight away.' : 'An administrator checks it first. Editing a shared plan sends it for review again.' }} Leave unticked to keep it private.</span>
            </span>
        </label>
    </section>

    <div class="flex flex-wrap gap-3">
        <x-button icon="fa-floppy-disk">Save lesson plan</x-button>
        <x-button variant="outline" :href="route('teach.lesson-plans.index')">Cancel</x-button>
    </div>
</form>

@if ($plan->exists)
    <form method="POST" action="{{ route('teach.lesson-plans.destroy', $plan) }}" class="mt-4" onsubmit="return confirm('Delete this lesson plan?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700"><i class="fas fa-trash mr-1" aria-hidden="true"></i>Delete</button>
    </form>
@endif
@endsection

@push('scripts')
<script>
    // The topic list follows the chosen subject.
    (function () {
        var topics = @json($topics);
        var subject = document.getElementById('subject_id');
        var topic = document.getElementById('curriculum_node_id');
        function fill() {
            var keep = topic.value || topic.dataset.selected;
            topic.length = 1;
            (topics[subject.value] || []).forEach(function (t) {
                var o = new Option(t.label, t.id);
                if (String(t.id) === String(keep)) { o.selected = true; }
                topic.add(o);
            });
            topic.options[0].text = (topics[subject.value] || []).length ? 'None' : (subject.value ? 'No topics for this subject yet' : 'Choose a subject first');
            topic.dataset.selected = '';
        }
        subject.addEventListener('change', fill);
        fill();
    })();
</script>
@endpush
