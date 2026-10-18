<?php

namespace App\Services\Commerce;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin Paystack REST client: initialise, verify, check webhook signatures.
 * The secret key never leaves the server (it is only sent to api.paystack.co).
 */
class Paystack
{
    public function isConfigured(): bool
    {
        return (string) config('services.paystack.secret_key') !== '';
    }

    /** "test", "live" or "not configured", for admin screens. Never shows the key. */
    public function mode(): string
    {
        $key = (string) config('services.paystack.secret_key');

        return match (true) {
            $key === '' => 'not configured',
            str_starts_with($key, 'sk_live_') => 'live',
            str_starts_with($key, 'sk_test_') => 'test',
            default => 'configured',
        };
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return string the checkout URL to redirect the customer to
     *
     * @throws PaystackException
     */
    public function initialize(string $email, int $amountMinor, string $currency, string $reference, string $callbackUrl, array $metadata = []): string
    {
        $data = $this->request('post', '/transaction/initialize', [
            'email' => $email,
            'amount' => $amountMinor, // Paystack takes the amount in the subunit (pesewas)
            'currency' => $currency,
            'reference' => $reference,
            'callback_url' => $callbackUrl,
            'metadata' => $metadata,
        ]);

        if (empty($data['authorization_url'])) {
            throw new PaystackException('Paystack did not return a checkout link.');
        }

        return (string) $data['authorization_url'];
    }

    /**
     * The authoritative state of a transaction, straight from Paystack.
     *
     * @return array<string, mixed> Paystack's `data` object (status, reference, amount, currency, id, channel, paid_at...)
     *
     * @throws PaystackException
     */
    public function verify(string $reference): array
    {
        return $this->request('get', '/transaction/verify/'.rawurlencode($reference));
    }

    /** x-paystack-signature is HMAC-SHA512 of the raw request body with the secret key. */
    public function validSignature(string $rawBody, ?string $signature): bool
    {
        $secret = (string) config('services.paystack.secret_key');

        return $secret !== '' && $signature !== null && $signature !== ''
            && hash_equals(hash_hmac('sha512', $rawBody, $secret), $signature);
    }

    /** @return array<string, mixed> */
    private function request(string $method, string $path, array $payload = []): array
    {
        if (! $this->isConfigured()) {
            throw new PaystackException('Online payments are not set up yet.');
        }

        try {
            $response = Http::withToken((string) config('services.paystack.secret_key'))
                ->acceptJson()
                ->timeout(20)
                ->retry(2, 500, fn ($e) => $e instanceof ConnectionException, throw: false)
                ->{$method}(rtrim((string) config('services.paystack.base_url'), '/').$path, $payload);
        } catch (\Throwable $e) {
            Log::warning('Paystack request failed', ['path' => $path, 'error' => $e->getMessage()]);

            throw new PaystackException('Could not reach Paystack. Please try again.', previous: $e);
        }

        $body = $response->json() ?? [];

        if (! $response->successful() || ($body['status'] ?? false) !== true) {
            Log::warning('Paystack error response', ['path' => $path, 'http' => $response->status(), 'message' => $body['message'] ?? null]);

            throw new PaystackException((string) ($body['message'] ?? 'Paystack returned an error.'));
        }

        return (array) ($body['data'] ?? []);
    }
}
