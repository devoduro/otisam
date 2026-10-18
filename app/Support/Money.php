<?php

namespace App\Support;

use InvalidArgumentException;

/** Amounts are integers in minor units (pesewas). Never floats. */
final class Money
{
    private const SYMBOLS = ['GHS' => 'GH₵'];

    public static function format(int $minor, string $currency = 'GHS'): string
    {
        $symbol = self::SYMBOLS[$currency] ?? $currency.' ';

        return $symbol.' '.number_format(intdiv($minor, 100)).'.'.str_pad((string) ($minor % 100), 2, '0', STR_PAD_LEFT);
    }

    /** "120", "120.5", "1,200.50" → minor units, without going through floats. */
    public static function toMinor(string|int $amount): int
    {
        $amount = str_replace([',', ' '], '', trim((string) $amount));

        if (! preg_match('/^(\d{1,9})(?:\.(\d{1,2}))?$/', $amount, $m)) {
            throw new InvalidArgumentException('Enter an amount like 120 or 120.50.');
        }

        return (int) $m[1] * 100 + (int) str_pad($m[2] ?? '0', 2, '0');
    }

    /** Minor units → "120.50" for form inputs. */
    public static function toDecimal(int $minor): string
    {
        return intdiv($minor, 100).'.'.str_pad((string) ($minor % 100), 2, '0', STR_PAD_LEFT);
    }
}
