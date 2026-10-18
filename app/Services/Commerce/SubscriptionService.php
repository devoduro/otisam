<?php

namespace App\Services\Commerce;

use App\Models\Commerce\Payment;
use App\Models\Commerce\Plan;
use App\Models\Commerce\Subscription;
use App\Models\Setting;
use App\Models\User;
use App\Services\Audit;
use Illuminate\Support\Carbon;

/**
 * Creates and changes subscriptions (BLUEPRINT §7.1).
 *
 * Stacking: buying while still covered starts the new period when the current one ends, so no
 * paid days are lost. Callers run inside a transaction.
 */
class SubscriptionService
{
    public const ENTITLEMENT = 'premium_standard';

    public function grantFromPayment(Payment $payment, string $source = 'payment'): Subscription
    {
        $plan = $payment->plan;
        [$start, $end, $grace] = $this->period($payment->user, $plan->entitlement_key, $plan->duration_months);

        $subscription = new Subscription;
        $subscription->forceFill([
            'user_id' => $payment->user_id,
            'plan_id' => $plan->id,
            'payment_id' => $payment->id,
            'entitlement_key' => $plan->entitlement_key,
            'source' => $source,
            'starts_at' => $start,
            'ends_at' => $end,
            'grace_ends_at' => $grace,
            'status' => 'active',
        ])->save();

        return $subscription;
    }

    /** Staff grant/extension, e.g. compensation or a school partnership. Always audited. */
    public function grantManually(User $user, int $months, ?Plan $plan, string $reason, User $by): Subscription
    {
        $key = $plan?->entitlement_key ?? self::ENTITLEMENT;
        [$start, $end, $grace] = $this->period($user, $key, $months);

        $subscription = new Subscription;
        $subscription->forceFill([
            'user_id' => $user->id,
            'plan_id' => $plan?->id,
            'entitlement_key' => $key,
            'source' => 'admin_grant',
            'starts_at' => $start,
            'ends_at' => $end,
            'grace_ends_at' => $grace,
            'status' => 'active',
            'granted_by' => $by->id,
            'reason' => $reason,
        ])->save();

        Audit::record('commerce', 'subscription.granted', "Granted {$months} month(s) of premium to {$user->name}", $subscription, null,
            ['user_id' => $user->id, 'months' => $months, 'starts_at' => $start->toDateTimeString(), 'ends_at' => $end->toDateTimeString(), 'reason' => $reason]);

        return $subscription;
    }

    public function revoke(Subscription $subscription, string $reason, ?User $by, string $status = 'revoked'): void
    {
        $old = $subscription->status;
        $subscription->forceFill(['status' => $status, 'reason' => $reason])->save();

        Audit::record('commerce', "subscription.{$status}", "Subscription #{$subscription->id} {$status}: {$reason}", $subscription,
            ['status' => $old], ['status' => $status, 'by' => $by?->id]);
    }

    /** The latest moment this user is covered for an entitlement (null if not covered now). */
    public function coveredUntil(User $user, string $key = self::ENTITLEMENT): ?Carbon
    {
        $ends = Subscription::where('user_id', $user->id)->where('entitlement_key', $key)
            ->where('status', 'active')->where('ends_at', '>', now())->max('ends_at');

        return $ends ? Carbon::parse($ends) : null;
    }

    public static function graceDays(): int
    {
        return max(0, (int) Setting::get('subscriptions.grace_days', 0));
    }

    /** @return array{0: Carbon, 1: Carbon, 2: ?Carbon} */
    private function period(User $user, string $key, int $months): array
    {
        $start = $this->coveredUntil($user, $key) ?? now();
        $end = $start->copy()->addMonthsNoOverflow($months);
        $grace = self::graceDays() > 0 ? $end->copy()->addDays(self::graceDays()) : null;

        return [$start, $end, $grace];
    }
}
