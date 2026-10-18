<?php

namespace App\Jobs;

use App\Models\Commerce\Payment;
use App\Models\Commerce\PaymentWebhookEvent;
use App\Services\Commerce\PaymentFulfilment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPaystackWebhook implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var array<int, int> */
    public array $backoff = [60, 300, 900, 3600];

    public function __construct(public readonly int $eventId) {}

    public function handle(PaymentFulfilment $fulfilment): void
    {
        $event = PaymentWebhookEvent::find($this->eventId);
        if (! $event || $event->processed_at) {
            return;
        }

        $event->forceFill(['attempts' => $event->attempts + 1])->save();
        $data = (array) ($event->payload['data'] ?? []);

        try {
            match ($event->event_type) {
                // The webhook body is only a hint: fulfil() asks Paystack for the real state.
                'charge.success' => $event->reference ? $fulfilment->fulfil($event->reference, 'webhook') : null,
                'refund.processed' => $this->refund($fulfilment, $event, $data),
                default => null, // stored for the record, nothing to do
            };
        } catch (\Throwable $e) {
            $event->forceFill(['error' => mb_substr($e->getMessage(), 0, 250)])->save();

            throw $e;
        }

        $event->forceFill(['processed_at' => now(), 'error' => null])->save();
    }

    private function refund(PaymentFulfilment $fulfilment, PaymentWebhookEvent $event, array $data): void
    {
        $payment = $event->reference ? Payment::where('reference', $event->reference)->first() : null;
        if (! $payment || $payment->status !== 'success') {
            return;
        }

        $fulfilment->refund($payment, (int) ($data['amount'] ?? $payment->amount_minor), 'Refund processed by Paystack', true, null,
            isset($data['id']) ? 'paystack-refund-'.$data['id'] : null);
    }
}
