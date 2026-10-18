<?php

namespace App\Services\Sms;

use App\Support\Phone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Speso business SMS API (https://business.speso.co/developer), ported from jhs_portal's
 * SpesoSmsService: POST {base_url}/messages/send with a bearer token and
 * {sender_id, phone, message}.
 *
 * Delivery reports are not wired yet: the payload format of Speso's callback has not been
 * confirmed, so messages stay at "sent" (accepted by Speso) until it is.
 */
class SpesoSmsGateway implements SmsGateway
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $senderId,
        private readonly string $baseUrl,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            (string) config('services.speso.api_key'),
            (string) config('services.speso.sender_id'),
            // `?:` so a blank SPESO_BASE_URL line or a stale config cache can't leave this empty.
            rtrim((string) (config('services.speso.base_url') ?: 'https://business.speso.co/api/v1'), '/'),
        );
    }

    public function name(): string
    {
        return 'speso';
    }

    public function send(string $phone, string $message): SmsResult
    {
        $phone = (string) Phone::clean($phone);

        if ($this->apiKey === '') {
            return new SmsResult(false, 'config', 'Speso API key is not configured. Set SPESO_API_KEY in .env.');
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(15)
                ->post($this->baseUrl.'/messages/send', [
                    'sender_id' => $this->senderId,
                    'phone' => $phone,
                    'message' => $message,
                ]);
        } catch (\Throwable $e) {
            Log::warning('Speso SMS exception', ['phone' => Phone::mask($phone), 'error' => $e->getMessage()]);

            return new SmsResult(false, 'error', 'Could not reach the SMS provider.', retryable: true);
        }

        $body = $response->json() ?? [];
        $success = $response->successful() && ($body['success'] ?? true) !== false;

        if (! $success) {
            // Never log the message text: it may hold a one-time code.
            Log::warning('Speso SMS rejected', ['phone' => Phone::mask($phone), 'status' => $response->status(), 'error' => $body['message'] ?? $body['error'] ?? null]);
        }

        $id = $body['data']['id'] ?? $body['data']['message_id'] ?? $body['message_id'] ?? $body['id'] ?? null;

        return new SmsResult(
            $success,
            (string) $response->status(),
            (string) ($body['message'] ?? $body['error'] ?? ($success ? 'Message submitted' : 'SMS sending failed')),
            $id !== null ? (string) $id : null,
            retryable: ! $success && ($response->serverError() || $response->status() === 429),
        );
    }
}
