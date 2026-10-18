@extends('layouts.app')

@section('title', $teacher->name)
@section('description', trim($teacher->name.', '.($profile->headline ?: 'teacher').($profile->school_name ? ' at '.$profile->school_name : '').'. Video tutorials and lesson materials on '.config('app.name').'.'))

@php use App\Models\Classroom\TeacherMaterial as M; @endphp

@section('content')
<section class="relative overflow-hidden bg-primary-700 bg-crosshatch text-white">
    <div class="pointer-events-none absolute -right-16 -top-20 h-72 w-72 rounded-full bg-gold-500/15 blur-3xl"></div>
    <div class="relative mx-auto max-w-6xl px-4 py-10">
        <a href="{{ route('teachers.index') }}" class="text-sm font-semibold text-white/70 no-underline hover:text-white"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i> All teachers</a>
        <div class="mt-5 flex flex-col gap-6 sm:flex-row sm:items-center">
            @if ($profile->photoUrl())
                <img src="{{ $profile->photoUrl() }}" alt="{{ $teacher->name }}" class="h-28 w-28 rounded-full object-cover ring-4 ring-gold-400">
            @else
                <span class="flex h-28 w-28 items-center justify-center rounded-full bg-gold-500 font-display text-4xl font-extrabold text-primary-950 ring-4 ring-white/20">{{ $profile->initials() }}</span>
            @endif
            <div class="min-w-0">
                <h1 class="text-3xl font-extrabold sm:text-4xl">{{ $teacher->name }} <i class="fas fa-circle-check text-2xl text-gold-400" title="Verified teacher" aria-label="Verified teacher"></i></h1>
                @if ($profile->headline)<p class="mt-1 text-lg text-white/80">{{ $profile->headline }}</p>@endif
                <div class="mt-3 flex flex-wrap gap-2 text-sm">
                    @if ($profile->school_name)<span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15"><i class="fas fa-school mr-1.5 text-gold-400" aria-hidden="true"></i>{{ $profile->school_name }}</span>@endif
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15"><i class="fas fa-location-dot mr-1.5 text-gold-400" aria-hidden="true"></i>{{ collect([$profile->district, $profile->region])->filter()->implode(', ') ?: 'Ghana' }}</span>
                    @if ($profile->years_teaching)<span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15"><i class="fas fa-award mr-1.5 text-gold-400" aria-hidden="true"></i>{{ $profile->years_teaching }} {{ \Illuminate\Support\Str::plural('year', $profile->years_teaching) }} teaching</span>@endif
                </div>
            </div>
        </div>
    </div>
</section>

<div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        @if ($profile->bio)
            <section class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-[#ece9f3]">
                <h2 class="text-lg font-extrabold text-primary-700">About</h2>
                <p class="mt-2 whitespace-pre-line text-gray-700">{{ $profile->bio }}</p>
            </section>
        @endif

        <section aria-labelledby="videos-heading">
            <h2 id="videos-heading" class="text-lg font-extrabold text-primary-700">Video tutorials ({{ $videos->count() }})</h2>
            @forelse ($videos as $video)
                @if ($loop->first)<ul class="mt-3 grid gap-4 sm:grid-cols-2">@endif
                <li>
                    <a href="{{ route('videos.show', $video) }}" class="group block overflow-hidden rounded-2xl bg-white no-underline shadow-card ring-1 ring-[#ece9f3] transition hover:-translate-y-0.5">
                        <span class="relative block aspect-video bg-slate-900">
                            <img src="{{ $video->thumbnailUrl() }}" alt="" loading="lazy" class="h-full w-full object-cover opacity-90">
                            <span class="absolute inset-0 flex items-center justify-center"><span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/90 text-primary-600 shadow"><i class="fas fa-play" aria-hidden="true"></i></span></span>
                        </span>
                        <span class="block p-3"><span class="block font-semibold text-gray-800">{{ $video->title }}</span><span class="text-xs text-gray-500">{{ $video->subject?->name }}</span></span>
                    </a>
                </li>
                @if ($loop->last)</ul>@endif
            @empty
                <p class="mt-2 rounded-2xl bg-white p-5 text-sm text-gray-500 shadow-card ring-1 ring-[#ece9f3]">No video tutorials yet.</p>
            @endforelse
        </section>

        <section aria-labelledby="materials-heading">
            <h2 id="materials-heading" class="text-lg font-extrabold text-primary-700">Lesson materials ({{ $materials->count() }})</h2>
            @forelse ($materials as $m)
                @if ($loop->first)<ul class="mt-3 space-y-2">@endif
                <li class="flex items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]">
                    <span class="min-w-0">
                        <span class="block truncate font-semibold text-gray-800">{{ $m->title }}</span>
                        <span class="text-xs text-gray-500">{{ collect([M::KINDS[$m->kind] ?? null, $m->subject?->name, $m->classLevel?->name])->filter()->implode(' · ') }}</span>
                    </span>
                    <a href="{{ route('downloads.material', $m) }}" class="shrink-0 rounded-xl bg-gold-50 px-3 py-1.5 text-sm font-bold text-gold-700 no-underline hover:bg-gold-100"><i class="fas fa-download mr-1" aria-hidden="true"></i>Download</a>
                </li>
                @if ($loop->last)</ul>@endif
            @empty
                <p class="mt-2 rounded-2xl bg-white p-5 text-sm text-gray-500 shadow-card ring-1 ring-[#ece9f3]">No public materials yet.</p>
            @endforelse
        </section>
    </div>

    <aside class="space-y-6">
        @if ($profile->subjectList())
            <section class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
                <h2 class="text-sm font-bold text-gray-800">Subjects</h2>
                <div class="mt-3 flex flex-wrap gap-1.5">
                    @foreach ($profile->subjectList() as $s)<a href="{{ route('teachers.index', ['subject' => $s]) }}" class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700 no-underline">{{ $s }}</a>@endforeach
                </div>
            </section>
        @endif

        @if ($colleagues->isNotEmpty())
            <section class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
                <h2 class="text-sm font-bold text-gray-800">Also at {{ $profile->school_name }}</h2>
                <ul class="mt-3 space-y-2">
                    @foreach ($colleagues as $c)
                        <li><a href="{{ route('teachers.show', $c->slug) }}" class="flex items-center gap-3 no-underline">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">{{ $c->initials() }}</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $c->user->name }}</span>
                        </a></li>
                    @endforeach
                </ul>
            </section>
        @endif

        @guest
            <section class="rounded-2xl bg-gold-50 p-5 ring-1 ring-gold-200">
                <h2 class="font-bold text-gold-900">Are you a teacher?</h2>
                <p class="mt-1 text-sm text-gold-900/80">Create your free profile, share your lessons and set quizzes for your classes.</p>
                <x-button class="mt-3" variant="accent" :href="route('teachers.register')" icon="fa-user-plus">Join as a teacher</x-button>
            </section>
        @endguest
    </aside>
</div>
@endsection
