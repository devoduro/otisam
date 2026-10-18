<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Enums\TeacherVerificationStatus;
use App\Enums\UserStatus;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '024' . fake()->unique()->numerify('#######'),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'account_type' => AccountType::Student,
            'status' => UserStatus::Active,
            'phone_verified_at' => now(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['phone_verified_at' => null]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => UserStatus::Suspended]);
    }

    public function student(): static
    {
        return $this->state(fn () => ['account_type' => AccountType::Student])
            ->afterCreating(function (User $user) {
                $user->assignRole(Permissions::STUDENT);
                $user->studentProfile()->create(['class_level' => 'SHS 2', 'programme' => 'General Science']);
            });
    }

    public function teacher(TeacherVerificationStatus $status = TeacherVerificationStatus::Pending): static
    {
        return $this->state(fn () => ['account_type' => AccountType::Teacher])
            ->afterCreating(function (User $user) use ($status) {
                $user->assignRole($status === TeacherVerificationStatus::Approved ? Permissions::TEACHER : Permissions::TEACHER_PENDING);
                $profile = $user->teacherProfile()->make([
                    'school_name' => 'Accra Academy',
                    'region' => 'Greater Accra',
                    'subjects_taught' => 'Core Mathematics',
                ]);
                $profile->verification_status = $status;
                $profile->save();
            });
    }

    public function staff(string $role = Permissions::SUPER_ADMIN): static
    {
        return $this->state(fn () => ['account_type' => AccountType::Staff])
            ->afterCreating(fn (User $user) => $user->assignRole($role));
    }
}
