@extends('layouts.admin')

@section('title', $plan->exists ? 'Edit plan: '.$plan->name : 'Add plan')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <form method="POST" action="{{ $plan->exists ? route('admin.commerce.plans.update', $plan) : route('admin.commerce.plans.store') }}" class="card-panel">
            @csrf
            @if ($plan->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">Name</label>
                    <input id="name" name="name" value="{{ old('name', $plan->name) }}" class="form-control" required maxlength="80">
                </div>
                <div class="col-6 col-md-3">
                    <label for="duration_months" class="form-label fw-semibold">Months</label>
                    <input id="duration_months" type="number" name="duration_months" value="{{ old('duration_months', $plan->duration_months) }}" class="form-control" min="1" max="36" required>
                </div>
                <div class="col-6 col-md-3">
                    <label for="price" class="form-label fw-semibold">Price ({{ config('platform.currency') }})</label>
                    <input id="price" name="price" inputmode="decimal" value="{{ old('price', \App\Support\Money::toDecimal((int) $plan->price_minor)) }}" class="form-control" required placeholder="e.g. 120.00">
                </div>
                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">Short description</label>
                    <input id="description" name="description" value="{{ old('description', $plan->description) }}" class="form-control" maxlength="500">
                </div>
                <div class="col-12">
                    <label for="benefits" class="form-label fw-semibold">Benefits <span class="text-muted fw-normal">(one per line, shown on the plans page)</span></label>
                    <textarea id="benefits" name="benefits" rows="4" class="form-control" maxlength="2000">{{ old('benefits', implode("\n", (array) $plan->benefits)) }}</textarea>
                </div>
                <div class="col-6 col-md-3">
                    <label for="sort" class="form-label fw-semibold">Order</label>
                    <input id="sort" type="number" name="sort" value="{{ old('sort', $plan->sort ?? 0) }}" class="form-control" min="0">
                </div>
                <div class="col-md-9 d-flex flex-wrap gap-4 align-items-end">
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $plan->is_active))><label class="form-check-label" for="is_active">On sale</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $plan->is_featured))><label class="form-check-label" for="is_featured">Highlight as best value</label></div>
                </div>
            </div>
            <p class="small text-muted mt-3 mb-0">All plans unlock the same premium library. A price change applies to new checkouts only.</p>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-premium" type="submit">Save plan</button>
                <a href="{{ route('admin.commerce.plans.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
    @if ($plan->exists)
        <div class="col-lg-4">
            <div class="card-panel">
                <h2 class="h6 fw-bold mb-2">Delete</h2>
                <p class="small text-muted">Only possible before anyone has bought it. Otherwise take it off sale.</p>
                <form method="POST" action="{{ route('admin.commerce.plans.destroy', $plan) }}">@csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" type="submit" data-confirm="Delete {{ $plan->name }}?">Delete plan</button></form>
            </div>
        </div>
    @endif
</div>
@endsection
