<?php

namespace App\Services\Sms;

use App\Support\Phone;
use Illuminate\Support\Facades\Log;

/**
 * Development/test gateway: writes the message to the log instead of sending it, so OTP
 * flows can be exercised locally without spending SMS credit. Selected with SMS_PROVIDER=log.
 */
class LogSmsGateway implements SmsGateway
{
    /** @var array<int, array{phone: string, message: string}> messages "sent" in this process (tests read it) */
    public static array $sent = [];

    public function name(): string
    {
        return 'log';
    }

    public function send(string $phone, string $message): SmsResult
    {
        self::$sent[] = ['phone' => (string) Phone::clean($phone), 'message' => $message];

        Log::info('SMS (log gateway)', ['phone' => Phone::mask($phone), 'message' => $message]);

        return new SmsResult(true, '200', 'Logged', 'log-'.count(self::$sent));
    }
}
