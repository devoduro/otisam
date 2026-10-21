{{-- A subject's papers with Study / Exam / Solutions buttons. Expects $subject, $papers (with questions_count) and $best (paper id => best score). --}}
<ul class="mt-6 space-y-3">
    @foreach ($papers as $paper)
        <li class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow-card ring-1 ring-[#ece9f3]">
            <div>
                <span class="rounded-full bg-gold-100 px-2 py-0.5 text-[11px] font-bold text-gold-800">{{ $paper->exam_style }} style</span>
                <a href="{{ route('papers.show', [$subject, $paper->number]) }}" class="mt-1 block font-semibold text-gray-800 no-underline hover:text-primary-700">{{ $paper->title }}</a>
                <span class="text-xs text-gray-500">Objective · {{ $paper->questions_count }} questions · {{ $paper->duration_minutes }} min</span>
                @isset($best[$paper->id])<span class="ml-1 rounded-full bg-green-100 px-2 py-0.5 text-[11px] font-bold text-green-800">Best: {{ $best[$paper->id] }}/{{ $paper->questions_count }}</span>@endisset
            </div>
            <div class="flex flex-wrap gap-2">
                <x-button :href="route('papers.take', [$subject, $paper->number, 'mode' => 'study'])" variant="outline" icon="fa-book-open">Study</x-button>
                <x-button :href="route('papers.take', [$subject, $paper->number, 'mode' => 'exam'])" icon="fa-stopwatch">Exam</x-button>
                <x-button :href="route('papers.solutions', [$subject, $paper->number])" variant="ghost" icon="fa-key">Solutions</x-button>
            </div>
        </li>
    @endforeach
</ul>
