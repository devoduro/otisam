@extends('layouts.portal')

@php
    use App\Models\Classroom\TeacherMaterial as M;
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm';
    $icons = ['lesson_plan' => 'fa-clipboard-list', 'lesson_note' => 'fa-book', 'scheme_of_work' => 'fa-calendar-week', 'worksheet' => 'fa-file-lines', 'slides' => 'fa-display', 'assessment' => 'fa-file-circle-check', 'other' => 'fa-file'];
@endphp

@section('title', 'Lesson materials')
@section('subtitle', 'Lesson plans, notes, schemes of work and worksheets. Keep them private or share them with other teachers.')

@section('actions')
    <x-button :href="route('teach.materials.create')" icon="fa-upload">Upload material</x-button>
@endsection

@section('content')
<nav class="mb-4 flex gap-2" aria-label="Which materials">
    @foreach (['shared' => 'Shared by teachers', 'mine' => 'My uploads ('.$mineCount.')'] as $value => $label)
        <a href="{{ route('teach.materials.index', ['scope' => $value]) }}" @if ($scope === $value) aria-current="page" @endif
           class="rounded-xl px-4 py-2 text-sm font-semibold no-underline {{ $scope === $value ? 'bg-primary-600 text-white' : 'bg-white text-primary-700 ring-1 ring-[#ece9f3] hover:bg-gold-50' }}">{{ $label }}</a>
    @endforeach
</nav>

<form method="GET" class="panel mb-4 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-5" role="search">
    <input type="hidden" name="scope" value="{{ $scope }}">
    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search title or description" class="{{ $control }} lg:col-span-2" aria-label="Search">
    <select name="kind" class="{{ $control }}" aria-label="Type">
        <option value="">All types</option>
        @foreach (M::KINDS as $k => $label)<option value="{{ $k }}" @selected(($filters['kind'] ?? '') === $k)>{{ $label }}</option>@endforeach
    </select>
    <select name="subject" class="{{ $control }}" aria-label="Subject">
        <option value="">All subjects</option>
        @foreach ($levels as $l)
            <optgroup label="{{ $l->name }}">
                @foreach ($l->subjects as $s)<option value="{{ $s->slug }}" @selected(($filters['subject'] ?? '') === $s->slug)>{{ $s->name }}</option>@endforeach
            </optgroup>
        @endforeach
    </select>
    <div class="flex gap-2">
        <select name="year" class="{{ $control }}" aria-label="Year">
            <option value="">All years</option>
            @foreach ($levels->flatMap->classLevels as $y)<option value="{{ $y->id }}" @selected((int) ($filters['year'] ?? 0) === $y->id)>{{ $y->name }}</option>@endforeach
        </select>
        <x-button icon="fa-filter"><span class="sr-only">Filter</span></x-button>
    </div>
</form>

@if ($materials->isEmpty())
    <div class="panel p-8 text-center text-gray-500">
        <i class="fas fa-folder-open text-4xl text-gray-300" aria-hidden="true"></i>
        <p class="mt-3">{{ $scope === 'mine' ? 'You have not uploaded anything yet.' : 'No shared materials match. Be the first to share a lesson plan!' }}</p>
        <x-button class="mt-4" :href="route('teach.materials.create')" icon="fa-upload">Upload material</x-button>
    </div>
@else
    <ul class="grid gap-3 md:grid-cols-2">
        @foreach ($materials as $m)
            <li class="panel flex gap-4 p-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-xl text-primary-600"><i class="fas {{ $icons[$m->kind] ?? 'fa-file' }}" aria-hidden="true"></i></span>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-800">{{ $m->title }}</p>
                    <p class="text-xs text-gray-500">{{ collect([M::KINDS[$m->kind] ?? null, $m->subject?->labelWithLevel(), $m->classLevel?->name])->filter()->implode(' · ') }}</p>
                    @if ($m->description)<p class="mt-1 line-clamp-2 text-sm text-gray-600">{{ $m->description }}</p>@endif
                    <p class="mt-2 text-xs text-gray-400">
                        @if ($scope === 'mine')
                            <span class="rounded-full px-2 py-0.5 font-semibold {{ $m->isShared() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ ['private' => 'Private', 'teachers' => 'Teachers', 'public' => 'Public'][$m->visibility] }}</span>
                            · {{ $m->download_count }} {{ \Illuminate\Support\Str::plural('download', $m->download_count) }} by others
                        @else
                            By {{ $m->owner?->name }}{{ $m->owner?->teacherProfile?->school_name ? ', '.$m->owner->teacherProfile->school_name : '' }} · {{ $m->created_at->diffForHumans() }}
                        @endif
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('teach.materials.download', $m) }}" class="inline-flex items-center gap-1 rounded-xl bg-gold-50 px-3 py-1.5 text-sm font-bold text-gold-700 no-underline hover:bg-gold-100"><i class="fas fa-download" aria-hidden="true"></i> Download <span class="font-normal text-gold-700/70">({{ number_format($m->file_size / 1048576, 1) }} MB)</span></a>
                        @if ($m->user_id === auth()->id())
                            <a href="{{ route('teach.materials.edit', $m) }}" class="inline-flex items-center gap-1 rounded-xl px-3 py-1.5 text-sm font-semibold no-underline hover:bg-gray-100"><i class="fas fa-pen" aria-hidden="true"></i> Edit</a>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
    <div class="mt-4">{{ $materials->links('pagination::tailwind') }}</div>
@endif
@endsection
