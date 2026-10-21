@extends('layouts.portal')

@section('title', 'My progress')
@section('subtitle', 'Your saved scores from practice papers and practice sets.')

@section('actions')
    <x-button :href="route('papers.index')" icon="fa-stopwatch">Sit a paper</x-button>
@endsection

@section('content')
<div class="grid gap-4 sm:grid-cols-4">
    <x-stat-tile title="Points this month" :value="number_format($points)" icon="fa-star" color="orange" />
    <x-stat-tile title="Rank this month" :value="$rank ? '#'.$rank : '—'" icon="fa-trophy" color="purple" />
    <x-stat-tile title="Attempts" :value="number_format($totals['attempts'])" icon="fa-list-check" color="blue" />
    <x-stat-tile title="Papers sat" :value="number_format($totals['papers'])" icon="fa-file-lines" color="green" />
</div>

@if ($recent->isEmpty())
    <div class="panel mt-6 p-8 text-center">
        <h2 class="text-lg font-bold text-gray-800">No scores yet</h2>
        <p class="mx-auto mt-1 max-w-md text-sm text-gray-500">Sit a practice paper in exam mode or finish a practice set, and your score is saved here.</p>
        <x-button class="mt-5" :href="route('study.index')" icon="fa-book-open">Choose a subject</x-button>
    </div>
@else
    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_20rem]">
        <section class="panel overflow-x-auto">
            <h2 class="px-4 pt-4 font-bold text-gray-800">Recent attempts</h2>
            <table class="mt-2 w-full text-left text-sm">
                <thead class="border-b border-gray-100 text-xs uppercase tracking-wide text-gray-500"><tr><th class="px-4 py-2">What</th><th class="px-4 py-2">Score</th><th class="px-4 py-2">When</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($recent as $a)
                        <tr>
                            <td class="px-4 py-2.5">
                                @if ($a->paper)
                                    <a href="{{ route('papers.show', [$a->subject, $a->paper->number]) }}" class="font-semibold no-underline">{{ $a->paper->title }}</a>
                                @else
                                    <span class="font-semibold text-gray-800">{{ $a->subject?->name }} practice set</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 tabular-nums"><span class="font-bold {{ $a->percent() >= 50 ? 'text-green-700' : 'text-red-600' }}">{{ $a->score }}/{{ $a->total }}</span> <span class="text-gray-400">{{ $a->percent() }}%</span></td>
                            <td class="px-4 py-2.5 text-gray-500">{{ $a->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        <section class="panel p-4">
            <h2 class="font-bold text-gray-800">By subject</h2>
            <ul class="mt-3 space-y-3">
                @foreach ($bySubject as $row)
                    @php $pct = $row->total ? round($row->score / $row->total * 100) : 0; @endphp
                    <li>
                        <div class="flex justify-between text-sm"><span class="font-semibold text-gray-700">{{ $row->subject?->name }}</span><span class="tabular-nums text-gray-500">{{ $pct }}%</span></div>
                        <div class="mt-1 h-2 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full {{ $pct >= 50 ? 'bg-green-500' : 'bg-gold-500' }}" style="width: {{ $pct }}%"></div></div>
                        <p class="mt-0.5 text-xs text-gray-400">{{ $row->attempts }} {{ \Illuminate\Support\Str::plural('attempt', $row->attempts) }}</p>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('leaderboard') }}" class="mt-4 block text-sm font-semibold no-underline"><i class="fas fa-trophy mr-1" aria-hidden="true"></i>See the leaderboard</a>
        </section>
    </div>
@endif
@endsection
