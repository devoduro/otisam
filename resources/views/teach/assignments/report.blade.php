@extends('layouts.portal')

@php
    use App\Services\Classroom\QuizReport;
    $sum = $report['summary'];
@endphp

@section('title', 'Results: '.$assignment->title)
@section('subtitle', $assignment->classroom->name.($assignment->time_limit_minutes ? ' · '.$assignment->time_limit_minutes.' min time limit' : ''))

@section('actions')
    <x-button variant="outline" :href="route('teach.assignments.show', $assignment)" icon="fa-arrow-left">Back to quiz</x-button>
    <x-button :href="route('teach.assignments.report-csv', $assignment)" icon="fa-file-csv">Download results</x-button>
@endsection

@section('content')
{{-- Summary --}}
<section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4" aria-label="Summary">
    <div class="panel p-4"><p class="text-xs uppercase tracking-wider text-gray-500">Submitted</p><p class="mt-1 text-2xl font-extrabold text-primary-700">{{ $sum['submitted'] }}/{{ $sum['students'] }}</p>
        <p class="text-xs text-gray-500">{{ $sum['in_progress'] }} started, not submitted · {{ $sum['not_started'] }} not started</p></div>
    <div class="panel p-4"><p class="text-xs uppercase tracking-wider text-gray-500">Class average</p><p class="mt-1 text-2xl font-extrabold text-primary-700">{{ $sum['average'] !== null ? $sum['average'].'%' : '—' }}</p>
        <p class="text-xs text-gray-500">Highest {{ $sum['highest'] !== null ? $sum['highest'].'%' : '—' }} · lowest {{ $sum['lowest'] !== null ? $sum['lowest'].'%' : '—' }}</p></div>
    <div class="panel p-4"><p class="text-xs uppercase tracking-wider text-gray-500">Passed ({{ $sum['pass_mark'] }}% or more)</p><p class="mt-1 text-2xl font-extrabold text-primary-700">{{ $sum['passed'] }}/{{ $sum['submitted'] }}</p>
        <form method="GET" class="mt-1 flex items-center gap-1 text-xs text-gray-500"><label for="pass">Pass mark</label><input id="pass" name="pass" type="number" min="1" max="100" value="{{ $sum['pass_mark'] }}" class="w-16 rounded-lg border border-gray-200 px-2 py-0.5"><button class="font-semibold text-primary-700">Apply</button></form></div>
    <div class="panel p-4"><p class="text-xs uppercase tracking-wider text-gray-500">Average time taken</p><p class="mt-1 text-2xl font-extrabold text-primary-700">{{ QuizReport::duration($sum['average_seconds']) }}</p>
        <p class="text-xs text-gray-500">minutes : seconds</p></div>
</section>

{{-- Students --}}
<section class="panel mt-6 overflow-x-auto p-0" aria-labelledby="students-heading">
    <h2 id="students-heading" class="px-5 pt-5 font-bold text-gray-800">Students</h2>
    <table class="mt-3 w-full text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
            <tr><th class="px-4 py-3">#</th><th class="px-4 py-3">Student</th><th class="px-4 py-3">Score</th><th class="px-4 py-3">Time taken</th><th class="px-4 py-3">Submitted</th><th class="px-4 py-3">Left page</th></tr>
        </thead>
        <tbody>
            @forelse ($report['students'] as $i => $r)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2.5 text-gray-400">{{ $i + 1 }}</td>
                    <td class="px-4 py-2.5"><span class="font-medium text-gray-800">{{ $r['name'] }}</span>@if ($r['username'])<span class="block font-mono text-xs text-gray-400">{{ $r['username'] }}</span>@endif</td>
                    <td class="px-4 py-2.5">
                        @if ($r['percent'] !== null)
                            <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $r['percent'] >= $sum['pass_mark'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">{{ $r['percent'] }}%</span>
                            <span class="ml-1 text-gray-500">{{ $r['score'] }}/{{ $r['max'] }}</span>
                        @else
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">{{ $r['status'] }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 tabular-nums">{{ QuizReport::duration($r['seconds']) }}</td>
                    <td class="px-4 py-2.5 text-gray-600">{{ $r['submitted_at']?->format('j M, g:ia') ?? '—' }}@if ($r['late']) <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Late</span>@endif</td>
                    <td class="px-4 py-2.5">@if ($r['tab_leaves'])<span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">{{ $r['tab_leaves'] }}&times;</span>@else<span class="text-gray-400">0</span>@endif</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No students in this class yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>

{{-- Questions --}}
<section class="panel mt-6 p-5" aria-labelledby="questions-heading">
    <h2 id="questions-heading" class="font-bold text-gray-800">Questions, hardest first</h2>
    <p class="text-sm text-gray-500">Questions few students got right are worth going over in class.</p>
    <ol class="mt-3 space-y-3">
        @forelse (QuizReport::questionsSortedByDifficulty($report['questions']) as $q)
            <li class="rounded-xl border-[1.5px] border-[#ece9f3] p-3">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <p class="font-medium text-gray-800"><span class="text-gray-400">Q{{ $q['number'] }}.</span> {{ $q['stem'] }}</p>
                    @if ($q['percent'] !== null)
                        <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $q['percent'] >= 70 ? 'bg-green-100 text-green-800' : ($q['percent'] >= 40 ? 'bg-gold-100 text-gold-800' : 'bg-red-100 text-red-700') }}">{{ $q['percent'] }}% right</span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-gray-600">Correct: <strong>{{ $q['correct'] }}. {{ $q['correct_text'] }}</strong> · {{ $q['right'] }} of {{ $sum['submitted'] }} right @if ($q['answered'] < $sum['submitted']) · {{ $sum['submitted'] - $q['answered'] }} left it blank @endif</p>
                @if ($q['common_wrong'])<p class="text-sm text-red-700">Most common wrong answer: {{ $q['common_wrong'] }} ({{ $q['common_wrong_count'] }})</p>@endif
            </li>
        @empty
            <li class="text-sm text-gray-500">This quiz has no questions.</li>
        @endforelse
    </ol>
</section>
@endsection
