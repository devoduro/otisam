<?php

namespace App\Services\Sms;

interface SmsGateway
{
    /**
     * Hand one message to the provider.
     *
     * `success` means the provider accepted it, not that it reached the handset.
     */
    public function send(string $phone, string $message): SmsResult;

    /** Short provider name stored on notification logs, e.g. "speso". */
    public function name(): string;
}
