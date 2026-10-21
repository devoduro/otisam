@extends('layouts.app')

@section('title', $paper->title.' · Solutions')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 md:px-6 sm:py-12">
    <a href="{{ route('papers.show', [$subject, $paper->number]) }}" class="text-sm font-semibold no-underline"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>{{ $paper->title }}</a>
    <h1 class="mt-2 text-2xl font-extrabold text-gray-800">{{ $paper->title }}: solutions</h1>
    <p class="mt-1 text-sm text-gray-500">{{ $questions->count() }} questions with the correct option and a short explanation.</p>

    <ol class="mt-6 space-y-4">
        @foreach ($questions as $question)
            @php $revision = $question->currentRevision; $correct = $keys[$question->current_revision_id]['correct'] ?? null; @endphp
            <li class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-[#ece9f3]">
                <p class="text-xs font-semibold text-gray-400">Question {{ $loop->iteration }}</p>
                <p class="mt-1 whitespace-pre-line font-semibold text-gray-800">{{ $revision->stem }}</p>
                <ul class="mt-3 grid gap-1.5 text-sm sm:grid-cols-2">
                    @foreach ($revision->payload['options'] ?? [] as $option)
                        <li class="rounded-lg px-3 py-2 {{ $option['id'] === $correct ? 'bg-green-50 font-semibold text-green-900 ring-1 ring-green-600' : 'bg-gray-50 text-gray-700' }}">
                            <strong class="mr-1">{{ $option['id'] }}.</strong> {{ $option['text'] }}@if ($option['id'] === $correct) <i class="fas fa-check ml-1" aria-label="correct answer"></i>@endif
                        </li>
                    @endforeach
                </ul>
                @if ($revision->explanation)
                    <p class="mt-3 rounded-xl bg-primary-50 p-3 text-sm text-primary-900"><strong>Why:</strong> {{ $revision->explanation }}</p>
                @endif
            </li>
        @endforeach
    </ol>
    <p class="mt-6 text-xs text-gray-400">Questions written by {{ config('app.name') }} in the style of the {{ $paper->exam_style }}. They are not WAEC past questions.</p>
</div>
@endsection
