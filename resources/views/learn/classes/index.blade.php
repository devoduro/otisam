@extends('layouts.portal')

@section('title', 'My classes')
@section('subtitle', 'Join your teacher\'s class with the code they give you, then find your assignments and quizzes here.')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <section class="panel p-5" aria-labelledby="todo-heading">
            <h2 id="todo-heading" class="font-bold text-gray-800">Assignments and quizzes</h2>
            @forelse ($assignments as $a)
                @php
                    $s = $submissions[$a->id] ?? null;
                    [$tone, $label] = match (true) {
                        $s?->isGraded() && $s->score !== null => ['bg-green-100 text-green-800', (float) $s->score.'/'.(float) $s->max_score],
                        (bool) $s?->isSubmitted() => ['bg-primary-100 text-primary-800', 'Handed in'],
                        ! $a->isOpen() => ['bg-gray-100 text-gray-600', 'Closed'],
                        $a->isOverdue() => ['bg-red-100 text-red-700', 'Overdue'],
                        default => ['bg-gold-100 text-gold-800', 'To do'],
                    };
                @endphp
                <a href="{{ route('learn.assignments.show', $a) }}" class="mt-3 flex items-center justify-between gap-3 rounded-xl border-[1.5px] border-[#ece9f3] p-3 no-underline hover:border-gold-500">
                    <span class="min-w-0">
                        <span class="block truncate font-semibold text-gray-800"><i class="fas {{ $a->isQuiz() ? 'fa-list-check text-gold-600' : 'fa-file-pen text-primary-600' }} mr-1" aria-hidden="true"></i> {{ $a->title }}</span>
                        <span class="text-xs text-gray-500">{{ $a->classroom->name }} · {{ $a->due_at ? 'Due '.$a->due_at->format('D j M, g:ia') : 'No due date' }}</span>
                    </span>
                    <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $tone }}">{{ $label }}</span>
                </a>
            @empty
                <p class="mt-2 text-sm text-gray-500">{{ $classrooms->isEmpty() ? 'Join a class to see work from your teacher.' : 'No work set yet.' }}</p>
            @endforelse
        </section>
    </div>

    <aside class="space-y-6">
        <form method="POST" action="{{ route('learn.classes.join') }}" class="relative overflow-hidden rounded-2xl bg-primary-700 bg-crosshatch p-5 text-white shadow-lift">
            @csrf
            <h2 class="font-bold">Join a class</h2>
            <p class="mt-1 text-sm text-white/75">Enter the 6-character code from your teacher.</p>
            <label for="code" class="sr-only">Class code</label>
            <input id="code" name="code" value="{{ old('code') }}" required maxlength="12" autocomplete="off" autocapitalize="characters" placeholder="e.g. K7Q2MX"
                   class="mt-3 block w-full rounded-xl border-0 bg-white px-4 py-3 text-center font-mono text-xl font-bold uppercase tracking-[0.3em] text-primary-900 placeholder:tracking-normal placeholder:text-gray-400">
            @error('code')<p class="mt-2 rounded-lg bg-red-500/20 px-3 py-2 text-sm">{{ $message }}</p>@enderror
            <button type="submit" class="mt-3 inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-gold-500 font-bold text-primary-950 shadow-gold hover:bg-gold-400">Join class <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></button>
        </form>

        <section class="panel p-5" aria-labelledby="classes-heading">
            <h2 id="classes-heading" class="text-sm font-bold text-gray-800">My classes ({{ $classrooms->count() }})</h2>
            @forelse ($classrooms as $c)
                <a href="{{ route('learn.classes.show', $c) }}" class="mt-2 block rounded-xl p-2 no-underline hover:bg-gray-50">
                    <span class="block font-semibold text-gray-800">{{ $c->name }}</span>
                    <span class="text-xs text-gray-500">{{ $c->teacher->name }}{{ $c->subject ? ' · '.$c->subject->name : '' }}</span>
                </a>
            @empty
                <p class="mt-2 text-sm text-gray-500">You have not joined any class yet.</p>
            @endforelse
        </section>
    </aside>
</div>
@endsection
