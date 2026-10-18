<?php

namespace App\Services\Auth;

use App\Models\PhoneVerification;
use App\Services\Notifications\Notifier;
use App\Support\Phone;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Six-digit SMS codes for phone verification and password reset.
 *
 * - Only a hash of the code is stored; codes expire after platform.otp.ttl_minutes.
 * - A code allows platform.otp.max_attempts wrong guesses, then it stops working.
 * - Sending is limited per phone (one a minute, max_per_hour an hour) and per IP.
 * - Sent synchronously, not queued: on shared hosting the queue runs from cron once a minute,
 *   which is too slow for a code the user is waiting for.
 */
class PhoneOtpService
{
    public const PURPOSE_VERIFY = 'verify';

    public const PURPOSE_RESET = 'reset';

    public function __construct(private readonly Notifier $notifier) {}

    /**
     * @return array{sent: bool, retry_after: int, error: ?string} error is "too_many" or "gateway"
     */
    public function send(string $phone, string $purpose, ?int $userId = null): array
    {
        $phone = (string) Phone::clean($phone);
        $ip = (string) request()?->ip();

        $limits = [
            "otp-minute:{$phone}" => [1, 60],
            "otp-hour:{$phone}" => [(int) config('platform.otp.max_per_hour'), 3600],
            "otp-ip:{$ip}" => [(int) config('platform.otp.max_per_ip_hour'), 3600],
        ];

        foreach ($limits as $key => [$max]) {
            if (RateLimiter::tooManyAttempts($key, $max)) {
                return ['sent' => false, 'retry_after' => RateLimiter::availableIn($key), 'error' => 'too_many'];
            }
        }

        $code = (string) random_int(100000, 999999);

        // An earlier unused code for the same purpose stops working once a new one is issued.
        PhoneVerification::where('phone', $phone)->where('purpose', $purpose)->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        PhoneVerification::create([
            'phone' => $phone,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes((int) config('platform.otp.ttl_minutes')),
            'ip_address' => $ip,
        ]);

        $app = config('app.name');
        $minutes = (int) config('platform.otp.ttl_minutes');
        $message = $purpose === self::PURPOSE_RESET
            ? "{$code} is your {$app} password reset code. It expires in {$minutes} minutes. If you did not ask for it, ignore this message."
            : "{$code} is your {$app} verification code. It expires in {$minutes} minutes. Do not share it with anyone.";

        $result = $this->notifier->smsNow($phone, $message, "otp.{$purpose}", 'security', $userId, storedBody: "[{$purpose} code]");

        if (! $result->success) {
            return ['sent' => false, 'retry_after' => 0, 'error' => 'gateway'];
        }

        foreach ($limits as $key => [, $decay]) {
            RateLimiter::hit($key, $decay);
        }

        return ['sent' => true, 'retry_after' => 60, 'error' => null];
    }

    /** Check a code and use it up. False for wrong, expired, superseded or exhausted codes. */
    public function verify(string $phone, string $purpose, string $code): bool
    {
        $phone = (string) Phone::clean($phone);
        $code = (string) preg_replace('/\D/', '', $code);

        $record = PhoneVerification::where('phone', $phone)->where('purpose', $purpose)
            ->whereNull('consumed_at')->latest('id')->first();

        if (! $record || $record->expires_at->isPast() || $record->attempts >= (int) config('platform.otp.max_attempts')) {
            return false;
        }

        if (! Hash::check($code, $record->code_hash)) {
            $record->increment('attempts');

            return false;
        }

        // Conditional update so two simultaneous submissions cannot both use the same code.
        return PhoneVerification::whereKey($record->id)->whereNull('consumed_at')->update(['consumed_at' => now()]) === 1;
    }
}
