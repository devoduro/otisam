<?php

namespace Tests\Unit;

use App\Services\Sms\SpesoSmsGateway;
use App\Support\Permissions;
use App\Support\Phone;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SupportTest extends TestCase
{
    public function test_phone_numbers_are_normalised_to_ghana_local_format(): void
    {
        $this->assertSame('0241234567', Phone::clean('+233 24 123 4567'));
        $this->assertSame('0241234567', Phone::clean('233241234567'));
        $this->assertSame('0241234567', Phone::clean('24 123 4567'));
        $this->assertSame('0241234567', Phone::clean('024-123-4567'));
        $this->assertNull(Phone::clean(''));
        $this->assertTrue(Phone::isGhanaMobile('0551234567'));
        $this->assertFalse(Phone::isGhanaMobile('0302123456'), 'Landlines cannot receive codes.');
        $this->assertSame('024****567', Phone::mask('0241234567'));
    }

    public function test_super_admin_only_permissions_are_not_assignable(): void
    {
        foreach (Permissions::SUPER_ADMIN_ONLY as $permission) {
            $this->assertContains($permission, Permissions::all());
            $this->assertNotContains($permission, Permissions::assignable());
        }

        foreach (Permissions::DEFAULT_ROLES as $role => $permissions) {
            $this->assertEmpty(array_intersect($permissions, Permissions::SUPER_ADMIN_ONLY), "{$role} must not hold Super-Admin-only permissions.");
            $this->assertEmpty(array_diff($permissions, Permissions::all()), "{$role} lists an unknown permission.");
        }
    }

    public function test_speso_gateway_sends_the_jhs_portal_request_shape(): void
    {
        Http::fake(['business.speso.co/*' => Http::response(['success' => true, 'message' => 'Queued', 'data' => ['id' => 'abc123']])]);

        $result = (new SpesoSmsGateway('key-1', 'OTISAM', 'https://business.speso.co/api/v1'))->send('+233241234567', 'Hello');

        $this->assertTrue($result->success);
        $this->assertSame('abc123', $result->providerMessageId);
        Http::assertSent(fn (Request $r) => $r->url() === 'https://business.speso.co/api/v1/messages/send'
            && $r->hasHeader('Authorization', 'Bearer key-1')
            && $r['phone'] === '0241234567' && $r['sender_id'] === 'OTISAM' && $r['message'] === 'Hello');
    }

    public function test_speso_failures_are_reported_and_marked_retryable_only_when_transient(): void
    {
        Http::fake(['*' => Http::sequence()->push(['success' => false, 'message' => 'Invalid number'], 422)->push('oops', 503)]);
        $gateway = new SpesoSmsGateway('key-1', 'OTISAM', 'https://business.speso.co/api/v1');

        $bad = $gateway->send('0241234567', 'Hi');
        $this->assertFalse($bad->success);
        $this->assertFalse($bad->retryable);

        $down = $gateway->send('0241234567', 'Hi');
        $this->assertFalse($down->success);
        $this->assertTrue($down->retryable);

        $this->assertFalse((new SpesoSmsGateway('', 'OTISAM', 'https://x'))->send('0241234567', 'Hi')->success);
    }
}
