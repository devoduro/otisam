@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
@php use App\Support\Money; @endphp
<div class="mx-auto max-w-lg px-4 py-8 sm:py-12">
    <nav aria-label="Breadcrumb" class="text-sm text-muted"><a href="{{ route('plans.index') }}">Plans</a> / Checkout</nav>
    <h1 class="mt-2 text-2xl font-extrabold text-primary-900">{{ $plan->name }}</h1>
    <p class="text-muted">{{ $plan->durationLabel() }} of premium access</p>

    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6">
        <x-flash />

        @if ($coveredUntil)
            <p class="mb-4 rounded-lg bg-primary-50 p-3 text-sm">You are covered until {{ $coveredUntil->format('j M Y') }}. This plan will start then and run to {{ $coveredUntil->copy()->addMonthsNoOverflow($plan->duration_months)->format('j M Y') }}.</p>
        @endif

        <dl class="space-y-1 text-sm">
            <div class="flex justify-between"><dt>Price</dt><dd>{{ Money::format($quote['list'], $plan->currency) }}</dd></div>
            @if ($quote['discount'] > 0)
                <div class="flex justify-between text-green-800"><dt>Discount ({{ $quote['coupon']->code }})</dt><dd>− {{ Money::format($quote['discount'], $plan->currency) }}</dd></div>
            @endif
            <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-bold"><dt>To pay</dt><dd>{{ Money::format($quote['total'], $plan->currency) }}</dd></div>
        </dl>

        <form method="GET" action="{{ route('account.checkout', $plan) }}" class="mt-4 flex items-end gap-2">
            <div class="flex-1">
                <label for="coupon-check" class="mb-1 block text-sm font-semibold">Discount code <span class="font-normal text-muted">(optional)</span></label>
                <input id="coupon-check" name="coupon" value="{{ $couponCode }}" maxlength="40" class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 uppercase">
            </div>
            <button type="submit" class="min-h-11 rounded-lg border border-primary-900 px-4 font-semibold text-primary-900">Apply</button>
        </form>
        @if ($quote['problem'])<p class="mt-1 text-sm font-medium text-red-700" role="alert">{{ $quote['problem'] }}</p>@endif

        @if ($paymentsReady || $quote['total'] === 0)
            <form method="POST" action="{{ route('account.checkout.store', $plan) }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="coupon" value="{{ $quote['coupon']?->code }}">
                @if ($quote['total'] > 0)
                    <x-input name="email" type="email" label="Email for the receipt" :value="auth()->user()->email" autocomplete="email" hint="Paystack sends its receipt here. Leave empty if you have no email; you will still get an SMS." />
                @endif
                @error('coupon')<p class="text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                <x-button full>{{ $quote['total'] > 0 ? 'Pay '.Money::format($quote['total'], $plan->currency).' with Paystack' : 'Activate for free' }}</x-button>
                @if ($quote['total'] > 0)
                    <p class="text-center text-xs text-muted">You will choose mobile money or card on Paystack's secure page. Your access starts as soon as the payment is confirmed.</p>
                @endif
            </form>
        @else
            <p class="mt-6 rounded-lg bg-amber-50 p-3 text-sm">Online payment is not available yet. Please check back soon.</p>
        @endif
    </div>
</div>
@endsection
