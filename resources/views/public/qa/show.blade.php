@extends('layouts.app')

@section('title', \Illuminate\Support\Str::limit($question->title, 60).' · Q&A')
@section('description', \Illuminate\Support\Str::limit($question->tagLabel().': '.$question->body, 155))

@php
    use Illuminate\Support\Str;
    $user = auth()->user();
    $isOwner = $question->isOwnedBy($user);
    $hiddenNote = fn ($post) => $post->hidden_reason === 'reports' ? 'hidden after reports, waiting for review' : 'hidden by a moderator';
@endphp

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 md:px-6 sm:py-12">
    <x-flash />

    <nav aria-label="Breadcrumb" class="text-sm text-gray-500">
        <a href="{{ route('qa.index') }}">Q&amp;A</a>
        @if ($question->subject) / <a href="{{ route('qa.index', ['subject' => $question->subject->slug]) }}">{{ $question->subject->name }}</a>@endif
    </nav>

    @unless ($question->isVisible())
        <p class="mt-4 rounded-md bg-amber-50 p-3 text-sm" role="status">
            {{ $isOwner ? 'Only you and our moderators can see this question' : 'Staff view: this question is not public' }} ({{ $hiddenNote($question) }}).
        </p>
    @endunless

    <article class="mt-3 rounded-2xl bg-white p-6 shadow-card ring-1 ring-[#ece9f3]">
        <span class="text-[11px] font-bold uppercase tracking-wide"><span class="rounded-full bg-gold-100 px-2 py-0.5 text-gold-800">{{ $question->tagLabel() }}</span></span>
        <h1 class="mt-3 text-2xl font-bold text-gray-800">{{ $question->title }}</h1>
        <div class="mt-3 whitespace-pre-line break-words text-gray-700">{{ $question->body }}</div>
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-400">
            <span>Asked by {{ $question->authorFirstName() }} · <time datetime="{{ $question->created_at->toIso8601String() }}">{{ $question->created_at->diffForHumans() }}</time>@if ($question->edited_at) · edited @endif</span>
            <span class="flex items-center gap-3">
                @if ($isOwner)
                    <a href="{{ route('qa.edit', $question) }}" class="font-semibold no-underline"><i class="fas fa-pen mr-1" aria-hidden="true"></i>Edit</a>
                    <form method="POST" action="{{ route('qa.destroy', $question) }}" onsubmit="return confirm('Delete this question and its answers?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="font-semibold text-red-600 hover:underline"><i class="fas fa-trash mr-1" aria-hidden="true"></i>Delete</button>
                    </form>
                @elseif ($user)
                    @include('public.qa.report', ['action' => route('qa.report', $question), 'id' => 'q'.$question->id])
                @endif
            </span>
        </div>
    </article>

    <h2 class="mt-8 text-lg font-bold text-gray-800">{{ $answers->where('status', 'visible')->count() }} {{ Str::plural('answer', $answers->where('status', 'visible')->count()) }}</h2>

    <ol class="mt-3 space-y-3">
        @forelse ($answers as $a)
            @php $accepted = $a->id === $question->accepted_answer_id; @endphp
            <li id="answer-{{ $a->id }}" class="rounded-2xl bg-white p-5 shadow-card ring-1 {{ $accepted ? 'ring-2 ring-green-400' : 'ring-[#ece9f3]' }}">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="font-semibold text-gray-700">{{ $a->authorFirstName() }}</span>
                    @if ($a->byTeacher())<span class="rounded-full bg-primary-50 px-2 py-0.5 font-bold text-primary-700"><i class="fas fa-chalkboard-user mr-1" aria-hidden="true"></i>Teacher</span>@endif
                    @if ($accepted)<span class="rounded-full bg-green-100 px-2 py-0.5 font-bold text-green-800"><i class="fas fa-check mr-1" aria-hidden="true"></i>Accepted</span>@endif
                    @unless ($a->isVisible())<span class="rounded-full bg-red-100 px-2 py-0.5 font-bold text-red-700">{{ ucfirst($hiddenNote($a)) }}</span>@endunless
                    <time datetime="{{ $a->created_at->toIso8601String() }}" class="text-gray-400">{{ $a->created_at->diffForHumans() }}@if ($a->edited_at) · edited @endif</time>
                </div>
                <div class="mt-2 whitespace-pre-line break-words text-gray-700">{{ $a->body }}</div>
                <div class="mt-3 flex flex-wrap items-center justify-end gap-3 text-xs">
                    @if ($canAccept && $a->isVisible() && $question->isVisible())
                        <form method="POST" action="{{ route('qa.accept', [$question, $a]) }}">
                            @csrf
                            <button type="submit" class="font-semibold {{ $accepted ? 'text-gray-500' : 'text-green-700' }} hover:underline"><i class="fas {{ $accepted ? 'fa-xmark' : 'fa-check' }} mr-1" aria-hidden="true"></i>{{ $accepted ? 'Unmark accepted' : 'Accept this answer' }}</button>
                        </form>
                    @endif
                    @if ($a->isOwnedBy($user))
                        <a href="{{ route('qa.answers.edit', $a) }}" class="font-semibold no-underline"><i class="fas fa-pen mr-1" aria-hidden="true"></i>Edit</a>
                        <form method="POST" action="{{ route('qa.answers.destroy', $a) }}" onsubmit="return confirm('Delete your answer?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="font-semibold text-red-600 hover:underline"><i class="fas fa-trash mr-1" aria-hidden="true"></i>Delete</button>
                        </form>
                    @elseif ($user)
                        @include('public.qa.report', ['action' => route('qa.answers.report', $a), 'id' => 'a'.$a->id])
                    @endif
                </div>
            </li>
        @empty
            <li class="rounded-2xl bg-white p-6 text-center text-gray-500 shadow-card ring-1 ring-[#ece9f3]">No answers yet.</li>
        @endforelse
    </ol>

    @if ($canAnswer)
        <form method="POST" action="{{ route('qa.answers.store', $question) }}" class="mt-6 rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
            @csrf
            <label for="body" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Your answer</label>
            <textarea id="body" name="body" rows="5" required maxlength="5000" class="block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]" placeholder="Explain the steps, not just the final answer.">{{ old('body') }}</textarea>
            @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-gray-500">Plain text only. Write in your own words.</p>
            <div class="mt-3"><x-button icon="fa-paper-plane">Post answer</x-button></div>
        </form>
    @elseif (! $user && $question->isVisible())
        <p class="mt-6 rounded-2xl bg-white p-5 text-center text-sm text-gray-600 shadow-card ring-1 ring-[#ece9f3]">
            <a href="{{ route('login') }}" class="font-semibold">Sign in</a> or <a href="{{ route('register') }}" class="font-semibold">create a free account</a> to answer.
        </p>
    @endif

    @if ($isModerator)
        <p class="mt-6 text-sm"><a href="{{ route('admin.qa.index') }}"><i class="fas fa-shield-halved mr-1" aria-hidden="true"></i>Q&amp;A reports</a></p>
    @endif
</div>
@endsection
