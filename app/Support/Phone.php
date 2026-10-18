<?php

namespace App\Support;

/**
 * Ghana mobile numbers in the one shape the platform stores and the SMS gateway wants:
 * ten digits with a leading 0 ("+233 24 123 4567", "233241234567" and "24 123 4567" all
 * become "0241234567"). Ported from jhs_portal.
 */
class Phone
{
    /** Ghana mobile prefixes: 02x / 05x (MTN, Telecel, AirtelTigo). */
    public const GHANA_MOBILE = '/^0[25]\d{8}$/';

    public static function clean(?string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '233') && strlen($digits) >= 12) {
            return '0' . substr($digits, 3);
        }

        return strlen($digits) === 9 ? '0' . $digits : $digits;
    }

    public static function isGhanaMobile(?string $phone): bool
    {
        return (bool) preg_match(self::GHANA_MOBILE, (string) self::clean($phone));
    }

    /** "0241234567" → "024****567", for logs and screens that must not show the full number. */
    public static function mask(?string $phone): string
    {
        $phone = (string) self::clean($phone);

        return strlen($phone) < 7 ? '***' : substr($phone, 0, 3) . str_repeat('*', strlen($phone) - 6) . substr($phone, -3);
    }
}
