<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Commerce\Coupon;
use App\Models\Commerce\Payment;
use App\Models\Commerce\PaymentWebhookEvent;
use App\Models\Commerce\Plan;
use App\Models\Commerce\Subscription;
use App\Models\Curriculum\Subject;
use App\Models\Library\Resource;
use App\Models\Library\ResourceCategory;
use App\Models\NotificationMessage;
use App\Models\Setting;
use App\Models\User;
use App\Services\Library\InspectedUpload;
use App\Services\Library\ResourceService;
use FPDF;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Acceptance criteria 7–11, plus coupons, stacking, refunds, reconciliation and reminders. */
class PaymentsTest extends TestCase
{
    private const SECRET = 'sk_test_unit_secret';

    private Plan $plan;

    /** reference => Paystack verify `data` (or null for "not found") */
    private array $verify = [];

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.paystack.secret_key' => self::SECRET, 'services.paystack.public_key' => 'pk_test_unit']);
        $this->plan = Plan::create(['name' => 'Term pass', 'duration_months' => 3, 'price_minor' => 12000, 'currency' => 'GHS', 'is_active' => true]);

        Http::fake(function (Request $request) {
            if (str_contains($request->url(), '/transaction/initialize')) {
                return Http::response(['status' => true, 'message' => 'Authorization URL created', 'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test-'.$request['reference'],
                    'access_code' => 'x', 'reference' => $request['reference'],
                ]]);
            }
            if (preg_match('#/transaction/verify/(.+)$#', $request->url(), $m)) {
                $data = $this->verify[rawurldecode($m[1])] ?? null;

                return $data === null
                    ? Http::response(['status' => false, 'message' => 'Transaction reference not found'], 400)
                    : Http::response(['status' => true, 'message' => 'Verification successful', 'data' => $data]);
            }

            return Http::response(['status' => false], 404);
        });
    }

    private function student(): User
    {
        return User::factory()->student()->create();
    }

    /** Start a checkout through the real controller; returns the payment. */
    private function checkout(User $user, array $input = []): Payment
    {
        $this->actingAs($user)->post(route('account.checkout.store', $this->plan), $input)->assertRedirect();

        return Payment::where('user_id', $user->id)->latest('id')->firstOrFail();
    }

    private function paystackSays(Payment $payment, string $status = 'success', ?int $amount = null, string $currency = 'GHS', ?string $reference = null): void
    {
        $this->verify[$payment->reference] = [
            'id' => random_int(100000, 999999), 'status' => $status, 'reference' => $reference ?? $payment->reference,
            'amount' => $amount ?? $payment->amount_minor, 'currency' => $currency, 'channel' => 'mobile_money',
            'paid_at' => now()->toIso8601String(), 'gateway_response' => $status === 'success' ? 'Approved' : 'Declined',
        ];
    }

    private function webhook(array $payload, ?string $signature = null)
    {
        $raw = json_encode($payload);

        return $this->call('POST', route('webhooks.paystack'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_PAYSTACK_SIGNATURE' => $signature ?? hash_hmac('sha512', $raw, self::SECRET),
        ], $raw);
    }

    private function premiumResource(): Resource
    {
        Storage::fake('library');
        $pdf = new FPDF;
        foreach (range(1, 4) as $i) {
            $pdf->AddPage();
        }
        $tmp = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tmp, $pdf->Output('S'));

        $service = app(ResourceService::class);
        $resource = $service->create([
            'title' => 'Premium solutions', 'category_id' => ResourceCategory::where('name', 'Solutions and worked answers')->value('id'),
            'audience' => 'student', 'subject_id' => Subject::where('name', 'Core Mathematics')->value('id'), 'access_level' => 'premium', 'copyright_holder' => 'Otisam',
        ], new InspectedUpload($tmp, 's.pdf', 'pdf', 'application/pdf', filesize($tmp), hash_file('sha256', $tmp)), User::factory()->staff()->create());
        $service->setStatus($resource, 'published');

        return $resource;
    }

    public function test_checkout_uses_the_server_price_and_redirects_to_paystack(): void
    {
        $student = User::factory()->student()->create(['email' => null]);
        $this->actingAs($student)->get(route('account.checkout', $this->plan))->assertOk()->assertSee('GH₵ 120.00');

        $response = $this->post(route('account.checkout.store', $this->plan), ['amount' => 1, 'amount_minor' => 1, 'price_minor' => 1]);
        $payment = Payment::firstOrFail();

        $response->assertRedirect('https://checkout.paystack.com/test-'.$payment->reference);
        $this->assertSame(12000, $payment->amount_minor);
        $this->assertSame('initiated', $payment->status);
        $this->assertSame("user{$student->id}@students.otisam.app", $payment->customer_email, 'Phone-only students get a placeholder email for Paystack.');
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'initialize') && $r['amount'] === 12000 && $r['currency'] === 'GHS'
            && $r->hasHeader('Authorization', 'Bearer '.self::SECRET));
    }

    public function test_verified_payment_activates_the_correct_subscription_and_unlocks_premium(): void
    {
        $resource = $this->premiumResource();
        $student = $this->student();
        $payment = $this->checkout($student);

        $this->get(route('library.view', $resource))->assertForbidden();

        $this->paystackSays($payment);
        $this->get(route('payments.callback', ['reference' => $payment->reference]))->assertRedirect(route('account.payments.show', $payment));

        $payment->refresh();
        $this->assertSame('success', $payment->status);
        $this->assertSame('callback', $payment->verification_source);
        $subscription = $payment->subscription;
        $this->assertTrue($subscription->starts_at->isToday());
        $this->assertSame(now()->addMonthsNoOverflow(3)->toDateString(), $subscription->ends_at->toDateString());
        $this->assertNotNull($payment->receipt->number);

        // AC10: premium now opens; the receipt is available.
        $this->get(route('library.view', $resource))->assertOk();
        $this->get(route('account.payments.show', $payment))->assertOk()->assertSee('Payment received');
        $this->get(route('account.payments.receipt', $payment))->assertOk()->assertSee($payment->receipt->number);

        // The student is told, once, by SMS.
        $this->assertSame(1, NotificationMessage::where('event_key', 'payment.success')->where('channel', 'sms')->count());

        // AC11: once the period ends, protected content closes again.
        $this->travelTo($subscription->ends_at->copy()->addMinute());
        $this->get(route('library.view', $resource))->assertForbidden();
        $this->get(route('library.download', $resource))->assertForbidden();
    }

    public function test_failed_unverified_and_forged_payments_never_activate(): void
    {
        $student = $this->student();

        // Paystack says failed.
        $failed = $this->checkout($student);
        $this->paystackSays($failed, 'failed');
        $this->get(route('payments.callback', ['reference' => $failed->reference]));
        $this->assertSame('failed', $failed->fresh()->status);

        // Browser claims success but Paystack does not know the transaction (forged redirect).
        $forged = $this->checkout($student);
        $this->get(route('payments.callback', ['reference' => $forged->reference, 'status' => 'success', 'trxref' => $forged->reference]));
        $this->assertSame('initiated', $forged->fresh()->status);

        // Paid, but less than the price (e.g. tampered amount).
        $short = $this->checkout($student);
        $this->paystackSays($short, 'success', amount: 100);
        $this->get(route('payments.callback', ['reference' => $short->reference]));
        $this->assertSame('failed', $short->fresh()->status);
        $this->assertStringContainsString('amount mismatch', $short->fresh()->failure_reason);

        // Paid in another currency.
        $usd = $this->checkout($student);
        $this->paystackSays($usd, 'success', currency: 'USD');
        $this->get(route('payments.callback', ['reference' => $usd->reference]));
        $this->assertSame('failed', $usd->fresh()->status);

        // A reference we never issued.
        $this->get(route('payments.callback', ['reference' => 'OT-NOT-OURS']))->assertRedirect(route('account.subscription'));

        $this->assertSame(0, Subscription::count());
        $this->assertTrue(AuditLog::where('action', 'payment.mismatch')->exists());
    }

    public function test_unsigned_or_wrongly_signed_webhooks_are_rejected(): void
    {
        $payment = $this->checkout($this->student());
        $this->paystackSays($payment);
        $payload = ['event' => 'charge.success', 'data' => ['id' => 1, 'reference' => $payment->reference, 'status' => 'success']];

        $this->webhook($payload, 'not-a-real-signature')->assertStatus(401);
        $this->webhook($payload, '')->assertStatus(401);

        $this->assertSame(0, PaymentWebhookEvent::count());
        $this->assertSame('initiated', $payment->fresh()->status);
    }

    public function test_duplicate_webhooks_and_callback_create_exactly_one_subscription(): void
    {
        $payment = $this->checkout($this->student());
        $this->paystackSays($payment);
        $payload = ['event' => 'charge.success', 'data' => ['id' => 555, 'reference' => $payment->reference, 'status' => 'success', 'amount' => 12000]];

        $this->webhook($payload)->assertOk();
        $this->webhook($payload)->assertOk();
        $this->webhook($payload)->assertOk();
        $this->get(route('payments.callback', ['reference' => $payment->reference]));
        Artisan::call('payments:reconcile');

        $this->assertSame(1, PaymentWebhookEvent::count());
        $this->assertSame(1, Subscription::count());
        $this->assertSame(1, NotificationMessage::where('event_key', 'payment.success')->where('channel', 'sms')->count());
        $this->assertSame('webhook', $payment->fresh()->verification_source);
        $this->assertNotNull(PaymentWebhookEvent::first()->processed_at);

        // The database itself refuses a second subscription for the same payment.
        $this->expectException(UniqueConstraintViolationException::class);
        (new Subscription)->forceFill(['user_id' => $payment->user_id, 'payment_id' => $payment->id, 'source' => 'payment', 'starts_at' => now(), 'ends_at' => now()->addMonth()])->save();
    }

    public function test_buying_again_extends_after_the_current_period(): void
    {
        $student = $this->student();
        $first = $this->checkout($student);
        $this->paystackSays($first);
        $this->get(route('payments.callback', ['reference' => $first->reference]));

        $second = $this->checkout($student);
        $this->paystackSays($second);
        $this->get(route('payments.callback', ['reference' => $second->reference]));

        $a = $first->fresh()->subscription;
        $b = $second->fresh()->subscription;
        $this->assertEquals($a->ends_at, $b->starts_at);
        $this->assertSame(now()->addMonthsNoOverflow(3)->addMonthsNoOverflow(3)->toDateString(), $b->ends_at->toDateString());
    }

    public function test_pending_mobile_money_is_settled_by_reconciliation(): void
    {
        $payment = $this->checkout($this->student());
        $this->paystackSays($payment, 'ongoing');
        $this->get(route('payments.callback', ['reference' => $payment->reference]));
        $this->assertSame('pending', $payment->fresh()->status);
        $this->get(route('account.payments.show', $payment))->assertSee('Waiting for confirmation');

        $this->paystackSays($payment, 'success');
        $this->travel(5)->minutes();
        Artisan::call('payments:reconcile');
        $this->assertSame('success', $payment->fresh()->status);
        $this->assertSame('reconcile', $payment->fresh()->verification_source);

        // An attempt nobody finished is given up after 48 hours.
        $stale = $this->checkout($this->student());
        $this->travel(49)->hours();
        Artisan::call('payments:reconcile');
        $this->assertSame('abandoned', $stale->fresh()->status);
    }

    public function test_coupons_discount_the_price_and_a_full_discount_activates_without_paystack(): void
    {
        $student = $this->student();
        Coupon::create(['code' => 'launch20', 'type' => 'percent', 'value' => 20, 'per_user_limit' => 1, 'is_active' => true]);
        Coupon::create(['code' => 'FREEPASS', 'type' => 'percent', 'value' => 100, 'per_user_limit' => 1, 'is_active' => true]);
        Coupon::create(['code' => 'OLD', 'type' => 'fixed', 'value' => 500, 'ends_at' => now()->subDay(), 'is_active' => true]);

        $this->actingAs($student)->post(route('account.checkout.store', $this->plan), ['coupon' => 'old'])->assertSessionHasErrors('coupon');

        $discounted = $this->checkout($student, ['coupon' => 'Launch20']);
        $this->assertSame(9600, $discounted->amount_minor);
        $this->assertSame(2400, $discounted->discount_minor);

        $free = $this->checkout($student, ['coupon' => 'freepass']);
        $this->assertSame('success', $free->status);
        $this->assertSame('coupon_free', $free->subscription->source);
        Http::assertNotSent(fn (Request $r) => str_contains($r->url(), 'initialize') && $r['reference'] === $free->reference);

        // Used once already: refused the second time.
        $this->post(route('account.checkout.store', $this->plan), ['coupon' => 'freepass'])->assertSessionHasErrors('coupon');
    }

    public function test_grace_period_extends_access(): void
    {
        Setting::put('subscriptions.grace_days', 3, 'subscriptions');
        $resource = $this->premiumResource();
        $payment = $this->checkout($student = $this->student());
        $this->paystackSays($payment);
        $this->get(route('payments.callback', ['reference' => $payment->reference]));

        $ends = $payment->fresh()->subscription->ends_at;
        $this->travelTo($ends->copy()->addDays(2));
        $this->actingAs($student)->get(route('library.view', $resource))->assertOk();
        $this->travelTo($ends->copy()->addDays(4));
        $this->get(route('library.view', $resource))->assertForbidden();
    }

    public function test_refund_webhook_ends_access_once(): void
    {
        $payment = $this->checkout($this->student());
        $this->paystackSays($payment);
        $this->get(route('payments.callback', ['reference' => $payment->reference]));

        $refund = ['event' => 'refund.processed', 'data' => ['id' => 77, 'transaction_reference' => $payment->reference, 'amount' => 12000, 'status' => 'processed']];
        $this->webhook($refund)->assertOk();
        $this->webhook($refund)->assertOk();

        $this->assertSame('refunded', $payment->fresh()->status);
        $this->assertSame('refunded', $payment->fresh()->subscription->status);
        $this->assertSame(1, $payment->refunds()->count());
    }

    public function test_finance_staff_manage_plans_grants_and_refunds_with_audit(): void
    {
        $finance = User::factory()->staff('Finance Officer')->create();
        $student = $this->student();

        $this->actingAs($finance)->post(route('admin.commerce.plans.store'), ['name' => 'Year', 'duration_months' => 12, 'price' => '350.00', 'is_active' => '1'])
            ->assertRedirect(route('admin.commerce.plans.index'));
        $this->assertSame(35000, Plan::where('name', 'Year')->value('price_minor'));
        $this->post(route('admin.commerce.plans.store'), ['name' => 'Bad', 'duration_months' => 1, 'price' => '0', 'is_active' => '1'])->assertSessionHasErrors('price');

        $this->post(route('admin.commerce.subscriptions.grant'), ['login' => $student->phone, 'months' => 2, 'reason' => 'Partner school pilot'])->assertSessionHas('success');
        $grant = Subscription::where('user_id', $student->id)->firstOrFail();
        $this->assertSame('admin_grant', $grant->source);
        $this->assertSame($finance->id, $grant->granted_by);
        $this->assertTrue(AuditLog::where('action', 'subscription.granted')->exists());

        $this->post(route('admin.commerce.subscriptions.revoke', $grant), ['reason' => 'Pilot ended'])->assertSessionHas('success');
        $this->assertSame('revoked', $grant->fresh()->status);

        $payment = $this->checkout($student);
        $this->paystackSays($payment);
        $this->get(route('payments.callback', ['reference' => $payment->reference]));
        $this->actingAs($finance)->post(route('admin.commerce.payments.refund', $payment), ['amount' => '120', 'reason' => 'Duplicate purchase', 'revoke_access' => '1'])
            ->assertSessionHas('success');
        $this->assertSame('refunded', $payment->fresh()->status);
        $this->assertTrue(AuditLog::where('action', 'payment.refunded')->exists());

        foreach ([route('admin.commerce.plans.index'), route('admin.commerce.coupons.index'), route('admin.commerce.coupons.create'), route('admin.commerce.payments.index'),
            route('admin.commerce.payments.show', $payment), route('admin.commerce.subscriptions.index'), route('admin.dashboard')] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_commerce_is_protected_from_other_roles_and_other_users(): void
    {
        $contentManager = User::factory()->staff('Content Manager')->create();
        $this->actingAs($contentManager)->get(route('admin.commerce.payments.index'))->assertForbidden();
        $this->post(route('admin.commerce.subscriptions.grant'), ['login' => 'x', 'months' => 1, 'reason' => 'x'])->assertForbidden();

        $owner = $this->student();
        $payment = $this->checkout($owner);
        $this->actingAs($this->student())->get(route('account.payments.show', $payment))->assertNotFound();
        $this->post(route('account.payments.check', $payment))->assertNotFound();

        // Staff cannot buy; guests are sent to sign in.
        $this->actingAs($contentManager)->get(route('account.checkout', $this->plan))->assertRedirect(route('admin.dashboard'));
        auth()->logout();
        $this->get(route('account.checkout', $this->plan))->assertRedirect(route('login'));
    }

    public function test_expiry_reminders_are_sent_once_and_skipped_after_renewal(): void
    {
        $student = $this->student();
        $payment = $this->checkout($student);
        $this->paystackSays($payment);
        $this->get(route('payments.callback', ['reference' => $payment->reference]));
        $ends = $payment->fresh()->subscription->ends_at;

        $this->travelTo($ends->copy()->subDays(7)->setTime(8, 0));
        Artisan::call('subscriptions:remind');
        Artisan::call('subscriptions:remind');
        $this->assertSame(1, NotificationMessage::where('event_key', 'subscription.expiry.7d')->where('channel', 'sms')->count());

        // Renewed: no "1 day left" message for the old period.
        $renewal = $this->checkout($student);
        $this->paystackSays($renewal);
        $this->get(route('payments.callback', ['reference' => $renewal->reference]));
        $this->travelTo($ends->copy()->subDay()->setTime(8, 0));
        Artisan::call('subscriptions:remind');
        $this->assertSame(0, NotificationMessage::where('event_key', 'subscription.expiry.1d')->count());
    }

    public function test_plans_page_shows_only_plans_on_sale(): void
    {
        Plan::create(['name' => 'Hidden plan', 'duration_months' => 6, 'price_minor' => 20000, 'currency' => 'GHS', 'is_active' => false]);

        $this->get(route('plans.index'))->assertOk()->assertSee('Term pass')->assertSee('GH₵ 120.00')->assertDontSee('Hidden plan');
    }
}
