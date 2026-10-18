@extends('layouts.admin')

@section('title', $coupon->exists ? 'Edit coupon '.$coupon->code : 'Add coupon')

@section('content')
<form method="POST" action="{{ $coupon->exists ? route('admin.commerce.coupons.update', $coupon) : route('admin.commerce.coupons.store') }}" class="card-panel" style="max-width:820px">
    @csrf
    @if ($coupon->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-4">
            <label for="code" class="form-label fw-semibold">Code</label>
            <input id="code" name="code" value="{{ old('code', $coupon->code) }}" class="form-control text-uppercase" required maxlength="40">
        </div>
        <div class="col-md-4">
            <label for="type" class="form-label fw-semibold">Type</label>
            <select id="type" name="type" class="form-select">
                @foreach (\App\Models\Commerce\Coupon::TYPES as $v => $l)<option value="{{ $v }}" @selected(old('type', $coupon->type) === $v)>{{ $l }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="value" class="form-label fw-semibold">Value</label>
            <input id="value" name="value" value="{{ old('value', $coupon->exists ? ($coupon->type === 'percent' ? $coupon->value : \App\Support\Money::toDecimal($coupon->value)) : '') }}" class="form-control" required placeholder="10 (%) or 20.00 ({{ config('platform.currency') }})">
        </div>
        <div class="col-12">
            <label for="description" class="form-label fw-semibold">Note <span class="text-muted fw-normal">(internal)</span></label>
            <input id="description" name="description" value="{{ old('description', $coupon->description) }}" class="form-control" maxlength="255" placeholder="e.g. Partner school launch offer">
        </div>
        <div class="col-md-4">
            <label for="plan_id" class="form-label fw-semibold">Plan</label>
            <select id="plan_id" name="plan_id" class="form-select">
                <option value="">Any plan</option>
                @foreach ($plans as $p)<option value="{{ $p->id }}" @selected((int) old('plan_id', $coupon->plan_id) === $p->id)>{{ $p->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-4">
            <label for="starts_at" class="form-label fw-semibold">Valid from</label>
            <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}" class="form-control">
        </div>
        <div class="col-6 col-md-4">
            <label for="ends_at" class="form-label fw-semibold">Valid until</label>
            <input id="ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $coupon->ends_at?->format('Y-m-d\TH:i')) }}" class="form-control">
        </div>
        <div class="col-6 col-md-4">
            <label for="max_redemptions" class="form-label fw-semibold">Total uses</label>
            <input id="max_redemptions" type="number" name="max_redemptions" value="{{ old('max_redemptions', $coupon->max_redemptions) }}" class="form-control" min="1" placeholder="Unlimited">
        </div>
        <div class="col-6 col-md-4">
            <label for="per_user_limit" class="form-label fw-semibold">Uses per person</label>
            <input id="per_user_limit" type="number" name="per_user_limit" value="{{ old('per_user_limit', $coupon->per_user_limit) }}" class="form-control" min="1" required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $coupon->is_active))><label class="form-check-label" for="is_active">Active</label></div>
        </div>
    </div>
    <p class="small text-muted mt-3 mb-0">A 100% coupon activates access without a payment.</p>
    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-premium" type="submit">Save coupon</button>
        <a href="{{ route('admin.commerce.coupons.index') }}" class="btn btn-light">Cancel</a>
    </div>
</form>
@endsection
