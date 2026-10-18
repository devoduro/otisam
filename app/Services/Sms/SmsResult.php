<?php

namespace App\Services\Sms;

final class SmsResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $code,
        public readonly string $message,
        public readonly ?string $providerMessageId = null,
        /** True when retrying could help (timeouts, 5xx, rate limits); false for bad numbers or config. */
        public readonly bool $retryable = false,
    ) {
    }
}
