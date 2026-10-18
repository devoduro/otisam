<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPaystackWebhook;
use App\Models\Commerce\PaymentWebhookEvent;
use App\Services\Commerce\Paystack;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * POST /webhooks/paystack. Checks the signature on the raw body, stores the event once (unique
 * dedupe key), answers 200 straight away and leaves the work to a queued job, which re-verifies
 * with Paystack before changing anything.
 */
class PaystackWebhookController extends Controller
{
    public function __invoke(Request $request, Paystack $paystack): Response
    {
        $allowedIps = config('services.paystack.webhook_ips');
        if ($allowedIps && ! in_array($request->ip(), $allowedIps, true)) {
            Log::warning('Paystack webhook from unexpected IP', ['ip' => $request->ip()]);

            return response('', 403);
        }

        $raw = $request->getContent();
        if (! $paystack->validSignature($raw, $request->header('x-paystack-signature'))) {
            Log::warning('Paystack webhook with invalid signature', ['ip' => $request->ip()]);

            return response('', 401);
        }

        $payload = json_decode($raw, true);
        if (! is_array($payload) || empty($payload['event'])) {
            return response('', 400);
        }

        $data = (array) ($payload['data'] ?? []);
        $reference = $data['reference'] ?? $data['transaction_reference'] ?? ($data['transaction']['reference'] ?? null);
        $dedupe = hash('sha256', $payload['event'].'|'.($data['id'] ?? '').'|'.($reference ?? '').'|'.($data['status'] ?? ''));

        try {
            $event = new PaymentWebhookEvent;
            $event->forceFill([
                'provider' => 'paystack',
                'event_type' => mb_substr((string) $payload['event'], 0, 60),
                'dedupe_key' => $dedupe,
                'reference' => $reference ? mb_substr((string) $reference, 0, 60) : null,
                'payload' => $payload,
                'received_at' => now(),
            ])->save();
        } catch (UniqueConstraintViolationException) {
            return response('', 200); // duplicate delivery: already stored, nothing more to do
        }

        ProcessPaystackWebhook::dispatch($event->id);

        return response('', 200);
    }
}
