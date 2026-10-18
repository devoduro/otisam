@extends('layouts.admin')

@section('title', 'Subscribers')
@section('subtitle', $activeCount.' people with premium access now')

@section('content')
<div class="row g-3">
    <div class="col-xl-8">
        <ul class="nav nav-pills mb-3">
            @foreach (['active' => 'Active now', 'expired' => 'Expired', 'revoked' => 'Revoked / refunded'] as $v => $l)
                <li class="nav-item"><a class="nav-link {{ $filter === $v ? 'active' : '' }}" href="{{ route('admin.commerce.subscriptions.index', ['state' => $v]) }}">{{ $l }}</a></li>
            @endforeach
        </ul>
        <div class="table-panel">
            <table class="table align-middle table-stack">
                <thead><tr><th>Person</th><th>Plan / source</th><th>Period</th><th>State</th>@can('subscriptions.adjust')<th></th>@endcan</tr></thead>
                <tbody>
                    @forelse ($subscriptions as $s)
                        <tr>
                            <td data-label="Person">{{ $s->user->name }}<div class="small text-muted">{{ $s->user->phone }}</div></td>
                            <td data-label="Plan" class="small">{{ $s->plan?->name ?? '—' }}<br>{{ \App\Models\Commerce\Subscription::SOURCES[$s->source] ?? $s->source }}{{ $s->grantedBy ? ' by '.$s->grantedBy->name : '' }}</td>
                            <td data-label="Period" class="small text-nowrap">{{ $s->starts_at->format('j M Y') }} – {{ $s->ends_at->format('j M Y') }}</td>
                            <td data-label="State"><span class="badge text-bg-light border">{{ $s->stateLabel() }}</span>@if ($s->reason)<div class="small text-muted">{{ \Illuminate\Support\Str::limit($s->reason, 60) }}</div>@endif</td>
                            @can('subscriptions.adjust')
                                <td class="text-end">
                                    @if ($s->status === 'active' && $s->accessEndsAt()->isFuture())
                                        <form method="POST" action="{{ route('admin.commerce.subscriptions.revoke', $s) }}" class="d-flex gap-1">
                                            @csrf
                                            <label class="visually-hidden" for="reason-{{ $s->id }}">Reason</label>
                                            <input id="reason-{{ $s->id }}" name="reason" class="form-control form-control-sm" placeholder="Reason" required maxlength="255" style="max-width:140px">
                                            <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="End this person's premium access now?">Revoke</button>
                                        </form>
                                    @endif
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">None.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $subscriptions->links() }}</div>
    </div>
    @can('subscriptions.adjust')
        <div class="col-xl-4">
            <form method="POST" action="{{ route('admin.commerce.subscriptions.grant') }}" class="card-panel">
                @csrf
                <h2 class="h6 fw-bold mb-2">Grant or extend access</h2>
                <p class="small text-muted">For compensation, partner schools or support cases. Added after any access the person already has. Always recorded in the audit log.</p>
                <label for="login" class="form-label small fw-semibold">Student/teacher phone or email</label>
                <input id="login" name="login" value="{{ old('login') }}" class="form-control form-control-sm mb-2" required>
                <div class="row g-2 mb-2">
                    <div class="col-5"><label for="months" class="form-label small fw-semibold">Months</label><input id="months" type="number" name="months" value="{{ old('months', 1) }}" min="1" max="24" class="form-control form-control-sm" required></div>
                    <div class="col-7"><label for="plan_id" class="form-label small fw-semibold">Plan (optional)</label>
                        <select id="plan_id" name="plan_id" class="form-select form-select-sm"><option value="">—</option>@foreach ($plans as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
                </div>
                <label for="reason" class="form-label small fw-semibold">Reason</label>
                <input id="reason" name="reason" value="{{ old('reason') }}" class="form-control form-control-sm mb-3" required maxlength="255">
                <button class="btn btn-premium btn-sm" type="submit">Grant access</button>
            </form>
        </div>
    @endcan
</div>
@endsection
