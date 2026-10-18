@extends('layouts.portal')

@section('title', $classroom->name)
@section('subtitle', collect([$classroom->subject?->name, $classroom->classLevel?->name, $classroom->isArchived() ? 'Archived' : null])->filter()->implode(' · ') ?: 'Class')

@section('actions')
    @unless ($classroom->isArchived())
        <x-button :href="route('teach.assignments.create', [$classroom, 'type' => 'assignment'])" icon="fa-file-pen">New assignment</x-button>
        <x-button variant="accent" :href="route('teach.assignments.create', [$classroom, 'type' => 'quiz'])" icon="fa-list-check">New quiz</x-button>
    @endunless
    <x-button variant="outline" :href="route('teach.classes.edit', $classroom)" icon="fa-pen">Edit</x-button>
@endsection

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <section class="panel p-5" aria-labelledby="work-heading">
            <h2 id="work-heading" class="font-bold text-gray-800">Assignments and quizzes</h2>
            @forelse ($assignments as $a)
                <a href="{{ route('teach.assignments.show', $a) }}" class="mt-3 flex items-center justify-between gap-3 rounded-xl border-[1.5px] border-[#ece9f3] p-3 no-underline hover:border-gold-500">
                    <span class="min-w-0">
                        <span class="block truncate font-semibold text-gray-800"><i class="fas {{ $a->isQuiz() ? 'fa-list-check text-gold-600' : 'fa-file-pen text-primary-600' }} mr-1" aria-hidden="true"></i> {{ $a->title }}</span>
                        <span class="text-xs text-gray-500">{{ \App\Models\Classroom\Assignment::TYPES[$a->type] }} · {{ $a->due_at ? 'Due '.$a->due_at->format('j M, g:ia') : 'No due date' }}</span>
                    </span>
                    <span class="flex shrink-0 items-center gap-2 text-xs font-semibold">
                        <span class="rounded-full px-2 py-0.5 {{ ['draft' => 'bg-gray-100 text-gray-600', 'published' => 'bg-green-100 text-green-800', 'closed' => 'bg-red-100 text-red-800'][$a->status] }}">{{ \App\Models\Classroom\Assignment::STATUSES[$a->status] }}</span>
                        @if ($a->isPublished())<span class="text-gray-500">{{ $a->submitted_count }}/{{ $classroom->students->count() }} in</span>@endif
                    </span>
                </a>
            @empty
                <p class="mt-2 text-sm text-gray-500">Nothing set yet. Use <strong>New assignment</strong> or <strong>New quiz</strong> above.</p>
            @endforelse
        </section>

        <section class="panel p-5" aria-labelledby="students-heading">
            <h2 id="students-heading" class="font-bold text-gray-800">Students ({{ $classroom->students->count() }})</h2>
            @forelse ($classroom->students as $student)
                <div class="flex items-center justify-between gap-3 border-b border-gray-100 py-2.5 last:border-0">
                    <div class="min-w-0">
                        <p class="truncate font-medium text-gray-800">{{ $student->name }}</p>
                        <p class="text-xs text-gray-500">Joined {{ \Illuminate\Support\Carbon::parse($student->pivot->joined_at)->diffForHumans() }}</p>
                    </div>
                    <form method="POST" action="{{ route('teach.classes.students.remove', [$classroom, $student]) }}" onsubmit="return confirm('Remove this student from the class?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="rounded-lg px-2 py-1 text-sm text-gray-400 hover:bg-red-50 hover:text-red-600" title="Remove"><i class="fas fa-user-minus" aria-hidden="true"></i><span class="sr-only">Remove {{ $student->name }}</span></button>
                    </form>
                </div>
            @empty
                <p class="mt-2 text-sm text-gray-500">No students yet. Share the join code.</p>
            @endforelse
        </section>
    </div>

    <aside class="space-y-6">
        <section class="relative overflow-hidden rounded-2xl bg-primary-700 bg-crosshatch p-5 text-white shadow-lift" aria-labelledby="code-heading">
            <h2 id="code-heading" class="text-sm font-semibold text-white/70">Class join code</h2>
            <p class="mt-1 font-mono text-4xl font-extrabold tracking-[0.2em]" data-join-code>{{ $classroom->join_code }}</p>
            <p class="mt-2 text-sm text-white/75">
                @if ($classroom->joining_open && ! $classroom->isArchived())
                    Students sign in, open <strong>My classes</strong> and enter this code.
                @else
                    Joining is switched off for this class.
                @endif
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <button type="button" class="rounded-xl bg-gold-500 px-3 py-2 text-sm font-bold text-primary-950" onclick="navigator.clipboard?.writeText('{{ $classroom->join_code }}'); this.textContent='Copied';"><i class="fas fa-copy mr-1" aria-hidden="true"></i>Copy</button>
                <form method="POST" action="{{ route('teach.classes.code', $classroom) }}" onsubmit="return confirm('Make a new code? The old one will stop working.')">
                    @csrf
                    <button type="submit" class="rounded-xl bg-white/10 px-3 py-2 text-sm font-bold text-white ring-1 ring-white/30 hover:bg-white/20"><i class="fas fa-rotate mr-1" aria-hidden="true"></i>New code</button>
                </form>
            </div>
        </section>

        @if ($classroom->description)
            <section class="panel p-5"><h2 class="text-sm font-bold text-gray-800">Note for students</h2><p class="mt-1 whitespace-pre-line text-sm text-gray-600">{{ $classroom->description }}</p></section>
        @endif

        <form method="POST" action="{{ route('teach.classes.archive', $classroom) }}">
            @csrf
            <button type="submit" class="text-sm font-semibold text-gray-500 hover:text-gray-700"><i class="fas {{ $classroom->isArchived() ? 'fa-box-open' : 'fa-box-archive' }} mr-1" aria-hidden="true"></i>{{ $classroom->isArchived() ? 'Restore this class' : 'Archive this class' }}</button>
        </form>
    </aside>
</div>
@endsection
