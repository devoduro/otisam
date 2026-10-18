@extends('layouts.portal')

@section('title', $classroom->exists ? 'Edit class' : 'New class')

@section('content')
<form method="POST" action="{{ $classroom->exists ? route('teach.classes.update', $classroom) : route('teach.classes.store') }}" class="panel max-w-2xl space-y-5 p-6">
    @csrf
    @if ($classroom->exists) @method('PUT') @endif

    <x-input name="name" label="Class name" required maxlength="120" :value="$classroom->name" placeholder="e.g. SHS 2 Science A, Core Maths" />

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="subject_id" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Subject <span class="font-normal text-gray-500">(optional)</span></label>
            <select id="subject_id" name="subject_id" class="block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]">
                <option value="">No particular subject</option>
                @include('partials.subject-options', ['levels' => $levels, 'selected' => old('subject_id', $classroom->subject_id)])
            </select>
            @error('subject_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="class_level_id" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Year <span class="font-normal text-gray-500">(optional)</span></label>
            <select id="class_level_id" name="class_level_id" class="block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]">
                <option value="">Any</option>
                @foreach ($levels as $l)
                    <optgroup label="{{ $l->name }}">
                        @foreach ($l->classLevels as $y)<option value="{{ $y->id }}" @selected((int) old('class_level_id', $classroom->class_level_id) === $y->id)>{{ $y->name }}</option>@endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label for="description" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Note for students <span class="font-normal text-gray-500">(optional)</span></label>
        <textarea id="description" name="description" rows="3" maxlength="500" class="block w-full rounded-xl border-[1.5px] border-[#e2e0ea] px-3.5 py-3 text-[15px]">{{ old('description', $classroom->description) }}</textarea>
    </div>

    <label class="flex items-center gap-3 text-sm font-semibold text-gray-700">
        <input type="hidden" name="joining_open" value="0">
        <input type="checkbox" name="joining_open" value="1" class="h-4 w-4 accent-[#28409e]" @checked(old('joining_open', $classroom->joining_open))>
        Students can join with the class code
    </label>

    <div class="flex gap-3">
        <x-button icon="fa-save">{{ $classroom->exists ? 'Save' : 'Create class' }}</x-button>
        <x-button variant="outline" :href="$classroom->exists ? route('teach.classes.show', $classroom) : route('teach.classes.index')">Cancel</x-button>
    </div>
</form>
@endsection
