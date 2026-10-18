@extends('layouts.portal')
@section('hide_heading', '1')

@section('title', 'Payment '.$payment->statusLabel())

@push('head')
    @if (in_array($payment->status, ['initiated', 'pending'], true))
        <meta http-equiv="refresh" content="15">
    @endif
@endpush

@section('content')
<div class="max-w-lg">
    <div class="rounded-md bg-white shadow-sm ring-1 ring-gray-100 p-6 text-center">
        <x-flash />
        @switch($payment->status)
            @case('success')
                <p class="text-5xl" aria-hidden="true">✅</p>
                <h1 class="mt-2 text-2xl font-extrabold text-gray-800">Payment received</h1>
                <p class="mt-2">{{ $payment->amount() }} for {{ $payment->plan->name }}.</p>
                @if ($payment->subscription)
                    <p class="mt-1 text-gray-500">Premium access: {{ $payment->subscription->starts_at->format('j M Y') }} to <strong>{{ $payment->subscription->ends_at->format('j M Y') }}</strong>.</p>
                @endif
                <div class="mt-6 flex flex-col gap-2">
                    <x-button :href="route('library.index')">Go to the library</x-button>
                    @if ($payment->receipt)<a href="{{ route('account.payments.receipt', $payment) }}" class="text-sm">View receipt {{ $payment->receipt->number }}</a>@endif
                </div>
                @break
            @case('initiated')
            @case('pending')
                <p class="text-5xl" aria-hidden="true">⏳</p>
                <h1 class="mt-2 text-2xl font-extrabold text-gray-800">Waiting for confirmation</h1>
                <p class="mt-2 text-gray-500">If you paid by mobile money, approve the prompt on your phone. This page checks again every 15 seconds; you can also leave it, as your access activates as soon as Paystack confirms.</p>
                <form method="POST" action="{{ route('account.payments.check', $payment) }}" class="mt-6">
                    @csrf
                    <x-button>Check again now</x-button>
                </form>
                @if ($payment->authorization_url && $payment->status === 'initiated')
                    <p class="mt-3 text-sm"><a href="{{ $payment->authorization_url }}">Return to the payment page</a></p>
                @endif
                @break
            @default
                <p class="text-5xl" aria-hidden="true">⚠️</p>
                <h1 class="mt-2 text-2xl font-extrabold text-gray-800">Payment not completed</h1>
                <p class="mt-2 text-gray-500">{{ $payment->status === 'refunded' ? 'This payment was refunded.' : 'No money was taken for this attempt, or it was reversed.' }}</p>
                <div class="mt-6"><x-button :href="route('account.checkout', $payment->plan)">Try again</x-button></div>
        @endswitch
        <p class="mt-6 text-xs text-gray-500">Reference: {{ $payment->reference }}</p>
    </div>
    <p class="mt-4 text-center text-sm"><a href="{{ route('account.subscription') }}">My subscription and payments</a></p>
</div>
@endsection
