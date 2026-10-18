<?php

namespace App\Http\Controllers\Admin\Commerce;

use App\Http\Controllers\Controller;
use App\Models\Commerce\Payment;
use App\Models\Commerce\Plan;
use App\Models\Setting;
use App\Services\Audit;
use App\Services\Commerce\Paystack;
use App\Services\Commerce\SubscriptionService;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class PlanController extends Controller
{
    public function index(Paystack $paystack): View
    {
        return view('admin.commerce.plans.index', [
            'plans' => Plan::ordered()->withCount(['payments' => fn ($q) => $q->where('status', 'success')])->get(),
            'paystackMode' => $paystack->mode(),
            'graceDays' => SubscriptionService::graceDays(),
        ]);
    }

    public function create(): View
    {
        return view('admin.commerce.plans.form', ['plan' => new Plan(['duration_months' => 3, 'currency' => config('platform.currency'), 'is_active' => false])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $plan = Plan::create($this->validated($request));
        Audit::record('commerce', 'plan.created', "Plan created: {$plan->name} ({$plan->price()})", $plan, null, $plan->only(['price_minor', 'duration_months', 'is_active']));

        return redirect()->route('admin.commerce.plans.index')->with('success', "Plan \"{$plan->name}\" saved.");
    }

    public function edit(Plan $plan): View
    {
        return view('admin.commerce.plans.form', compact('plan'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $old = $plan->only(['price_minor', 'duration_months', 'is_active']);
        $plan->update($this->validated($request));

        // Price changes affect only new checkouts; payments already started keep their amount.
        Audit::record('commerce', 'plan.updated', "Plan updated: {$plan->name} ({$plan->price()})", $plan, $old, $plan->only(['price_minor', 'duration_months', 'is_active']));

        return redirect()->route('admin.commerce.plans.index')->with('success', "Plan \"{$plan->name}\" saved.");
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if (Payment::where('plan_id', $plan->id)->exists()) {
            return back()->with('error', 'This plan has payments. Deactivate it instead.');
        }
        $plan->delete();

        return redirect()->route('admin.commerce.plans.index')->with('success', 'Plan deleted.');
    }

    public function updateGrace(Request $request): RedirectResponse
    {
        $days = $request->validate(['grace_days' => ['required', 'integer', 'min:0', 'max:30']])['grace_days'];
        Setting::put('subscriptions.grace_days', $days, 'subscriptions');
        Audit::record('commerce', 'grace.updated', "Subscription grace period set to {$days} day(s)");

        return back()->with('success', "Grace period set to {$days} day(s). It applies to subscriptions created from now on.");
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:500'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:36'],
            'price' => ['required', 'string', 'max:15'],
            'benefits' => ['nullable', 'string', 'max:2000'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        try {
            $priceMinor = Money::toMinor($data['price']);
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages(['price' => $e->getMessage()]);
        }

        if ($request->boolean('is_active') && $priceMinor < 100) {
            throw ValidationException::withMessages(['price' => 'Set a price of at least 1.00 before making the plan available.']);
        }

        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'duration_months' => (int) $data['duration_months'],
            'price_minor' => $priceMinor,
            'currency' => config('platform.currency'),
            'benefits' => array_values(array_filter(array_map('trim', preg_split('/\R/', (string) ($data['benefits'] ?? ''))))),
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'sort' => $data['sort'] ?? 0,
        ];
    }
}
