{{--
    Shared filter bar for public catalogues (video lessons, practice).
    Expects: $action, $filters, $levels (with classLevels), $level, $subjects (grouped by education_level_id),
    $topics; optional $sorts (value => label), $searchPlaceholder, $extra (Blade-rendered extra fields).
    Changing a broader filter clears the narrower ones (level → year/subject/topic, subject → topic).
--}}
@php
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038] focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30';
    $years = $level ? $level->classLevels : $levels->flatMap->classLevels;
    $active = collect($filters)->except(['sort', 'count', 'page'])->filter(fn ($v) => filled($v))->isNotEmpty();
@endphp
<form method="GET" action="{{ $action }}" role="search" data-catalogue-filters
      class="mt-6 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
        <div class="sm:col-span-2 lg:col-span-2">
            <label for="f-q" class="mb-1 block text-[13px] font-bold text-primary-800">Search</label>
            <input id="f-q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ $searchPlaceholder ?? 'Search' }}" class="{{ $control }}">
        </div>
        <div>
            <label for="f-level" class="mb-1 block text-[13px] font-bold text-primary-800">Level</label>
            <select id="f-level" name="level" class="{{ $control }}" data-resets="year,subject,topic">
                <option value="">JHS &amp; SHS</option>
                @foreach ($levels as $l)<option value="{{ strtolower($l->code) }}" @selected($level?->id === $l->id)>{{ $l->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label for="f-year" class="mb-1 block text-[13px] font-bold text-primary-800">Year</label>
            <select id="f-year" name="year" class="{{ $control }}" data-resets="topic">
                <option value="">All years</option>
                @foreach ($years as $y)<option value="{{ $y->id }}" @selected((int) ($filters['year'] ?? 0) === $y->id)>{{ $y->name }} (Year {{ $y->sort }})</option>@endforeach
            </select>
        </div>
        <div>
            <label for="f-subject" class="mb-1 block text-[13px] font-bold text-primary-800">Subject</label>
            <select id="f-subject" name="subject" class="{{ $control }}" data-resets="topic">
                <option value="">All subjects</option>
                @foreach ($levels as $l)
                    @continue(! isset($subjects[$l->id]))
                    <optgroup label="{{ $l->name }}">
                        @foreach ($subjects[$l->id] as $s)<option value="{{ $s->slug }}" @selected(($filters['subject'] ?? '') === $s->slug)>{{ $s->name }}</option>@endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div>
            <label for="f-topic" class="mb-1 block text-[13px] font-bold text-primary-800">Topic</label>
            <select id="f-topic" name="topic" class="{{ $control }}" @disabled($topics->isEmpty())>
                <option value="">{{ ($filters['subject'] ?? null) ? ($topics->isEmpty() ? 'No topics tagged yet' : 'All topics') : 'Choose a subject first' }}</option>
                @foreach ($topics as $t)<option value="{{ $t->id }}" @selected((int) ($filters['topic'] ?? 0) === $t->id)>{{ str_repeat('— ', max(0, substr_count($t->path, '/') - 1)) }}{{ $t->code ? $t->code.' ' : '' }}{{ \Illuminate\Support\Str::limit($t->title, 60) }}</option>@endforeach
            </select>
        </div>
        {{ $extra ?? '' }}
        @isset($sorts)
            <div>
                <label for="f-sort" class="mb-1 block text-[13px] font-bold text-primary-800">Sort</label>
                <select id="f-sort" name="sort" class="{{ $control }}">
                    @foreach ($sorts as $value => $label)<option value="{{ $value }}" @selected(($filters['sort'] ?? array_key_first($sorts)) === $value)>{{ $label }}</option>@endforeach
                </select>
            </div>
        @endisset
    </div>
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <x-button icon="fa-filter">Apply filters</x-button>
        @if ($active)<a href="{{ $action }}" class="rounded-xl px-3 py-2 text-sm font-semibold no-underline hover:bg-gray-100"><i class="fas fa-xmark mr-1" aria-hidden="true"></i>Clear all</a>@endif
    </div>
</form>

@include('public.partials.auto-filter-script')
