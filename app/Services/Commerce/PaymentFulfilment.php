<?php

namespace App\Services\Commerce;

use App\Models\Commerce\CouponRedemption;
use App\Models\Commerce\Payment;
use App\Models\Commerce\Receipt;
use App\Models\Commerce\Refund;
use App\Models\User;
use App\Services\Audit;
use App\Services\Notifications\Notifier;
use App\Support\Money;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * The ONE place a payment becomes a subscription (BLUEPRINT §7.1). The browser callback, the
 * Paystack webhook and the reconciliation job all call fulfil(); whichever arrives first does the
 * work and the others find the payment already settled.
 *
 * Rules:
 *  1. The outcome comes from Paystack's verify API, never from the browser or the webhook body.
 *  2. Reference, amount and currency must match what we recorded when the payment started.
 *  3. The payment row is locked (SELECT … FOR UPDATE) while it is settled; a payment that is
 *     already "success" is returned untouched.
 *  4. subscriptions.payment_id is unique, so even a logic error cannot grant twice.
 *  5. Messages go out only after the transaction commits, and a failed SMS never undoes a payment.
 */
class PaymentFulfilment
{
    /** Paystack transaction states that are still in progress (e.g. waiting for MoMo approval). */
    private const IN_PROGRESS = ['ongoing', 'pending', 'processing', 'queued', 'send_birthday', 'send_otp', 'send_pin', 'send_phone', 'send_address', 'open_url'];

    private const FAILED = ['failed', 'reversed'];

    public function __construct(
        private readonly Paystack $paystack,
        private readonly SubscriptionService $subscriptions,
        private readonly Notifier $notifier,
    ) {}

    /**
     * Verify with Paystack and settle the payment. Returns null for unknown references.
     */
    public function fulfil(string $reference, string $source): ?Payment
    {
        $payment = Payment::where('reference', $reference)->first();
        if (! $payment) {
            Log::warning('Payment reference not found', ['reference' => mb_substr($reference, 0, 60), 'source' => $source]);

            return null;
        }
        if ($payment->status === 'success' || $payment->provider !== 'paystack') {
            return $payment;
        }

        try {
            $data = $this->paystack->verify($reference); // outside the lock: network calls can be slow
        } catch (PaystackException $e) {
            Log::info('Paystack verify not conclusive', ['reference' => $reference, 'source' => $source, 'error' => $e->getMessage()]);

            return $payment;
        }

        $succeeded = DB::transaction(function () use ($payment, $data, $source) {
            /** @var Payment $locked */
            $locked = Payment::whereKey($payment->id)->lockForUpdate()->first();

            if ($locked->status === 'success') {
                return false; // someone else settled it while we were verifying
            }

            $status = (string) ($data['status'] ?? '');
            $payload = Arr::only($data, ['id', 'status', 'reference', 'amount', 'currency', 'channel', 'paid_at', 'gateway_response', 'fees']);

            if ($status === 'success') {
                $mismatch = $this->mismatch($locked, $data);
                if ($mismatch) {
                    $locked->forceFill(['status' => 'failed', 'failure_reason' => $mismatch, 'provider_payload' => $payload, 'verified_at' => now(), 'verification_source' => $source])->save();
                    Log::critical('Paystack verification mismatch', ['reference' => $locked->reference, 'problem' => $mismatch]);
                    Audit::record('commerce', 'payment.mismatch', "Payment {$locked->reference} rejected: {$mismatch}", $locked);

                    return false;
                }

                $locked->forceFill([
                    'status' => 'success',
                    'provider_transaction_id' => isset($data['id']) ? (string) $data['id'] : null,
                    'channel' => $data['channel'] ?? null,
                    'paid_at' => isset($data['paid_at']) ? Carbon::parse($data['paid_at']) : now(),
                    'verified_at' => now(),
                    'verification_source' => $source,
                    'provider_payload' => $payload,
                    'failure_reason' => null,
                ])->save();

                $this->complete($locked);

                return true;
            }

            if (in_array($status, self::FAILED, true) || $status === 'abandoned') {
                $locked->forceFill([
                    'status' => $status === 'abandoned' ? 'abandoned' : 'failed',
                    'failure_reason' => mb_substr((string) ($data['gateway_response'] ?? $status), 0, 255),
                    'provider_payload' => $payload, 'verified_at' => now(), 'verification_source' => $source,
                ])->save();
            } elseif (in_array($status, self::IN_PROGRESS, true) && $locked->status === 'initiated') {
                $locked->forceFill(['status' => 'pending', 'provider_payload' => $payload])->save();
            }

            return false;
        });

        $payment->refresh();

        if ($succeeded) {
            $this->notifySuccess($payment);
        } elseif (in_array($payment->status, ['failed', 'abandoned'], true) && $payment->verification_source === $source) {
            $this->notifier->toUser($payment->user, 'payment.failed', 'transactional', 'Payment not completed',
                "Your payment of {$payment->amount()} for {$payment->plan->name} was not completed. You can try again at any time.",
                "payment.failed:{$payment->id}");
        }

        return $payment;
    }

