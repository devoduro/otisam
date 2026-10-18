<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Commerce\Payment;
use App\Models\Commerce\Plan;
use App\Services\Commerce\Checkout;
use App\Services\Commerce\PaymentFulfilment;
use App\Services\Commerce\Paystack;
use App\Services\Commerce\PaystackException;
use App\Services\Commerce\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private readonly Checkout $checkout) {}

    public function show(Request $request, Plan $plan, Paystack $paystack, SubscriptionService $subscriptions): View
    {
        abort_unless($plan->is_active && $plan->price_minor > 0, 404);

        $quote = $this->checkout->quote($plan, $request->user(), $request->query('coupon'));

        return view('account.checkout', [
            'plan' => $plan,
            'quote' => $quote,
            'couponCode' => $request->query('coupon'),
            'coveredUntil' => $subscriptions->coveredUntil($request->user()),
            'paymentsReady' => $paystack->isConfigured(),
        ]);
    }

    public function store(Request $request, Plan $plan): RedirectResponse
    {
        abort_unless($plan->is_active && $plan->price_minor > 0, 404);

        // Only these fields are read; any amount sent by the browser is ignored.
        $data = $request->validate([
            'coupon' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email:rfc', 'max:150'],
        ]);

        $quote = $this->checkout->quote($plan, $request->user(), $data['coupon'] ?? null);
        if ($quote['problem']) {
            throw ValidationException::withMessages(['coupon' => $quote['problem']]);
        }

        try {
            $payment = $this->checkout->start($plan, $request->user(), $quote['coupon'], $data['email'] ?? null, route('payments.callback'));
        } catch (PaystackException $e) {
            return back()->with('error', 'We could not start the payment: '.$e->getMessage());
        }

        if ($payment->status === 'success') {
            return redirect()->route('account.payments.show', $payment);
        }

        return redirect()->away($payment->authorization_url);
    }

    /**
     * Paystack sends the browser back here. The query string is not trusted: fulfil() asks
     * Paystack itself. The page the user then sees only reads the stored result.
     */
    public function callback(Request $request, PaymentFulfilment $fulfilment): RedirectResponse
    {
        $reference = (string) $request->query('reference', $request->query('trxref', ''));
        $payment = $reference !== '' && mb_strlen($reference) <= 60 ? $fulfilment->fulfil($reference, 'callback') : null;

        if (! $payment) {
            return redirect()->route('account.subscription')->with('error', 'We could not find that payment.');
        }

        return redirect()->route('account.payments.show', $payment);
    }

    public function payment(Request $request, Payment $payment): View
    {
        abort_unless($payment->user_id === $request->user()->id, 404);

        return view('account.payment', ['payment' => $payment->load(['plan', 'subscription', 'receipt'])]);
    }

    /** "Check again" while a mobile-money payment is waiting for approval. */
    public function check(Request $request, Payment $payment, PaymentFulfilment $fulfilment): RedirectResponse
    {
        abort_unless($payment->user_id === $request->user()->id, 404);

        if (! $payment->isFinal()) {
            $fulfilment->fulfil($payment->reference, 'callback');
        }

        return redirect()->route('account.payments.show', $payment);
    }
}
