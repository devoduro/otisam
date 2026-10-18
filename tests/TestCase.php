<?php

namespace Tests;

use App\Services\Sms\LogSmsGateway;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\RateLimiter;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
        LogSmsGateway::$sent = [];
        RateLimiter::clear('otp');
    }

    /** The 6-digit code in the last SMS "sent" to a phone through the log gateway. */
    protected function lastCodeSentTo(string $phone): ?string
    {
        foreach (array_reverse(LogSmsGateway::$sent) as $sms) {
            if ($sms['phone'] === $phone && preg_match('/\b(\d{6})\b/', $sms['message'], $m)) {
                return $m[1];
            }
        }

        return null;
    }
}
