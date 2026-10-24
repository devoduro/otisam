@extends('layouts.app')

@section('title', 'Video lessons')
@section('description', 'Short video lessons for SHS subjects, organised by subject and topic.')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 md:px-6 sm:py-12">
    <h1 class="text-3xl font-extrabold text-gray-800">Video lessons</h1>
    <p class="mt-1 text-gray-500">Watch a teacher explain the topic, then practise with past questions.</p>

    @include('public.partials.catalogue-filters', [
        'action' => route('videos.index'),
        'searchPlaceholder' => 'e.g. photosynthesis, simultaneous equations',
        'sorts' => ['' => 'By topic', 'newest' => 'Newest first', 'oldest' => 'Oldest first', 'popular' => 'Most watched', 'shortest' => 'Shortest first'],
    ])
    <p class="mt-3 text-sm text-gray-500" aria-live="polite">{{ number_format($videos->total()) }} {{ \Illuminate\Support\Str::plural('video', $videos->total()) }}@if ($topic) in {{ $topic->title }}@elseif ($subject) in {{ $subject->name }}@endif @if ($groups->isNotEmpty())· {{ $groups->whereNotNull('topic')->count() }} topics @endif</p>

    @if ($groups->isNotEmpty())
        @php
            // Tabs: one per subject, plus the groups that are not curriculum topics (olympiad, SAT…).
            $tabOf = fn ($g) => $g['topic'] && in_array($g['topic']->title, \App\Services\Videos\VideoTopicClassifier::EXTRA_TOPICS, true)
                ? 'extras' : ($g['subject']?->slug ?? 'other');
            $tabs = $groups->groupBy($tabOf)->map(fn ($items, $key) => [
                'label' => $key === 'extras' ? 'Olympiad, SAT & more' : ($key === 'other' ? 'Other' : ($items->first()['subject']?->name ?? 'Other')),
                'topics' => $items->count(),
                'videos' => $items->sum('count'),
            ])->sortBy(fn ($t, $key) => $key === 'extras' ? 2 : ($key === 'other' ? 3 : 1));
        @endphp
        <div class="mt-5 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]" data-topic-browser>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap gap-1 rounded-xl bg-[#f5f4fa] p-1" role="tablist" aria-label="Topic groups">
                    <button type="button" role="tab" aria-selected="true" data-tab="all" class="rounded-lg px-3 py-1.5 text-sm font-semibold text-gray-600 aria-selected:bg-white aria-selected:text-primary-800 aria-selected:shadow">All <span class="text-xs text-gray-400">{{ $groups->count() }}</span></button>
                    @foreach ($tabs as $key => $tab)
                        <button type="button" role="tab" aria-selected="false" data-tab="{{ $key }}" class="rounded-lg px-3 py-1.5 text-sm font-semibold text-gray-600 aria-selected:bg-white aria-selected:text-primary-800 aria-selected:shadow">{{ $tab['label'] }} <span class="text-xs text-gray-400">{{ $tab['topics'] }}</span></button>
                    @endforeach
                </div>
                <div class="relative lg:w-72">
                    <i class="fas fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400" aria-hidden="true"></i>
                    <label for="topic-search" class="sr-only">Find a topic</label>
                    <input id="topic-search" type="search" placeholder="Find a topic, e.g. logarithm" autocomplete="off" data-topic-search
                           class="h-10 w-full rounded-xl border-[1.5px] border-[#e2e0ea] pl-8 pr-3 text-sm focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30">
                </div>
            </div>

            <ul class="mt-3 grid gap-x-4 gap-y-0.5 sm:grid-cols-2 lg:grid-cols-3" data-topic-list>
                @foreach ($groups as $group)
                    <li data-topic-item data-group="{{ $tabOf($group) }}" data-name="{{ \Illuminate\Support\Str::lower($group['topic']?->title ?? 'more videos') }}">
                        <a href="#topic-{{ $group['topic']?->id ?? 'other' }}" class="flex items-center justify-between gap-2 rounded-lg px-2 py-1.5 text-sm text-gray-700 no-underline hover:bg-[#f5f4fa] hover:text-primary-800">
                            <span class="truncate">{{ $group['topic']?->title ?? 'More videos' }}</span>
                            <span class="shrink-0 rounded-full bg-[#eef1fb] px-2 text-[11px] font-semibold text-primary-600">{{ $group['count'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                <span data-topic-summary aria-live="polite"></span>
                <button type="button" class="font-semibold text-primary-600 hover:underline" data-topic-more hidden>Show all topics</button>
            </div>
            <p class="mt-2 hidden text-sm text-gray-500" data-topic-empty>No topic matches that search.</p>
        </div>

        @foreach ($groups as $group)
            @php
                $seeAll = $group['topic'] && $group['subject']
                    ? route('videos.index', array_filter(['subject' => $group['subject']->slug, 'topic' => $group['topic']->id, 'year' => $filters['year'] ?? null]))
                    : null;
            @endphp
            <section id="topic-{{ $group['topic']?->id ?? 'other' }}" data-topic-section data-group="{{ $tabOf($group) }}" data-name="{{ \Illuminate\Support\Str::lower($group['topic']?->title ?? 'more videos') }}" class="mt-10 scroll-mt-24" aria-labelledby="topic-{{ $group['topic']?->id ?? 'other' }}-title">
                <div class="flex flex-wrap items-end justify-between gap-2 border-b border-[#ece9f3] pb-2">
                    <div>
                        <h2 id="topic-{{ $group['topic']?->id ?? 'other' }}-title" class="text-xl font-extrabold text-gray-800">{{ $group['topic']?->title ?? 'More videos' }}</h2>
                        <p class="text-xs text-gray-500">{{ collect([$group['subject']?->labelWithLevel(), $group['count'].' '.\Illuminate\Support\Str::plural('video', $group['count'])])->filter()->join(' · ') }}</p>
                    </div>
                    @if ($seeAll && $group['count'] > $group['videos']->count())
                        <a href="{{ $seeAll }}" class="text-sm font-semibold text-primary-600 no-underline hover:underline">See all {{ $group['count'] }} <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i></a>
                    @endif
                </div>
                <ul class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($group['videos'] as $video)
                        @include('public.videos.card')
                    @endforeach
                </ul>
            </section>
        @endforeach
    @else
    @if ($videos->isEmpty())
        <div class="mt-6 rounded-2xl bg-white p-8 text-center shadow-card ring-1 ring-[#ece9f3]">
            <i class="fas fa-circle-play text-4xl text-gray-300" aria-hidden="true"></i>
            <p class="mt-3 text-gray-500">{{ array_filter($filters) ? 'No video matches. Try another search.' : 'Video lessons are being prepared. Check back soon.' }}</p>
        </div>
    @else
        <ul class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($videos as $video)
                @include('public.videos.card')
            @endforeach
        </ul>
        <div class="mt-6">{{ $videos->links('pagination::tailwind') }}</div>
    @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Topic browser: tabs and search narrow both the topic list and the sections below it.
    (function () {
        var box = document.querySelector('[data-topic-browser]');
        if (!box) { return; }
        var LIMIT = 12;
        var tab = 'all', query = '', expanded = false;
        var items = box.querySelectorAll('[data-topic-item]');
        var sections = document.querySelectorAll('[data-topic-section]');
        var more = box.querySelector('[data-topic-more]');
        function matches(el) {
            return (tab === 'all' || el.dataset.group === tab) && (!query || el.dataset.name.indexOf(query) !== -1);
        }
        function render() {
            var shown = 0, total = 0;
            items.forEach(function (li) {
                var ok = matches(li);
                if (ok) { total++; }
                var visible = ok && (expanded || query || shown < LIMIT);
                if (visible) { shown++; }
                li.hidden = !visible;
            });
            sections.forEach(function (sec) { sec.hidden = !matches(sec); });
            more.hidden = query !== '' || total <= LIMIT;
            more.textContent = expanded ? 'Show fewer' : 'Show all ' + total + ' topics';
            box.querySelector('[data-topic-summary]').textContent = total + (total === 1 ? ' topic' : ' topics');
            box.querySelector('[data-topic-empty]').classList.toggle('hidden', total !== 0);
        }
        box.querySelectorAll('[data-tab]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                tab = btn.dataset.tab;
                expanded = false;
                box.querySelectorAll('[data-tab]').forEach(function (b) { b.setAttribute('aria-selected', b === btn ? 'true' : 'false'); });
                render();
            });
        });
        box.querySelector('[data-topic-search]').addEventListener('input', function (e) {
            query = e.target.value.trim().toLowerCase();
            render();
        });
        more.addEventListener('click', function () { expanded = !expanded; render(); });
        render();
    })();
</script>
@endpush
