<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LoginAndPasswordTest extends TestCase
{
    public function test_each_account_type_lands_on_its_own_dashboard(): void
    {
        $student = User::factory()->student()->create();
        $teacher = User::factory()->teacher()->create();
        $staff = User::factory()->staff('Administrator')->create(['email' => 'admin@example.com']);

        $this->post(route('login.attempt'), ['login' => $student->phone, 'password' => 'password'])->assertRedirect(route('learn.dashboard'));
        $this->post(route('logout'));
        $this->post(route('login.attempt'), ['login' => $teacher->phone, 'password' => 'password'])->assertRedirect(route('teach.dashboard'));
        $this->post(route('logout'));
        $this->post(route('login.attempt'), ['login' => 'ADMIN@example.com', 'password' => 'password'])->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_is_throttled_after_five_failures(): void
    {
        $user = User::factory()->student()->create();

        foreach (range(1, 5) as $i) {
            $this->post(route('login.attempt'), ['login' => $user->phone, 'password' => 'wrong'])->assertSessionHasErrors('login');
        }

        $this->post(route('login.attempt'), ['login' => $user->phone, 'password' => 'password'])
            ->assertSessionHasErrors(['login' => 'Too many sign-in attempts. Try again in 60 seconds.']);
        $this->assertGuest();
    }

    public function test_suspended_accounts_cannot_sign_in_and_are_signed_out(): void
    {
        $user = User::factory()->student()->suspended()->create();

        $this->post(route('login.attempt'), ['login' => $user->phone, 'password' => 'password'])->assertSessionHasErrors('login');
        $this->assertGuest();

        $active = User::factory()->student()->create();
        $this->actingAs($active)->get(route('learn.dashboard'))->assertOk();
        $active->forceFill(['status' => 'suspended'])->save();
        $this->get(route('learn.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_password_reset_by_sms_code(): void
    {
        $user = User::factory()->student()->create(['phone' => '0551234567']);

        $this->post(route('password.send'), ['phone' => '055 123 4567'])->assertRedirect(route('password.reset'));
        $code = $this->lastCodeSentTo('0551234567');
        $this->assertNotNull($code);

        $this->post(route('password.update'), ['code' => '123123', 'password' => 'newpass99', 'password_confirmation' => 'newpass99'])
            ->assertSessionHasErrors('code');
        $this->post(route('password.update'), ['code' => $code, 'password' => 'newpass99', 'password_confirmation' => 'newpass99'])
            ->assertRedirect(route('login'));

        $this->post(route('login.attempt'), ['login' => '0551234567', 'password' => 'newpass99'])->assertRedirect(route('learn.dashboard'));
    }

    public function test_reset_form_does_not_reveal_whether_a_number_is_registered(): void
    {
        $response = $this->post(route('password.send'), ['phone' => '0209999999']);

        $response->assertRedirect(route('password.reset'))->assertSessionHas('status');
        $this->assertNull($this->lastCodeSentTo('0209999999'));
    }

    public function test_staff_with_temporary_password_must_change_it_first(): void
    {
        $staff = User::factory()->staff('Administrator')->create(['must_change_password' => true]);

        $this->actingAs($staff)->get(route('admin.dashboard'))->assertRedirect(route('password.change'));

        $this->put(route('password.change.update'), [
            'current_password' => 'password',
            'password' => 'BrandNew2026',
            'password_confirmation' => 'BrandNew2026',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))->assertOk();
    }
}
