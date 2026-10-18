<?php

namespace App\Console\Commands;

use App\Models\Commerce\Payment;
use App\Services\Commerce\PaymentFulfilment;
use Illuminate\Console\Command;

/**
 * Safety net for lost callbacks and webhooks: re-verifies unsettled Paystack payments, and gives
 * up on attempts older than two days (BLUEPRINT §7.1). Scheduled every 10 minutes.
 */
class ReconcilePayments extends Command
{
    protected $signature = 'payments:reconcile {--hours=48 : Re-check payments started within this many hours}';

    protected $description = 'Re-verify initiated/pending Paystack payments and mark stale ones abandoned';

    public function handle(PaymentFulfilment $fulfilment): int
    {
        $hours = (int) $this->option('hours');
        $checked = 0;
        $settled = 0;

        Payment::where('provider', 'paystack')
            ->whereIn('status', ['initiated', 'pending'])
            ->where('created_at', '>=', now()->subHours($hours))
            ->where('created_at', '<=', now()->subMinutes(2)) // give the callback a moment first
            ->orderBy('id')
            ->each(function (Payment $payment) use ($fulfilment, &$checked, &$settled) {
                $checked++;
                $result = $fulfilment->fulfil($payment->reference, 'reconcile');
                if ($result && $result->isFinal()) {
                    $settled++;
                }
            });

        $abandoned = Payment::where('provider', 'paystack')
            ->whereIn('status', ['initiated', 'pending'])
            ->where('created_at', '<', now()->subHours($hours))
            ->update(['status' => 'abandoned', 'failure_reason' => "not completed within {$hours} hours", 'updated_at' => now()]);

        $this->info("Checked {$checked}, settled {$settled}, marked {$abandoned} abandoned.");

        return self::SUCCESS;
    }
}
