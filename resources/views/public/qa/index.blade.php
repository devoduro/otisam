@extends('layouts.app')

@section('title', 'Q&A')
@section('description', 'Ask a question about any JHS or SHS subject and get answers from students and teachers in Ghana.')

@section('content')
@php
    use Illuminate\Support\Str;
    $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3 py-2.5 text-sm text-[#241038] focus:border-gold-500 focus:outline-none focus:ring-[3px] focus:ring-gold-500/30';
    $bySubjectLevel = $subjects->groupBy('education_level_id');
@endphp
<div class="mx-auto max-w-5xl px-4 py-8 md:px-6 sm:py-12">
    <x-flash />

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800">Q&amp;A</h1>
            <p class="mt-1 text-gray-500">Stuck on a topic? Ask, and students and teachers will help. Be kind, and do not share exam papers.</p>
        </div>
        @if ($canAsk)
            <x-button :href="route('qa.create')" icon="fa-plus">Ask a question</x-button>
        @elseif (! auth()->check())
            <x-button :href="route('login')" variant="outline" icon="fa-right-to-bracket">Sign in to ask</x-button>
        @endif
    </div>

    <form method="GET" action="{{ route('qa.index') }}" role="search" class="mt-6 grid items-end gap-3 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3] sm:grid-cols-2 lg:grid-cols-5">
        <div>
            <label for="f-level" class="mb-1 block text-[13px] font-bold text-primary-800">Level</label>
            <select id="f-level" name="level" class="{{ $control }}" onchange="this.form.subject.value = ''; this.form.submit()">
                <option value="">JHS &amp; SHS</option>
                @foreach ($levels as $l)<option value="{{ strtolower($l->code) }}" @selected($level?->id === $l->id)>{{ $l->name }}</option>@endforeach
            </select>
        </div>
        <div class="lg:col-span-2">
            <label for="f-subject" class="mb-1 block text-[13px] font-bold text-primary-800">Subject</label>
            <select id="f-subject" name="subject" class="{{ $control }}">
                <option value="">All subjects</option>
                @foreach ($levels as $l)
                    @continue(! isset($bySubjectLevel[$l->id]))
                    <optgroup label="{{ $l->name }}">
                        @foreach ($bySubjectLevel[$l->id] as $s)<option value="{{ $s->slug }}" @selected($subject?->id === $s->id)>{{ $s->name }}</option>@endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1.5 text-sm text-gray-700">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="unanswered" value="1" @checked(! empty($filters['unanswered'])) class="rounded border-gray-300"> Unanswered only</label>
            @auth
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="mine" value="1" @checked(! empty($filters['mine'])) class="rounded border-gray-300"> My questions</label>
            @endauth
        </div>
        <div class="flex gap-2">
            <x-button icon="fa-filter">Filter</x-button>
            @if (collect($filters)->filter()->isNotEmpty())
                <x-button :href="route('qa.index')" variant="ghost">Clear</x-button>
            @endif
        </div>
    </form>

    <p class="mt-3 text-sm text-gray-500" aria-live="polite">{{ number_format($questions->total()) }} {{ Str::plural('question', $questions->total()) }}</p>

    @if ($questions->isEmpty())
        <div class="mt-6 rounded-2xl bg-white p-8 text-center shadow-card ring-1 ring-[#ece9f3]">
            <i class="fas fa-comments text-4xl text-gray-300" aria-hidden="true"></i>
            <p class="mt-3 text-gray-500">{{ collect($filters)->filter()->isNotEmpty() ? 'No question matches. Try another filter.' : 'No questions yet. Be the first to ask.' }}</p>
        </div>
    @else
        <ul class="mt-4 space-y-3">
            @foreach ($questions as $q)
                <li>
                    <a href="{{ route('qa.show', $q) }}" class="group block rounded-2xl bg-white p-5 no-underline shadow-card ring-1 ring-[#ece9f3] transition hover:shadow-md">
                        <span class="flex flex-wrap items-center gap-2 text-[11px] font-bold uppercase tracking-wide">
                            <span class="rounded-full bg-gold-100 px-2 py-0.5 text-gold-800">{{ $q->tagLabel() }}</span>
                            @if ($q->accepted_answer_id)<span class="rounded-full bg-green-100 px-2 py-0.5 text-green-800"><i class="fas fa-check mr-1" aria-hidden="true"></i>Answered</span>@endif
                            @unless ($q->isVisible())<span class="rounded-full bg-red-100 px-2 py-0.5 text-red-700">Hidden for review</span>@endunless
                        </span>
                        <span class="mt-2 block font-semibold text-gray-800 group-hover:text-primary-700">{{ $q->title }}</span>
                        <span class="mt-1 block text-sm text-gray-500">{{ Str::limit($q->body, 160) }}</span>
                        <span class="mt-3 flex flex-wrap gap-x-3 text-xs text-gray-400">
                            <span>{{ $q->authorFirstName() }}</span>
                            <span><i class="far fa-comment mr-1" aria-hidden="true"></i>{{ $q->answer_count }} {{ Str::plural('answer', $q->answer_count) }}</span>
                            <time datetime="{{ $q->created_at->toIso8601String() }}">{{ $q->created_at->diffForHumans() }}</time>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $questions->links('pagination::tailwind') }}</div>
    @endif
</div>
@endsection
