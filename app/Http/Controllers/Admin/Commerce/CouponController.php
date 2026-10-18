<?php

namespace App\Http\Controllers\Admin\Commerce;

use App\Http\Controllers\Controller;
use App\Models\Commerce\Coupon;
use App\Models\Commerce\Plan;
use App\Services\Audit;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        return view('admin.commerce.coupons.index', [
            'coupons' => Coupon::with('plan')->withCount('redemptions')->latest()->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('admin.commerce.coupons.form', ['coupon' => new Coupon(['type' => 'percent', 'per_user_limit' => 1, 'is_active' => true]), 'plans' => Plan::ordered()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $coupon = Coupon::create($this->validated($request));
        Audit::record('commerce', 'coupon.created', "Coupon created: {$coupon->code}", $coupon);

        return redirect()->route('admin.commerce.coupons.index')->with('success', "Coupon {$coupon->code} saved.");
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.commerce.coupons.form', ['coupon' => $coupon, 'plans' => Plan::ordered()->get()]);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($this->validated($request, $coupon));
        Audit::record('commerce', 'coupon.updated', "Coupon updated: {$coupon->code}", $coupon);

        return redirect()->route('admin.commerce.coupons.index')->with('success', "Coupon {$coupon->code} saved.");
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Coupon $coupon = null): array
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);

        $data = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:40', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Coupon::TYPES))],
            'value' => ['required', 'string', 'max:15'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'max_redemptions' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        if ($data['type'] === 'percent') {
            $value = (int) $data['value'];
            if ($value < 1 || $value > 100 || (string) $value !== trim($data['value'])) {
                throw ValidationException::withMessages(['value' => 'Enter a whole percentage from 1 to 100.']);
            }
        } else {
            try {
                $value = Money::toMinor($data['value']);
            } catch (\InvalidArgumentException $e) {
                throw ValidationException::withMessages(['value' => $e->getMessage()]);
            }
        }

        return ['value' => $value, 'is_active' => $request->boolean('is_active')] + $data;
    }
}
