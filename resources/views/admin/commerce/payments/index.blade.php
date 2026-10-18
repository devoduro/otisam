@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
@if ($stalePending > 0)
    <div class="alert alert-warning small">{{ $stalePending }} payment(s) have been waiting for more than 30 minutes. The reconciliation job re-checks them every 10 minutes; you can also re-verify one by hand.</div>
@endif

<form method="GET" class="card-panel mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="q" class="form-label small fw-semibold">Search</label>
            <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Reference, name, phone or email">
        </div>
        <div class="col-6 col-md-2">
            <label for="status" class="form-label small fw-semibold">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">All</option>
                @foreach (\App\Models\Commerce\Payment::STATUSES as $v => $l)<option value="{{ $v }}" @selected(($filters['status'] ?? '') === $v)>{{ $l }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2"><label for="from" class="form-label small fw-semibold">From</label><input id="from" type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control"></div>
        <div class="col-6 col-md-2"><label for="to" class="form-label small fw-semibold">To</label><input id="to" type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control"></div>
        <div class="col-6 col-md-2 d-grid"><button class="btn btn-premium" type="submit">Filter</button></div>
    </div>
</form>

<p class="small text-muted">Paid in this view: <strong>{{ number_format($totals->n) }}</strong> payment(s), <strong>{{ \App\Support\Money::format((int) $totals->total) }}</strong>.</p>

<div class="table-panel">
    <table class="table table-hover align-middle table-stack">
        <thead><tr><th>Date</th><th>Customer</th><th>Plan</th><th>Amount</th><th>Status</th><th>Reference</th></tr></thead>
        <tbody>
            @forelse ($payments as $p)
                <tr>
                    <td data-label="Date" class="small text-nowrap">{{ $p->created_at->format('j M Y, H:i') }}</td>
                    <td data-label="Customer">{{ $p->user->name }}<div class="small text-muted">{{ $p->user->phone }}</div></td>
                    <td data-label="Plan">{{ $p->plan->name }}</td>
                    <td data-label="Amount">{{ $p->amount() }}</td>
                    <td data-label="Status">@include('admin.commerce.payments._status', ['status' => $p->status])</td>
                    <td data-label="Reference"><a href="{{ route('admin.commerce.payments.show', $p) }}" class="small font-monospace">{{ $p->reference }}</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No payments.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $payments->links() }}</div>
@endsection
