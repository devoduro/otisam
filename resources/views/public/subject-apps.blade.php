@extends('layouts.app')

@section('title', 'Subject apps for teachers')
@section('description', 'Free AI lesson-planning apps for JHS and SHS teachers in Ghana, one per year and subject, made by Subject Specific App.')

@section('content')
@php
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038] focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30';
    $active = collect($filters)->filter(fn ($v) => filled($v))->isNotEmpty();
@endphp
<div class="mx-auto max-w-7xl px-4 py-8 md:px-6 sm:py-12">
    <h1 class="text-3xl font-extrabold text-gray-800">Subject apps for teachers</h1>
    <p class="mt-1 max-w-3xl text-gray-500">Free AI assistants that help you plan a week's lessons, activities and assessment for your subject and year. Choose your class and open the app.</p>

    <p class="mt-4 max-w-3xl rounded-xl bg-amber-50 p-3 text-sm text-amber-900" role="note">
        <i class="fas fa-circle-info mr-1" aria-hidden="true"></i>
        These apps are made and run by <a href="{{ \App\Support\SubjectApps::SOURCE }}" target="_blank" rel="noopener noreferrer" class="font-semibold">Subject Specific App</a> on Playlab, not by {{ config('app.name') }}. They open on another site, which has its own terms and privacy policy. Do not enter learners' personal details.
    </p>

    {{-- Quick picker: level/year, then subject, then open. --}}
    <section aria-labelledby="picker-title" class="mt-6 rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
        <h2 id="picker-title" class="font-bold text-gray-800">Find your app</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_2fr_auto] sm:items-end">
            <div>
                <label for="app-class" class="mb-1 block text-[13px] font-bold text-primary-800">Class</label>
                <select id="app-class" class="{{ $control }}">
                    <option value="">Choose…</option>
                    @foreach (array_keys($picker) as $class)<option value="{{ $class }}">{{ $class === 'JHS' ? 'JHS 1–3' : $class }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="app-subject" class="mb-1 block text-[13px] font-bold text-primary-800">Subject</label>
                <select id="app-subject" class="{{ $control }}" disabled><option value="">Choose a class first</option></select>
            </div>
            <a id="app-open" href="#" target="_blank" rel="noopener noreferrer" aria-disabled="true"
               class="pointer-events-none inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-[14.5px] font-bold text-white no-underline opacity-50 hover:bg-primary-700 hover:text-white">
                Open app <i class="fas fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    <form method="GET" action="{{ route('subject-apps.index') }}" role="search" class="mt-8 flex flex-wrap items-end gap-3">
        <div class="min-w-[12rem] flex-1">
            <label for="f-q" class="mb-1 block text-[13px] font-bold text-primary-800">Search subjects</label>
            <input id="f-q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="e.g. Chemistry" class="{{ $control }}">
        </div>
        <div>
            <label for="f-level" class="mb-1 block text-[13px] font-bold text-primary-800">Level</label>
            <select id="f-level" name="level" class="{{ $control }}">
                <option value="">JHS &amp; SHS</option>
                <option value="jhs" @selected(($filters['level'] ?? '') === 'jhs')>JHS</option>
                <option value="shs" @selected(($filters['level'] ?? '') === 'shs')>SHS</option>
            </select>
        </div>
        <div>
            <label for="f-year" class="mb-1 block text-[13px] font-bold text-primary-800">Year</label>
            <select id="f-year" name="year" class="{{ $control }}">
                <option value="">All years</option>
                @foreach ([1, 2, 3] as $y)<option value="{{ $y }}" @selected((int) ($filters['year'] ?? 0) === $y)>Year {{ $y }}</option>@endforeach
            </select>
        </div>
        <x-button icon="fa-filter">Filter</x-button>
        @if ($active)<a href="{{ route('subject-apps.index') }}" class="rounded-xl px-3 py-2 text-sm font-semibold no-underline hover:bg-gray-100"><i class="fas fa-xmark mr-1" aria-hidden="true"></i>Clear</a>@endif
    </form>
    <p class="mt-3 text-sm text-gray-500" aria-live="polite">{{ $total }} {{ \Illuminate\Support\Str::plural('app', $total) }}</p>

    @forelse ($groups as $group => $apps)
        <h2 class="mt-8 text-lg font-bold text-gray-800">{{ $group }}</h2>
        <ul class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($apps as $app)
                <li>
                    <a href="{{ $app['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-between gap-3 rounded-xl bg-white px-4 py-3 no-underline shadow-card ring-1 ring-[#ece9f3] hover:ring-primary-200">
                        <span>
                            <span class="block font-semibold text-gray-800">{{ $app['subject'] }}</span>
                            @if ($app['shared'])<span class="block text-xs text-gray-500">Shared app for the technical subjects</span>@endif
                        </span>
                        <i class="fas fa-arrow-up-right-from-square text-xs text-gray-400" aria-hidden="true"></i>
                        <span class="sr-only">(opens on Playlab in a new tab)</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @empty
        <div class="mt-6 rounded-2xl bg-white p-8 text-center text-gray-500 shadow-card ring-1 ring-[#ece9f3]">No app matches. Try another subject name.</div>
    @endforelse

    <p class="mt-10 text-sm text-gray-500">Prefer to write your own? <a href="{{ route('lesson-plans.index') }}">Browse {{ config('app.name') }} lesson plans</a>@auth @if (auth()->user()->isTeacher()) or <a href="{{ route('teach.lesson-plans.create') }}">write a lesson plan</a>@endif @endauth.</p>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var apps = @json($picker);
        var cls = document.getElementById('app-class');
        var subject = document.getElementById('app-subject');
        var open = document.getElementById('app-open');
        function setOpen(url) {
            open.href = url || '#';
            open.setAttribute('aria-disabled', url ? 'false' : 'true');
            open.classList.toggle('pointer-events-none', !url);
            open.classList.toggle('opacity-50', !url);
        }
        cls.addEventListener('change', function () {
            subject.length = 0;
            var list = apps[cls.value] || {};
            subject.add(new Option(cls.value ? 'Choose a subject…' : 'Choose a class first', ''));
            Object.keys(list).forEach(function (name) { subject.add(new Option(name, list[name])); });
            subject.disabled = !cls.value;
            setOpen(null);
        });
        subject.addEventListener('change', function () { setOpen(subject.value); });
    })();
</script>
@endpush
