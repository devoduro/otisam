<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\Programme;
use App\Models\NotificationMessage;
use App\Models\User;
use App\Services\Sms\LogSmsGateway;
use Tests\TestCase;

/** Acceptance criterion 2: a student can register, verify their account and sign in. */
class StudentRegistrationTest extends TestCase
{
    private function register(array $overrides = [])
    {
        return $this->post(route('register.store'), array_merge([
            'name' => 'Ama Mensah',
            'phone' => '+233 24 123 4567',
            'class_level_id' => ClassLevel::where('name', 'SHS 2')->value('id'),
            'programme_id' => Programme::where('name', 'General Science')->value('id'),
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'terms' => '1',
        ], $overrides));
    }

    public function test_student_registers_verifies_phone_and_signs_in(): void
    {
        $this->register()->assertRedirect(route('verify.phone'));

        $user = User::where('phone', '0241234567')->firstOrFail();
        $this->assertSame(AccountType::Student, $user->account_type);
        $this->assertTrue($user->hasRole('Student'));
        $this->assertSame('SHS 2', $user->studentProfile->classLevel->name);
        $this->assertSame('General Science', $user->studentProfile->programme->name);
        $this->assertTrue($user->studentProfile->is_minor, 'Accounts are treated as minors unless the 18+ box is ticked.');
        $this->assertNull($user->phone_verified_at);

        // Not verified yet: the student area is closed.
        $this->get(route('learn.dashboard'))->assertRedirect(route('verify.phone'));

        $code = $this->lastCodeSentTo('0241234567');
        $this->assertNotNull($code, 'A 6-digit code was sent by SMS.');

        // The code itself is never stored in the message log.
        $this->assertFalse(NotificationMessage::where('body', 'like', "%{$code}%")->exists());

        $this->post(route('verify.phone.check'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->post(route('verify.phone.check'), ['code' => $code])->assertRedirect(route('learn.dashboard'));
        $this->assertNotNull($user->fresh()->phone_verified_at);

        $this->get(route('learn.dashboard'))->assertOk()->assertSee(', Ama')->assertSee('SHS 2');

        $this->post(route('logout'));
        $this->post(route('login.attempt'), ['login' => '024 123 4567', 'password' => 'secret123'])
            ->assertRedirect(route('learn.dashboard'));
        $this->assertAuthenticatedAs($user->fresh());
    }

    public function test_sign_up_form_submits_class_and_programme_ids(): void
    {
        $class = ClassLevel::where('name', 'SHS 2')->firstOrFail();
        $programme = Programme::where('name', 'General Science')->firstOrFail();

        // Regression: an id => name option list once submitted the name instead of the id.
        $this->get(route('register'))->assertOk()
            ->assertSee('<option value="'.$class->id.'"', false)
            ->assertSee('<option value="'.$programme->id.'"', false)
            ->assertSee('<option value="Ashanti"', false);
    }

    public function test_code_is_single_use_and_expires(): void
    {
        $this->register();
        $code = $this->lastCodeSentTo('0241234567');

        $this->travel(11)->minutes();
        $this->post(route('verify.phone.check'), ['code' => $code])->assertSessionHasErrors('code');
    }

    public function test_code_stops_working_after_too_many_wrong_guesses(): void
    {
        $this->register();
        $code = $this->lastCodeSentTo('0241234567');

        foreach (range(1, 5) as $i) {
            $this->post(route('verify.phone.check'), ['code' => '111111']);
        }

        $this->post(route('verify.phone.check'), ['code' => $code])->assertSessionHasErrors('code');
    }

    public function test_rejects_non_ghana_numbers_and_verified_duplicates(): void
    {
        $this->register(['phone' => '12345'])->assertSessionHasErrors('phone');

        User::factory()->student()->create(['phone' => '0241234567']);
        $this->register()->assertSessionHasErrors('phone');
    }

    public function test_unverified_registration_does_not_lock_out_the_real_owner(): void
    {
        $this->register(['name' => 'Someone Else']);
        $this->post(route('logout'));

        $this->register(['name' => 'Real Owner'])->assertRedirect(route('verify.phone'));

        $this->assertSame(1, User::where('phone', '0241234567')->count());
        $this->assertSame('Real Owner', User::where('phone', '0241234567')->value('name'));
    }

    public function test_account_type_and_status_cannot_be_injected(): void
    {
        $this->register(['account_type' => 'staff', 'status' => 'active', 'phone_verified_at' => now()]);

        $user = User::where('phone', '0241234567')->firstOrFail();
        $this->assertSame(AccountType::Student, $user->account_type);
        $this->assertNull($user->phone_verified_at);
    }

    public function test_resending_codes_is_rate_limited(): void
    {
        $this->register();

        $this->post(route('verify.phone.resend'))->assertSessionHas('error');
        $this->assertCount(1, array_filter(LogSmsGateway::$sent, fn ($s) => $s['phone'] === '0241234567'));
    }
}
