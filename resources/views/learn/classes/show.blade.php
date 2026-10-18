@extends('layouts.portal')

@section('title', $classroom->name)
@section('subtitle', 'Teacher: '.$classroom->teacher->name.($classroom->subject ? ' · '.$classroom->subject->name : ''))

@section('actions')
    <x-button variant="outline" :href="route('learn.classes.index')" icon="fa-arrow-left">My classes</x-button>
@endsection

@section('content')
@if ($classroom->description)
    <div class="panel mb-4 p-4 text-sm text-gray-700"><i class="fas fa-bullhorn mr-1 text-gold-600" aria-hidden="true"></i> {{ $classroom->description }}</div>
@endif

<section class="panel p-5">
    <h2 class="font-bold text-gray-800">Work set for this class</h2>
    @forelse ($assignments as $a)
        @php $s = $submissions[$a->id] ?? null; @endphp
        <a href="{{ route('learn.assignments.show', $a) }}" class="mt-3 flex items-center justify-between gap-3 rounded-xl border-[1.5px] border-[#ece9f3] p-3 no-underline hover:border-gold-500">
            <span class="min-w-0">
                <span class="block truncate font-semibold text-gray-800"><i class="fas {{ $a->isQuiz() ? 'fa-list-check text-gold-600' : 'fa-file-pen text-primary-600' }} mr-1" aria-hidden="true"></i> {{ $a->title }}</span>
                <span class="text-xs text-gray-500">{{ $a->due_at ? 'Due '.$a->due_at->format('D j M, g:ia') : 'No due date' }}</span>
            </span>
            <span class="shrink-0 text-xs font-bold {{ $s?->isSubmitted() ? 'text-green-700' : 'text-gold-700' }}">
                {{ $s?->isSubmitted() ? ($s->score !== null ? (float) $s->score.'/'.(float) $s->max_score : 'Handed in') : 'Open' }}
            </span>
        </a>
    @empty
        <p class="mt-2 text-sm text-gray-500">Nothing set yet.</p>
    @endforelse
</section>

<form method="POST" action="{{ route('learn.classes.leave', $classroom) }}" class="mt-6" onsubmit="return confirm('Leave this class? You can join again with the code.')">
    @csrf @method('DELETE')
    <button type="submit" class="text-sm font-semibold text-gray-500 hover:text-red-600"><i class="fas fa-right-from-bracket mr-1" aria-hidden="true"></i>Leave class</button>
</form>
@endsection
