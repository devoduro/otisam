@extends('layouts.portal')

@section('title', 'My lesson plans')
@section('subtitle', 'Plan each week: starter, main activities, reflection and assessment. Keep plans private, or share them in Lesson plans.')

@section('actions')
    <x-button :href="route('teach.lesson-plans.create')" icon="fa-plus">New lesson plan</x-button>
@endsection

@section('content')
@if ($plans->isEmpty())
    <div class="panel p-8 text-center">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-2xl text-primary-600"><i class="fas fa-clipboard-list" aria-hidden="true"></i></span>
        <h2 class="mt-4 text-lg font-bold text-gray-800">Write your first lesson plan</h2>
        <p class="mx-auto mt-1 max-w-md text-sm text-gray-500">Start from a blank plan, or open a shared plan in <a href="{{ route('lesson-plans.index') }}">Lesson plans</a> and choose "Use as my starting point".</p>
        <x-button class="mt-5" :href="route('teach.lesson-plans.create')" icon="fa-plus">New lesson plan</x-button>
    </div>
@else
    <div class="panel overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-100 text-xs uppercase tracking-wide text-gray-500">
                <tr><th class="px-4 py-3">Plan</th><th class="px-4 py-3">Class</th><th class="px-4 py-3">Week</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($plans as $plan)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-800">{{ $plan->title }}</span>
                            @if ($plan->review_note)<span class="mt-0.5 block text-xs text-red-600"><i class="fas fa-circle-exclamation mr-1" aria-hidden="true"></i>{{ $plan->review_note }}</span>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ collect([$plan->subject?->name, $plan->subject?->educationLevel?->code, $plan->classLevel?->name])->filter()->implode(' · ') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $plan->weekLabel() ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ ['published' => 'bg-green-100 text-green-800', 'pending' => 'bg-gold-100 text-gold-800', 'draft' => 'bg-gray-100 text-gray-700', 'archived' => 'bg-gray-200 text-gray-700'][$plan->status] ?? 'bg-gray-100' }}">{{ \App\Models\LessonPlans\LessonPlan::STATUSES[$plan->status] ?? $plan->status }}</span></td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('lesson-plans.show', $plan) }}" class="mr-3 font-semibold no-underline"><i class="fas fa-eye mr-1" aria-hidden="true"></i>View</a>
                            <a href="{{ route('lesson-plans.print', $plan) }}" target="_blank" rel="noopener" class="mr-3 font-semibold no-underline"><i class="fas fa-print mr-1" aria-hidden="true"></i>Print</a>
                            <a href="{{ route('teach.lesson-plans.edit', $plan) }}" class="font-semibold no-underline"><i class="fas fa-pen mr-1" aria-hidden="true"></i>Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
