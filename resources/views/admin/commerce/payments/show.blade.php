@extends('layouts.admin')

@section('title', 'Payment '.$payment->reference)

@section('content')
@php use App\Support\Money; @endphp
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-panel mb-3">
            <dl class="row small mb-0">
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">@include('admin.commerce.payments._status', ['status' => $payment->status]) {{ $payment->failure_reason }}</dd>
                <dt class="col-sm-4">Customer</dt><dd class="col-sm-8">
                    @if ($payment->user->isStudent())<a href="{{ route('admin.students.show', $payment->user) }}">{{ $payment->user->name }}</a>
                    @elseif ($payment->user->isTeacher())<a href="{{ route('admin.teachers.show', $payment->user) }}">{{ $payment->user->name }}</a>
                    @else {{ $payment->user->name }} @endif · {{ $payment->user->phone }}</dd>
                <dt class="col-sm-4">Plan</dt><dd class="col-sm-8">{{ $payment->plan->name }} ({{ $payment->plan->durationLabel() }})</dd>
                <dt class="col-sm-4">List price</dt><dd class="col-sm-8">{{ Money::format($payment->list_price_minor, $payment->currency) }}</dd>
                @if ($payment->discount_minor)<dt class="col-sm-4">Discount</dt><dd class="col-sm-8">{{ Money::format($payment->discount_minor, $payment->currency) }} ({{ $payment->coupon?->code }})</dd>@endif
                <dt class="col-sm-4">Charged</dt><dd class="col-sm-8 fw-bold">{{ $payment->amount() }}</dd>
                <dt class="col-sm-4">Provider</dt><dd class="col-sm-8">{{ $payment->provider }}{{ $payment->channel ? ' · '.$payment->channel : '' }}{{ $payment->provider_transaction_id ? ' · id '.$payment->provider_transaction_id : '' }}</dd>
                <dt class="col-sm-4">Started</dt><dd class="col-sm-8">{{ $payment->created_at->format('j M Y, H:i:s') }}</dd>
                <dt class="col-sm-4">Paid</dt><dd class="col-sm-8">{{ $payment->paid_at?->format('j M Y, H:i:s') ?? '—' }}</dd>
                <dt class="col-sm-4">Verified</dt><dd class="col-sm-8">{{ $payment->verified_at?->format('j M Y, H:i:s') ?? '—' }}{{ $payment->verification_source ? ' via '.$payment->verification_source : '' }}</dd>
                <dt class="col-sm-4">Receipt</dt><dd class="col-sm-8">{{ $payment->receipt?->number ?? '—' }}</dd>
                <dt class="col-sm-4">Subscription</dt><dd class="col-sm-8">{{ $payment->subscription ? $payment->subscription->starts_at->format('j M Y').' – '.$payment->subscription->ends_at->format('j M Y').' ('.$payment->subscription->stateLabel().')' : '—' }}</dd>
            </dl>
        </div>
        @if ($payment->provider_payload)
            <details class="card-panel small"><summary>What Paystack reported</summary>
                <pre class="mb-0 mt-2">{{ json_encode($payment->provider_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></details>
        @endif
    </div>
    <div class="col-lg-5">
        @can('payments.reconcile')
            @if ($payment->provider === 'paystack' && $payment->status !== 'refunded')
                <div class="card-panel mb-3">
                    <h2 class="h6 fw-bold mb-2">Check with Paystack</h2>
                    <p class="small text-muted">Asks Paystack for the current state. If it was paid, access is activated (once).</p>
                    <form method="POST" action="{{ route('admin.commerce.payments.reverify', $payment) }}">@csrf<button class="btn btn-outline-primary btn-sm" type="submit">Re-verify now</button></form>
                </div>
            @endif
        @endcan
        @can('refunds.record')
            @if ($payment->status === 'success')
                <form method="POST" action="{{ route('admin.commerce.payments.refund', $payment) }}" class="card-panel mb-3">
                    @csrf
                    <h2 class="h6 fw-bold mb-2">Record a refund</h2>
                    <p class="small text-muted">This records the refund here. Send the money back from the Paystack dashboard.</p>
                    <label for="amount" class="form-label small fw-semibold">Amount ({{ $payment->currency }})</label>
                    <input id="amount" name="amount" value="{{ Money::toDecimal($payment->amount_minor) }}" class="form-control form-control-sm mb-2" required>
                    <label for="reason" class="form-label small fw-semibold">Reason</label>
                    <input id="reason" name="reason" class="form-control form-control-sm mb-2" required maxlength="255">
                    <input type="hidden" name="revoke_access" value="0">
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="revoke_access" value="1" id="revoke_access" checked><label class="form-check-label small" for="revoke_access">End the premium access bought with this payment</label></div>
                    <button class="btn btn-outline-danger btn-sm" type="submit" data-confirm="Record this refund?">Record refund</button>
                </form>
            @endif
        @endcan
        @foreach ($payment->refunds as $refund)
            <div class="card-panel mb-2 small">Refund {{ Money::format($refund->amount_minor, $payment->currency) }} · {{ $refund->created_at->format('j M Y') }} · {{ $refund->recorder?->name ?? 'Paystack' }}<br>{{ $refund->reason }}{{ $refund->revoked_access ? ' (access ended)' : ' (access kept)' }}</div>
        @endforeach
    </div>
</div>
@endsection
