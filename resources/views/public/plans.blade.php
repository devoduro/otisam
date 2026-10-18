@extends('layouts.app')

@section('title', 'Premium plans')
@section('description', 'One premium library, three lengths. Pay with mobile money or card.')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:py-12">
    <h1 class="text-3xl font-extrabold text-primary-900">Premium plans</h1>
    <p class="mt-1 max-w-2xl text-muted">Every plan unlocks the same premium library: worked solutions, marking guides and premium notes. Pick how long you want it. Pay by mobile money or card through Paystack.</p>

    @if ($current)
        <p class="mt-4 rounded-lg border-l-4 border-green-600 bg-green-50 p-3 text-sm" role="status">
            You have premium access until <strong>{{ $current->ends_at->format('j M Y') }}</strong>. Buying again adds the new period after that date.
        </p>
    @endif

    @if ($plans->isEmpty())
        <p class="mt-8 rounded-xl border border-gray-200 bg-white p-6 text-muted">Premium plans open soon. <a href="{{ route('register') }}">Create a free account</a> to be ready.</p>
    @else
        <ul class="mt-8 grid gap-4 md:grid-cols-3">
            @foreach ($plans as $plan)
                <li class="relative flex flex-col rounded-2xl border {{ $plan->is_featured ? 'border-accent-500 ring-2 ring-accent-500' : 'border-gray-200' }} bg-white p-6">
                    @if ($plan->is_featured)<span class="absolute -top-3 left-6 rounded-full bg-accent-500 px-3 py-0.5 text-xs font-bold text-ink">Best value</span>@endif
                    <h2 class="text-lg font-bold text-primary-900">{{ $plan->name }}</h2>
                    <p class="text-sm text-muted">{{ $plan->durationLabel() }}</p>
                    <p class="mt-4 text-3xl font-extrabold text-ink">{{ $plan->price() }}</p>
                    <p class="text-sm text-muted">about {{ $plan->monthlyPrice() }} a month</p>
                    @if ($plan->benefits)
                        <ul class="mt-4 flex-1 space-y-2 text-sm">
                            @foreach ($plan->benefits as $benefit)
                                <li class="flex gap-2"><span aria-hidden="true" class="text-green-700">✓</span>{{ $benefit }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="mt-6">
                        @auth
                            @if (auth()->user()->isStaff())
                                <p class="text-sm text-muted">Staff accounts cannot buy plans.</p>
                            @else
                                <x-button full :href="route('account.checkout', $plan)" :variant="$plan->is_featured ? 'accent' : 'primary'">Choose {{ $plan->name }}</x-button>
                            @endif
                        @else
                            <x-button full :href="route('register')" :variant="$plan->is_featured ? 'accent' : 'primary'">Sign up to buy</x-button>
                            <p class="mt-2 text-center text-xs text-muted">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
                        @endauth
                    </div>
                </li>
            @endforeach
        </ul>
        <p class="mt-6 text-xs text-muted">Plans do not renew automatically. We remind you by SMS before your access ends. Premium content is our own material; free past papers stay free.</p>
    @endif
</div>
@endsection