    /** Coupon made the price zero: settle without Paystack. Caller holds a transaction. */
    public function completeFree(Payment $payment): void
    {
        $payment->forceFill(['status' => 'success', 'paid_at' => now(), 'verified_at' => now(), 'verification_source' => 'coupon'])->save();
        $this->complete($payment, 'coupon_free');
        DB::afterCommit(fn () => $this->notifySuccess($payment->fresh()));
    }

    /**
     * Record a refund. By default access is removed (BLUEPRINT §7.1); staff may keep it with a reason.
     */
    public function refund(Payment $payment, int $amountMinor, string $reason, bool $revokeAccess, ?User $by, ?string $providerReference = null): ?Refund
    {
        return DB::transaction(function () use ($payment, $amountMinor, $reason, $revokeAccess, $by, $providerReference) {
            if ($providerReference && Refund::where('provider_reference', $providerReference)->exists()) {
                return null; // already recorded (webhook delivered twice)
            }

            $refund = new Refund;
            $refund->forceFill([
                'payment_id' => $payment->id, 'amount_minor' => $amountMinor, 'provider_reference' => $providerReference,
                'reason' => $reason, 'revoked_access' => $revokeAccess, 'recorded_by' => $by?->id,
            ])->save();

            if ($amountMinor >= $payment->amount_minor) {
                $payment->forceFill(['status' => 'refunded'])->save();
            }
            if ($revokeAccess && $payment->subscription && $payment->subscription->status === 'active') {
                $this->subscriptions->revoke($payment->subscription, "Refund: {$reason}", $by, 'refunded');
            }

            Audit::record('commerce', 'payment.refunded', 'Refund of '.Money::format($amountMinor, $payment->currency)." recorded for {$payment->reference}", $payment,
                null, ['amount_minor' => $amountMinor, 'revoked_access' => $revokeAccess, 'reason' => $reason]);

            return $refund;
        });
    }

    private function complete(Payment $payment, string $source = 'payment'): void
    {
        $this->subscriptions->grantFromPayment($payment, $source);

        if ($payment->coupon_id) {
            CouponRedemption::create(['coupon_id' => $payment->coupon_id, 'user_id' => $payment->user_id, 'payment_id' => $payment->id]);
        }

        $receipt = new Receipt;
        $receipt->forceFill(['payment_id' => $payment->id, 'issued_at' => now()])->save();
        $receipt->forceFill(['number' => 'OT-'.now()->format('Y').'-'.str_pad((string) $receipt->id, 6, '0', STR_PAD_LEFT)])->save();

        Audit::record('commerce', 'payment.success', "Payment {$payment->reference} confirmed ({$payment->amount()}) via {$payment->verification_source}", $payment);
    }

    private function notifySuccess(Payment $payment): void
    {
        $subscription = $payment->subscription;
        $until = $subscription?->ends_at?->format('j M Y');
        $app = config('app.name');

        $this->notifier->toUser($payment->user, 'payment.success', 'transactional', "{$app}: payment received",
            "Payment of {$payment->amount()} received for {$payment->plan->name}. Premium access is active until {$until}. Receipt: {$payment->receipt?->number}.",
            "payment.success:{$payment->id}");
    }

    /** Why a "success" from Paystack does not match our record, or null. */
    private function mismatch(Payment $payment, array $data): ?string
    {
        return match (true) {
            ($data['reference'] ?? null) !== $payment->reference => 'reference mismatch',
            (int) ($data['amount'] ?? -1) !== $payment->amount_minor => 'amount mismatch (expected '.$payment->amount_minor.', got '.(int) ($data['amount'] ?? 0).')',
            strtoupper((string) ($data['currency'] ?? '')) !== $payment->currency => 'currency mismatch',
            default => null,
        };
    }
}
