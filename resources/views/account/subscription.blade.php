@extends('layouts.portal')
@section('hide_heading', '1')

@section('title', 'My subscription')

@section('content')
<div class="max-w-4xl">
    <x-flash />
    <h1 class="text-3xl font-extrabold text-gray-800">My subscription</h1>

    <section class="mt-6 rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3] p-6">
        @if ($current)
            <p class="text-sm font-semibold uppercase text-green-800">Premium active</p>
            <p class="mt-1 text-xl font-bold">Until {{ $current->accessEndsAt()->format('j F Y') }}</p>
            <p class="text-sm text-gray-500">{{ $current->plan?->name ?? \App\Models\Commerce\Subscription::SOURCES[$current->source] }}{{ $current->ends_at->lt($current->accessEndsAt()) ? ' (includes grace period)' : '' }}</p>
            <div class="mt-4"><x-button :href="route('plans.index')" variant="outline">Extend</x-button></div>
        @else
            <p class="text-xl font-bold">Free account</p>
            <p class="text-sm text-gray-500">You can read free material and premium previews.</p>
            <div class="mt-4"><x-button :href="route('plans.index')">See premium plans</x-button></div>
        @endif
    </section>

    @if ($subscriptions->isNotEmpty())
        <h2 class="mt-8 text-lg font-bold text-gray-800">Access history</h2>
        <ul class="mt-2 divide-y divide-gray-200 rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3]">
            @foreach ($subscriptions as $s)
                <li class="flex flex-wrap justify-between gap-2 px-4 py-3 text-sm">
                    <span>{{ $s->plan?->name ?? \App\Models\Commerce\Subscription::SOURCES[$s->source] }} · {{ $s->starts_at->format('j M Y') }} – {{ $s->ends_at->format('j M Y') }}</span>
                    <span class="font-semibold">{{ $s->stateLabel() }}</span>
                </li>
            @endforeach
        </ul>
    @endif

    @if ($payments->isNotEmpty())
        <h2 class="mt-8 text-lg font-bold text-gray-800">Payments</h2>
        <ul class="mt-2 divide-y divide-gray-200 rounded-2xl bg-white shadow-card ring-1 ring-[#ece9f3]">
            @foreach ($payments as $p)
                <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 text-sm">
                    <span>{{ $p->created_at->format('j M Y') }} · {{ $p->plan->name }} · {{ $p->amount() }}</span>
                    <span class="flex items-center gap-3">
                        <a href="{{ route('account.payments.show', $p) }}">{{ $p->statusLabel() }}</a>
                        @if ($p->receipt)<a href="{{ route('account.payments.receipt', $p) }}">Receipt</a>@endif
                    </span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
