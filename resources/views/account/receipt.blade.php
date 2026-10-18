<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Receipt {{ $payment->receipt->number }} | {{ config('app.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; max-width: 640px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
        h1 { color: #13294B; margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        td, th { padding: .5rem 0; border-bottom: 1px solid #e5e7eb; text-align: left; }
        td:last-child, th:last-child { text-align: right; }
        .muted { color: #4b5563; font-size: .9rem; }
        .total td { font-weight: bold; border-bottom: 2px solid #13294B; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <p class="no-print"><a href="{{ route('account.subscription') }}">← Back</a> · <a href="#" onclick="window.print(); return false;">Print / save as PDF</a></p>
    <h1>{{ config('app.name') }}</h1>
    <p class="muted">Payment receipt</p>

    <p><strong>Receipt:</strong> {{ $payment->receipt->number }}<br>
       <strong>Date:</strong> {{ ($payment->paid_at ?? $payment->receipt->issued_at)->format('j F Y, H:i') }}<br>
       <strong>Customer:</strong> {{ $payment->user->name }} ({{ \App\Support\Phone::mask($payment->user->phone) }})<br>
       <strong>Reference:</strong> {{ $payment->reference }}{{ $payment->channel ? ' · '.str_replace('_', ' ', $payment->channel) : '' }}</p>

    <table>
        <tr><th>Item</th><th>Amount</th></tr>
        <tr><td>{{ $payment->plan->name }} ({{ $payment->plan->durationLabel() }})@if ($payment->subscription)<br><span class="muted">Access {{ $payment->subscription->starts_at->format('j M Y') }} – {{ $payment->subscription->ends_at->format('j M Y') }}</span>@endif</td>
            <td>{{ \App\Support\Money::format($payment->list_price_minor, $payment->currency) }}</td></tr>
        @if ($payment->discount_minor > 0)
            <tr><td>Discount</td><td>− {{ \App\Support\Money::format($payment->discount_minor, $payment->currency) }}</td></tr>
        @endif
        <tr class="total"><td>Paid</td><td>{{ $payment->amount() }}</td></tr>
    </table>
    @if ($payment->status === 'refunded')<p><strong>This payment was refunded.</strong></p>@endif
</body>
</html>
