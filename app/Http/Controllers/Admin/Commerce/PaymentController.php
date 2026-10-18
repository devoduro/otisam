<?php

namespace App\Http\Controllers\Admin\Commerce;

use App\Http\Controllers\Controller;
use App\Models\Commerce\Payment;
use App\Services\Audit;
use App\Services\Commerce\PaymentFulfilment;
use App\Support\Money;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(Payment::STATUSES))],
            'q' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $query = Payment::with(['user', 'plan'])
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['q'] ?? null, function ($q, $s) {
                $phone = Phone::clean($s);
                $q->where(fn ($w) => $w->where('reference', 'like', "%{$s}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")
                        ->when($phone && strlen($phone) >= 4, fn ($p) => $p->orWhere('phone', 'like', "%{$phone}%"))));
            })
            ->when($filters['from'] ?? null, fn ($q, $d) => $q->where('created_at', '>=', $d))
            ->when($filters['to'] ?? null, fn ($q, $d) => $q->where('created_at', '<', Carbon::parse($d)->addDay()));

        $totals = (clone $query)->where('status', 'success')->selectRaw('COUNT(*) as n, COALESCE(SUM(amount_minor),0) as total')->first();

        return view('admin.commerce.payments.index', [
            'payments' => $query->latest()->paginate(30)->withQueryString(),
            'filters' => $filters,
            'totals' => $totals,
            'stalePending' => Payment::whereIn('status', ['initiated', 'pending'])->where('provider', 'paystack')->where('created_at', '<', now()->subMinutes(30))->count(),
        ]);
    }

    public function show(Payment $payment): View
    {
        return view('admin.commerce.payments.show', ['payment' => $payment->load(['user', 'plan', 'coupon', 'subscription', 'receipt', 'refunds.recorder'])]);
    }

    /** Ask Paystack again (e.g. a customer says they paid but nothing happened). */
    public function reverify(Payment $payment, PaymentFulfilment $fulfilment): RedirectResponse
    {
        $before = $payment->status;
        $result = $fulfilment->fulfil($payment->reference, 'admin');
        Audit::record('commerce', 'payment.reverified', "Re-verified {$payment->reference}: {$before} → {$result?->status}", $payment);

        return back()->with('success', "Checked with Paystack: {$result?->statusLabel()}.");
    }

    public function refund(Request $request, Payment $payment, PaymentFulfilment $fulfilment): RedirectResponse
    {
        abort_unless($payment->status === 'success', 422);

        $data = $request->validate([
            'amount' => ['required', 'string', 'max:15'],
            'reason' => ['required', 'string', 'max:255'],
            'revoke_access' => ['boolean'],
        ]);

        try {
            $amount = Money::toMinor($data['amount']);
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages(['amount' => $e->getMessage()]);
        }
        if ($amount < 1 || $amount > $payment->amount_minor) {
            throw ValidationException::withMessages(['amount' => 'The refund must be between 0.01 and '.Money::toDecimal($payment->amount_minor).'.']);
        }

        $fulfilment->refund($payment, $amount, $data['reason'], $request->boolean('revoke_access'), $request->user());

        return back()->with('success', 'Refund recorded. Make the actual refund in the Paystack dashboard if you have not already.');
    }
}
