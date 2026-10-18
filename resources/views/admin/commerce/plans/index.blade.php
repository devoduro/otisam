@extends('layouts.admin')

@section('title', 'Plans')
@section('subtitle', 'Premium plans: same library, different durations')

@section('actions')
    <a href="{{ route('admin.commerce.plans.create') }}" class="btn btn-premium rounded-pill px-4">Add plan</a>
@endsection

@section('content')
<div class="alert {{ in_array($paystackMode, ['live', 'test'], true) ? ($paystackMode === 'live' ? 'alert-success' : 'alert-info') : 'alert-warning' }} small">
    Paystack: <strong>{{ $paystackMode }}</strong>.
    @if ($paystackMode === 'not configured') Add PAYSTACK_SECRET_KEY and PAYSTACK_PUBLIC_KEY to .env. Until then nobody can pay. @endif
    @if ($paystackMode === 'test') Payments use Paystack test mode: no real money moves. @endif
    Webhook URL for the Paystack dashboard: <code>{{ route('webhooks.paystack') }}</code>
</div>

<div class="table-panel mb-3">
    <table class="table table-hover align-middle table-stack">
        <thead><tr><th>Plan</th><th>Duration</th><th>Price</th><th>Sold</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
            @forelse ($plans as $plan)
                <tr>
                    <td data-label="Plan" class="fw-semibold">{{ $plan->name }} @if ($plan->is_featured)<span class="badge text-bg-warning">featured</span>@endif</td>
                    <td data-label="Duration">{{ $plan->durationLabel() }}</td>
                    <td data-label="Price">{{ $plan->price_minor > 0 ? $plan->price() : '— not set —' }}</td>
                    <td data-label="Sold">{{ $plan->payments_count }}</td>
                    <td data-label="Status"><span class="badge text-bg-{{ $plan->is_active ? 'success' : 'secondary' }}">{{ $plan->is_active ? 'On sale' : 'Hidden' }}</span></td>
                    <td data-label="Actions" class="text-end"><a href="{{ route('admin.commerce.plans.edit', $plan) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No plans yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<form method="POST" action="{{ route('admin.commerce.grace') }}" class="card-panel">
    @csrf @method('PUT')
    <h2 class="h6 fw-bold mb-2">Grace period</h2>
    <div class="d-flex flex-wrap gap-2 align-items-end">
        <div>
            <label for="grace_days" class="form-label small fw-semibold">Days of access after a plan ends</label>
            <input id="grace_days" type="number" name="grace_days" value="{{ $graceDays }}" min="0" max="30" class="form-control" style="width:120px">
        </div>
        <button class="btn btn-outline-primary" type="submit">Save</button>
    </div>
    <p class="small text-muted mt-2 mb-0">0 = access ends exactly at the end of the plan. Applies to new subscriptions.</p>
</form>
@endsection
