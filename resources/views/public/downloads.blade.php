@extends('layouts.app')

@section('title', 'Downloads')
@section('description', 'Download past questions, solutions and notes as PDF and Office files.')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 md:px-6 sm:py-12">
    <h1 class="text-3xl font-extrabold text-gray-800">Downloads</h1>
    <p class="mt-1 text-gray-500">Save documents to read offline. Free items need a free account; premium items need premium access.</p>

    @if ($mine->isNotEmpty())
        <section class="mt-6 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]" aria-labelledby="mine-heading">
            <h2 id="mine-heading" class="text-sm font-semibold text-gray-700"><i class="fas fa-clock-rotate-left mr-1 text-gold-500" aria-hidden="true"></i> My downloads</h2>
            <ul class="mt-2 divide-y divide-gray-100">
                @foreach ($mine as $row)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2 text-sm">
                        <a href="{{ route('library.show', $row['resource']) }}" class="font-medium">{{ $row['resource']->title }}</a>
                        <span class="flex items-center gap-3 text-xs text-gray-500">
                            {{ $row['at']->diffForHumans() }}
                            @if ($canDownload($row['resource']))
                                <a href="{{ route('library.download', $row['resource']) }}" class="font-semibold"><i class="fas fa-download" aria-hidden="true"></i> Again</a>
                            @else
                                <span title="Your access has changed"><i class="fas fa-lock" aria-hidden="true"></i> Locked</span>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @php $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038] focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30'; @endphp
    <nav class="mt-6 flex flex-wrap gap-2" aria-label="Download source">
        @foreach (['library' => ['fa-book-open', 'Library documents', $resources->total()], 'teachers' => ['fa-chalkboard-user', 'From teachers', $teacherMaterials->total()]] as $value => [$icon, $label, $n])
            <a href="{{ route('downloads.index', array_filter(['source' => $value === 'library' ? null : $value, 'level' => $filters['level'] ?? null, 'subject' => $filters['subject'] ?? null])) }}" @if ($source === $value) aria-current="page" @endif
               class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold no-underline {{ $source === $value ? 'bg-primary-600 text-white shadow-navy' : 'bg-white text-primary-700 ring-1 ring-[#ece9f3] hover:bg-gold-50' }}">
                <i class="fas {{ $icon }}" aria-hidden="true"></i>{{ $label }} <span class="rounded-full px-1.5 text-xs {{ $source === $value ? 'bg-white/20' : 'bg-gray-100' }}">{{ $n }}</span>
            </a>
        @endforeach
    </nav>

    <form method="GET" class="mt-3 grid gap-3 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3] sm:grid-cols-2 lg:grid-cols-5" role="search" data-catalogue-filters>
        @if ($source !== 'library')<input type="hidden" name="source" value="{{ $source }}">@endif
        <div class="sm:col-span-2">
            <label for="q" class="mb-1 block text-[13px] font-bold text-primary-800">Search</label>
            <input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="{{ $control }}" placeholder="Title">
        </div>
        <div>
            <label for="level" class="mb-1 block text-[13px] font-bold text-primary-800">Level</label>
            <select id="level" name="level" class="{{ $control }}" data-resets="subject">
                <option value="">JHS &amp; SHS</option>
                @foreach ($levels as $l)<option value="{{ strtolower($l->code) }}" @selected($level?->id === $l->id)>{{ $l->name }}</option>@endforeach
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
            @if ($source === 'teachers')
                <label for="kind" class="mb-1 block text-[13px] font-bold text-primary-800">Type</label>
                <select id="kind" name="kind" class="{{ $control }}">
                    <option value="">All types</option>
                    @foreach (\App\Models\Classroom\TeacherMaterial::KINDS as $k => $label)<option value="{{ $k }}" @selected(($filters['kind'] ?? '') === $k)>{{ $label }}</option>@endforeach
                </select>
            @else
                <label for="category" class="mb-1 block text-[13px] font-bold text-primary-800">Type</label>
                <select id="category" name="category" class="{{ $control }}">
                    <option value="">All types</option>
                    @foreach ($categories as $c)<option value="{{ $c->slug }}" @selected(($filters['category'] ?? '') === $c->slug)>{{ $c->name }}</option>@endforeach
                </select>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:col-span-2 lg:col-span-5">
            <x-button icon="fa-filter">Show downloads</x-button>
            @if (collect($filters)->except('source')->filter()->isNotEmpty())<a href="{{ route('downloads.index', $source === 'library' ? [] : ['source' => $source]) }}" class="rounded-xl px-3 py-2 text-sm font-semibold no-underline hover:bg-gray-100"><i class="fas fa-xmark mr-1" aria-hidden="true"></i>Clear all</a>@endif
        </div>
    </form>
    @include('public.partials.auto-filter-script')

    @if ($source === 'teachers')
        <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3]">
            @if ($teacherMaterials->isEmpty())
                <p class="p-8 text-center text-gray-500"><i class="fas fa-chalkboard-user mb-2 block text-3xl text-gray-300" aria-hidden="true"></i>{{ collect($filters)->except('source')->filter()->isNotEmpty() ? 'Nothing matches these filters.' : 'Lesson plans and notes shared by teachers will appear here.' }}</p>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach ($teacherMaterials as $m)
                        @php $p = $m->owner?->teacherProfile; @endphp
                        <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gold-50 text-gold-700"><i class="fas fa-chalkboard-user text-xl" aria-hidden="true"></i></span>
                            <span class="min-w-0 flex-1">
                                <span class="block font-medium text-gray-800">{{ $m->title }}</span>
                                <span class="block text-xs text-gray-500">{{ collect([\App\Models\Classroom\TeacherMaterial::KINDS[$m->kind] ?? null, $m->subject?->name, $m->classLevel?->name, strtoupper(pathinfo($m->file_name, PATHINFO_EXTENSION)).' · '.number_format($m->file_size / 1048576, 1).' MB'])->filter()->join(' · ') }}</span>
                                <span class="mt-0.5 block text-xs text-gray-500">By
                                    @if ($p?->isListed())<a href="{{ route('teachers.show', $p->slug) }}" class="font-semibold">{{ $m->owner->name }}</a>@else<span class="font-semibold">{{ $m->owner?->name }}</span>@endif
                                    {{ $p?->school_name ? '· '.$p->school_name : '' }}</span>
                            </span>
                            <span class="shrink-0">
                                @auth
                                    <x-button :href="route('downloads.material', $m)" icon="fa-download">Download</x-button>
                                @else
                                    <x-button :href="route('downloads.material', $m)" variant="outline" icon="fa-right-to-bracket">Sign in to download</x-button>
                                @endauth
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="mt-6">{{ $teacherMaterials->links('pagination::tailwind') }}</div>
    @else
    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3]">
        @if ($resources->isEmpty())
            <p class="p-8 text-center text-gray-500"><i class="fas fa-download mb-2 block text-3xl text-gray-300" aria-hidden="true"></i>{{ array_filter($filters) ? 'Nothing matches these filters.' : 'Downloadable documents will appear here as they are published.' }}</p>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($resources as $r)
                    @php $ext = $r->mainFile->extension; @endphp
                    <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md {{ $ext === 'pdf' ? 'bg-red-50 text-red-600' : (in_array($ext, ['docx']) ? 'bg-blue-50 text-blue-600' : (in_array($ext, ['pptx']) ? 'bg-orange-50 text-orange-600' : 'bg-gray-100 text-gray-600')) }}">
                            <i class="fas {{ ['pdf' => 'fa-file-pdf', 'docx' => 'fa-file-word', 'pptx' => 'fa-file-powerpoint', 'xlsx' => 'fa-file-excel'][$ext] ?? 'fa-file-image' }} text-xl" aria-hidden="true"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <a href="{{ route('library.show', $r) }}" class="block font-medium text-gray-800 hover:text-primary-600">{{ $r->title }}</a>
                            <span class="block text-xs text-gray-500">{{ collect([$r->subject?->name, $r->examLabel(), strtoupper($ext).' · '.$r->mainFile->humanSize()])->filter()->join(' · ') }}</span>
                        </span>
                        <span class="flex shrink-0 items-center gap-3">
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $r->isFree() ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-800' }}">{{ $r->isFree() ? 'Free' : 'Premium' }}</span>
                            @if ($canDownload($r))
                                <x-button :href="route('library.download', $r)" icon="fa-download">Download</x-button>
                            @elseif (! auth()->check())
                                <x-button :href="route('login')" variant="outline" icon="fa-right-to-bracket">Sign in</x-button>
                            @else
                                <x-button :href="route('plans.index')" variant="outline" icon="fa-crown">Premium</x-button>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="mt-6">{{ $resources->links('pagination::tailwind') }}</div>
    @endif
</div>
@endsection
