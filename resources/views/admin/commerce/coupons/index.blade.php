@extends('layouts.admin')

@section('title', 'Coupons')

@section('actions')
    <a href="{{ route('admin.commerce.coupons.create') }}" class="btn btn-premium rounded-pill px-4">Add coupon</a>
@endsection

@section('content')
<div class="table-panel">
    <table class="table table-hover align-middle table-stack">
        <thead><tr><th>Code</th><th>Discount</th><th>Plan</th><th>Valid</th><th>Used</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
            @forelse ($coupons as $c)
                <tr>
                    <td data-label="Code" class="fw-semibold font-monospace">{{ $c->code }}</td>
                    <td data-label="Discount">{{ $c->type === 'percent' ? $c->value.'%' : \App\Support\Money::format($c->value) }}</td>
                    <td data-label="Plan">{{ $c->plan?->name ?? 'Any' }}</td>
                    <td data-label="Valid" class="small">{{ $c->starts_at?->format('j M Y') ?? 'now' }} – {{ $c->ends_at?->format('j M Y') ?? 'no end' }}</td>
                    <td data-label="Used">{{ $c->redemptions_count }}{{ $c->max_redemptions ? ' / '.$c->max_redemptions : '' }}</td>
                    <td data-label="Status"><span class="badge text-bg-{{ $c->is_active ? 'success' : 'secondary' }}">{{ $c->is_active ? 'Active' : 'Off' }}</span></td>
                    <td data-label="Actions" class="text-end"><a href="{{ route('admin.commerce.coupons.edit', $c) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No coupons.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $coupons->links() }}</div>
@endsection
