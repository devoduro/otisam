@extends('layouts.app')

@section('title', $resource->title)
@section('description', \Illuminate\Support\Str::limit($resource->description ?: $resource->title.' · '.$resource->category->name, 155))

@section('content')
@php
    $main = $resource->mainFile;
    $preview = $resource->previewFile;
    // What the in-page reader shows: the full PDF when allowed, otherwise only the preview file.
    $readerSrc = $main?->isPdf() && $canViewFull ? route('library.view', $resource) : ($preview ? route('library.preview', $resource) : null);
    $solutions = $resource->linkedResources->whereIn('pivot.relation', ['solution', 'marking_scheme']);
    $relatedLinks = $resource->linkedResources->where('pivot.relation', 'related');
@endphp
<div class="mx-auto max-w-5xl px-4 py-8 sm:py-12">
    <nav aria-label="Breadcrumb" class="text-sm text-gray-500">
        <ol class="flex flex-wrap gap-1">
            <li><a href="{{ route('library.index') }}">Past Questions</a> /</li>
            @if ($resource->subject)<li><a href="{{ route('library.index', ['subject' => $resource->subject->slug]) }}">{{ $resource->subject->name }}</a> /</li>@endif
            <li aria-current="page">{{ \Illuminate\Support\Str::limit($resource->title, 50) }}</li>
        </ol>
    </nav>

    @unless ($resource->isVisible())
        <p class="mt-4 rounded-lg bg-amber-50 p-3 text-sm" role="status">Staff view: this item is not public ({{ \App\Models\Library\Resource::STATUSES[$resource->status] }}).</p>
    @endunless

    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-semibold">
        <span class="rounded-full px-2 py-0.5 {{ $resource->isFree() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-900' }}">{{ $resource->isFree() ? 'Free' : 'Premium' }}</span>
        <span class="text-gray-500">{{ $resource->category->name }}</span>
    </div>
    <h1 class="mt-2 text-2xl font-extrabold text-gray-800 sm:text-3xl">{{ $resource->title }}</h1>

    <dl class="mt-4 grid gap-x-6 gap-y-1 text-sm sm:grid-cols-2">
        @foreach ([
            'Subject' => $resource->subject?->name,
            'Examination' => $resource->examLabel(),
            'Class' => $resource->classLevel?->name,
            'Programme' => $resource->programme?->name,
            'File' => $main ? strtoupper($main->extension).' · '.$main->humanSize().($main->page_count ? ' · '.$main->page_count.' pages' : '') : null,
            'Copyright' => $resource->copyright_holder,
        ] as $label => $value)
            @if ($value)
                <div class="flex gap-2"><dt class="text-gray-500">{{ $label }}:</dt><dd class="font-medium">{{ $value }}</dd></div>
            @endif
        @endforeach
    </dl>

    @if ($resource->description)
        <p class="mt-4 whitespace-pre-line">{{ $resource->description }}</p>
    @endif

    {{-- Access panel --}}
    <div class="mt-6 rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3] p-4 sm:p-5">
        @if ($canViewFull)
            <div class="flex flex-wrap items-center gap-3">
                @if ($canDownload)
                    <x-button :href="route('library.download', $resource)">Download</x-button>
                @else
                    <x-button :href="route('login')" variant="outline">Sign in to download</x-button>
                @endif
                @if ($main && ! $main->isPdf())
                    <span class="text-sm text-gray-500">This {{ strtoupper($main->extension) }} file opens after download.</span>
                @endif
            </div>
        @else
            <h2 class="font-bold text-gray-800">
                @switch($lockReason)
                    @case('teachers') For approved teachers @break
                    @default Premium resource
                @endswitch
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                @switch($lockReason)
                    @case('teachers') This material is available to verified teacher accounts. <a href="{{ route('teachers.register') }}">Register as a teacher</a>. @break
                    @case('premium_guest') {{ $preview ? 'You can read the preview below.' : '' }} <a href="{{ route('plans.index') }}">See premium plans</a>, or <a href="{{ route('register') }}">create a free account</a> first. @break
                    @default {{ $preview ? 'You can read the preview below.' : '' }} <a href="{{ route('plans.index') }}">Get premium access</a> to read and download the full document.
                @endswitch
            </p>
        @endif
    </div>

    {{-- Reader --}}
    @if ($readerSrc)
        <section class="mt-6" aria-label="Document reader">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-800">{{ $canViewFull ? 'Read online' : 'Preview' }}</h2>
            </div>
            {{-- select-none and print:hidden: the pages cannot be selected, and the reader does not print (use Download where allowed). --}}
            <div class="mt-2 select-none rounded-xl bg-gray-100 p-2 sm:p-4 [-webkit-touch-callout:none] print:hidden" data-pdf-src="{{ $readerSrc }}" tabindex="-1" oncontextmenu="return false">
                <p class="mb-2 text-sm text-gray-500" data-pdf-status aria-live="polite">Loading document…</p>
                <div data-pdf-pages></div>
                <noscript><p class="text-sm">Turn on JavaScript to read the document here.</p></noscript>
            </div>
            @unless ($canViewFull)
                <p class="mt-2 text-sm text-gray-500">This is a shortened preview. The full document is part of premium access.</p>
            @endunless
        </section>
        @vite('resources/js/pdf-viewer.js')
    @elseif (! $canViewFull)
        <p class="mt-6 text-sm text-gray-500">No preview is available for this item.</p>
    @endif

    {{-- Solutions and marking schemes --}}
    @if ($solutions->isNotEmpty() || $resource->linkedFrom->isNotEmpty())
        <section class="mt-8">
            <h2 class="text-lg font-bold text-gray-800">Linked material</h2>
            <ul class="mt-2 grid gap-3 sm:grid-cols-2">
                @foreach ($solutions as $linked)
                    <li>
                        <a href="{{ route('library.show', $linked) }}" class="block rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3] p-4 no-underline hover:border-primary-600">
                            <span class="text-xs font-semibold uppercase text-gray-500">{{ \App\Models\Library\Resource::RELATIONS[$linked->pivot->relation] }} · {{ $linked->isFree() ? 'Free' : 'Premium' }}</span>
                            <span class="block font-semibold text-gray-800">{{ $linked->title }}</span>
                        </a>
                    </li>
                @endforeach
                @foreach ($resource->linkedFrom as $parent)
                    <li>
                        <a href="{{ route('library.show', $parent) }}" class="block rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3] p-4 no-underline hover:border-primary-600">
                            <span class="text-xs font-semibold uppercase text-gray-500">Question paper</span>
                            <span class="block font-semibold text-gray-800">{{ $parent->title }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($topics->isNotEmpty())
        <section class="mt-8">
            <h2 class="text-lg font-bold text-gray-800">Curriculum topics</h2>
            <ul class="mt-2 flex flex-wrap gap-2">
                @foreach ($topics as $topic)
                    <li><a href="{{ route('subjects.topic', [$resource->subject, $topic]) }}" class="inline-block rounded-full border border-gray-300 bg-white px-3 py-1 text-sm no-underline hover:border-primary-600">{{ $topic->code ? $topic->code.' ' : '' }}{{ \Illuminate\Support\Str::limit($topic->title, 50) }}</a></li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($relatedLinks->isNotEmpty() || $related->isNotEmpty())
        <section class="mt-8">
            <h2 class="text-lg font-bold text-gray-800">More like this</h2>
            <ul class="mt-2 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($relatedLinks->merge($related)->unique('id')->take(6) as $item)
                    <li>
                        <a href="{{ route('library.show', $item) }}" class="block h-full rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3] p-4 no-underline hover:border-primary-600">
                            <span class="block font-semibold text-gray-800">{{ $item->title }}</span>
                            <span class="text-sm text-gray-500">{{ $item->examLabel() }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <p class="mt-10 text-xs text-gray-500">Think this item infringes your copyright? <a href="{{ route('copyright', ['resource' => $resource->slug]) }}">Report it</a>.</p>
</div>
@endsection
