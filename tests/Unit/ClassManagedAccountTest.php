<?php

namespace Tests\Unit;

use App\Enums\AccountType;
use App\Http\Middleware\EnsurePhoneVerified;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/** Student accounts a teacher creates from a class list: username login, no phone. */
class ClassManagedAccountTest extends TestCase
{
    private function student(array $attributes = []): User
    {
        $user = User::factory()->student()->create(['phone' => null]);
        $user->forceFill($attributes)->save();

        return $user;
    }

    public function test_an_account_is_class_managed_only_with_both_a_username_and_a_managing_teacher(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->assertTrue($this->student(['username' => 'ama.owusu123', 'managed_by_id' => $teacher->id])->isClassManaged());
        $this->assertFalse($this->student(['username' => 'kofi.annan123'])->isClassManaged());
        $this->assertFalse($this->student(['managed_by_id' => $teacher->id])->isClassManaged());
        $this->assertFalse(User::factory()->student()->create()->isClassManaged());
    }

    public function test_the_managing_teacher_relation(): void
    {
        $teacher = User::factory()->teacher()->create();
        $student = $this->student(['username' => 'ama.owusu123', 'managed_by_id' => $teacher->id]);

        $this->assertTrue($student->managedBy->is($teacher));
    }

    public function test_deleting_the_teacher_keeps_the_student_account(): void
    {
        $teacher = User::factory()->teacher()->create();
        $student = $this->student(['username' => 'ama.owusu123', 'managed_by_id' => $teacher->id]);

        $teacher->delete();

        $this->assertNull($student->fresh()->managed_by_id);
        $this->assertFalse($student->fresh()->isClassManaged());
    }

    public function test_find_by_login_tells_usernames_phones_and_emails_apart(): void
    {
        $byUsername = $this->student(['username' => 'ama.owusu123']);
        $byPhone = User::factory()->student()->create(['phone' => '0241234567']);
        $byEmail = User::factory()->student()->create(['email' => 'esi@example.com']);

        $this->assertTrue(User::findByLogin('ama.owusu123')->is($byUsername));
        $this->assertTrue(User::findByLogin('  AMA.Owusu123 ')->is($byUsername), 'Usernames are not case sensitive.');
        $this->assertTrue(User::findByLogin('024 123 4567')->is($byPhone));
        $this->assertTrue(User::findByLogin('+233241234567')->is($byPhone));
        $this->assertTrue(User::findByLogin('ESI@example.com')->is($byEmail));
        $this->assertNull(User::findByLogin('nobody123'));
        $this->assertNull(User::findByLogin('ama.owusu'), 'A username must match in full.');
    }

    public function test_usernames_are_unique(): void
    {
        $this->student(['username' => 'ama.owusu123']);

        $this->expectException(UniqueConstraintViolationException::class);
        $this->student(['username' => 'ama.owusu123']);
    }

    private function passesPhoneCheck(User $user): bool
    {
        $request = Request::create('/learn');
        $request->setUserResolver(fn () => $user);

        $response = (new EnsurePhoneVerified)->handle($request, fn () => new Response('ok'));

        return $response->getContent() === 'ok';
    }

    public function test_class_managed_students_skip_phone_verification(): void
    {
        $teacher = User::factory()->teacher()->create();
        $managed = $this->student(['username' => 'ama.owusu123', 'managed_by_id' => $teacher->id, 'phone_verified_at' => null]);

        $this->assertTrue($this->passesPhoneCheck($managed));
    }

    public function test_other_unverified_accounts_still_need_phone_verification(): void
    {
        $unverified = User::factory()->student()->create(['phone_verified_at' => null]);
        $usernameOnly = $this->student(['username' => 'kofi.annan123', 'phone_verified_at' => null]);

        $this->assertFalse($this->passesPhoneCheck($unverified));
        $this->assertFalse($this->passesPhoneCheck($usernameOnly));
        $this->assertTrue($this->passesPhoneCheck(User::factory()->student()->create(['phone_verified_at' => now()])));
        $this->assertSame(AccountType::Student, $unverified->account_type);
    }
}
