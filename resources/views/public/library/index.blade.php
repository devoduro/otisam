@extends('layouts.app')

@section('title', 'Past questions & study library')
@section('description', 'Past questions, worked solutions, marking guides and notes for JHS and SHS students in Ghana.')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:py-12">
    <h1 class="text-3xl font-extrabold text-gray-800">Library</h1>
    <p class="mt-1 text-gray-500">Past questions, worked solutions and notes. Free items open straight away; premium items show a preview.</p>

    @php $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038] focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30'; @endphp
    <form method="GET" class="mt-6 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]" role="search" data-catalogue-filters>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <label for="q" class="mb-1 block text-[13px] font-bold text-primary-800">Search</label>
                <input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="{{ $control }}" placeholder="e.g. quadratic, osmosis, 2019">
            </div>
            <div>
                <label for="level" class="mb-1 block text-[13px] font-bold text-primary-800">Level</label>
                <select id="level" name="level" class="{{ $control }}" data-resets="class,subject">
                    <option value="">JHS &amp; SHS</option>
                    @foreach ($levels as $l)<option value="{{ strtolower($l->code) }}" @selected($level?->id === $l->id)>{{ $l->name }} ({{ $l->code }})</option>@endforeach
                </select>
            </div>
            <div>
                <label for="class" class="mb-1 block text-[13px] font-bold text-primary-800">Class</label>
                <select id="class" name="class" class="{{ $control }}">
                    <option value="">All classes</option>
                    @foreach (($level ? collect([$level]) : $levels) as $l)
                        @foreach ($l->classLevels as $c)<option value="{{ $c->id }}" @selected((int) ($filters['class'] ?? 0) === $c->id)>{{ $c->name }}</option>@endforeach
                    @endforeach
                </select>
            </div>
            <div>
                <label for="subject" class="mb-1 block text-[13px] font-bold text-primary-800">Subject</label>
                <select id="subject" name="subject" class="{{ $control }}">
                    <option value="">All subjects</option>
                    @foreach (($level ? collect([$level]) : $levels) as $l)
                        <optgroup label="{{ $l->name }}">
                            @foreach ($l->subjects as $s)<option value="{{ $s->slug }}" @selected(($filters['subject'] ?? '') === $s->slug)>{{ $s->name }}</option>@endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="exam" class="mb-1 block text-[13px] font-bold text-primary-800">Exam</label>
                <select id="exam" name="exam" class="{{ $control }}">
                    <option value="">Any exam</option>
                    @foreach ($examTypes as $e)<option value="{{ $e->id }}" @selected((int) ($filters['exam'] ?? 0) === $e->id)>{{ $e->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="year" class="mb-1 block text-[13px] font-bold text-primary-800">Exam year</label>
                <select id="year" name="year" class="{{ $control }}">
                    <option value="">Any year</option>
                    @foreach ($years as $y)<option value="{{ $y }}" @selected((int) ($filters['year'] ?? 0) === $y)>{{ $y }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="category" class="mb-1 block text-[13px] font-bold text-primary-800">Type</label>
                <select id="category" name="category" class="{{ $control }}">
                    <option value="">All types</option>
                    @foreach ($categories as $c)<option value="{{ $c->slug }}" @selected(($filters['category'] ?? '') === $c->slug)>{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="access" class="mb-1 block text-[13px] font-bold text-primary-800">Access</label>
                <select id="access" name="access" class="{{ $control }}">
                    <option value="">Free and premium</option>
                    <option value="free" @selected(($filters['access'] ?? '') === 'free')>Free only</option>
                    <option value="premium" @selected(($filters['access'] ?? '') === 'premium')>Premium only</option>
                </select>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <x-button icon="fa-filter">Show results</x-button>
            @if (array_filter($filters))<a href="{{ route('library.index') }}" class="rounded-xl px-3 py-2 text-sm font-semibold no-underline hover:bg-gray-100"><i class="fas fa-xmark mr-1" aria-hidden="true"></i>Clear all</a>@endif
        </div>
    </form>
    @include('public.partials.auto-filter-script')

    <p class="mt-6 text-sm text-gray-500" role="status">{{ $resources->total() }} {{ \Illuminate\Support\Str::plural('item', $resources->total()) }}</p>

    @if ($resources->isEmpty())
        <p class="mt-3 rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3] p-6 text-gray-500">Nothing matches yet. Try fewer filters, or check back soon. New material is being added.</p>
    @else
        <ul class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($resources as $resource)
                @include('public.library._card')
            @endforeach
        </ul>
        <div class="mt-6">{{ $resources->links('pagination::tailwind') }}</div>
    @endif
</div>
@endsection
