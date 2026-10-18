<?php

namespace App\Services\Commerce;

use App\Models\Commerce\Coupon;
use App\Models\Commerce\Payment;
use App\Models\Commerce\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Starts a purchase. The price always comes from the database (plan minus coupon), never from
 * the browser.
 */
class Checkout
{
    public function __construct(
        private readonly Paystack $paystack,
        private readonly PaymentFulfilment $fulfilment,
    ) {}

    /** @return array{list: int, discount: int, total: int, coupon: ?Coupon, problem: ?string} */
    public function quote(Plan $plan, User $user, ?string $couponCode): array
    {
        $coupon = null;
        $problem = null;
        $discount = 0;

        if ($couponCode !== null && trim($couponCode) !== '') {
            $coupon = Coupon::where('code', strtoupper(trim($couponCode)))->first();
            $problem = $coupon ? $coupon->problemFor($plan, $user) : 'That code was not found.';
            if ($problem) {
                $coupon = null;
            } else {
                $discount = $coupon->discountFor($plan->price_minor);
            }
        }

        return ['list' => $plan->price_minor, 'discount' => $discount, 'total' => $plan->price_minor - $discount, 'coupon' => $coupon, 'problem' => $problem];
    }

    /**
     * Create the payment record and either return Paystack's checkout URL, or (when a coupon makes
     * it free) activate the subscription straight away and return null.
     *
     * @throws PaystackException
     */
    public function start(Plan $plan, User $user, ?Coupon $coupon, ?string $email, string $callbackUrl): Payment
    {
        $list = $plan->price_minor;
        $discount = $coupon?->discountFor($list) ?? 0;
        $total = $list - $discount;

        $payment = new Payment;
        $payment->forceFill([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'reference' => 'OT-'.Str::upper((string) Str::ulid()),
            'provider' => $total === 0 ? 'coupon' : 'paystack',
            'amount_minor' => $total,
            'list_price_minor' => $list,
            'discount_minor' => $discount,
            'coupon_id' => $coupon?->id,
            'currency' => $plan->currency,
            'status' => 'initiated',
            'customer_email' => $email ?: ($user->email ?: "user{$user->id}@".config('services.paystack.placeholder_email_domain')),
        ])->save();

        if ($total === 0) {
            // Nothing to charge: activate now. Paystack cannot take a zero amount.
            DB::transaction(fn () => $this->fulfilment->completeFree($payment));

            return $payment->fresh();
        }

        try {
            $url = $this->paystack->initialize($payment->customer_email, $total, $payment->currency, $payment->reference, $callbackUrl, [
                'payment_id' => $payment->ulid,
                'plan' => $plan->slug,
                'cancel_action' => $callbackUrl.'?reference='.$payment->reference,
            ]);
        } catch (PaystackException $e) {
            $payment->forceFill(['status' => 'failed', 'failure_reason' => 'initialise: '.mb_substr($e->getMessage(), 0, 200)])->save();

            throw $e;
        }

        $payment->forceFill(['authorization_url' => $url])->save();

        return $payment;
    }
}
