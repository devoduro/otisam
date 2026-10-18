<?php

namespace App\Services\Auth;

use App\Enums\AccountType;
use App\Enums\TeacherVerificationStatus;
use App\Enums\UserStatus;
use App\Models\User;
use App\Services\Audit;
use App\Support\Permissions;
use App\Support\Phone;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    /** @param array<string, mixed> $data validated input */
    public function registerStudent(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->newUser(AccountType::Student, $data);
            $user->assignRole(Permissions::STUDENT);

            $isMinor = !($data['is_adult'] ?? false);
            $user->studentProfile()->create([
                'class_level' => $data['class_level'],
                'programme' => $data['programme'] ?? null,
                'school_name' => $data['school_name'] ?? null,
                'region' => $data['region'] ?? null,
                'is_minor' => $isMinor,
                'guardian_name' => $data['guardian_name'] ?? null,
                'guardian_phone' => Phone::clean($data['guardian_phone'] ?? null),
            ]);

            return $user;
        });
    }

    /** @param array<string, mixed> $data validated input */
    public function registerTeacher(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->newUser(AccountType::Teacher, $data);
            $user->assignRole(Permissions::TEACHER_PENDING);

            $profile = $user->teacherProfile()->make([
                'staff_number' => $data['staff_number'] ?? null,
                'current_rank' => $data['current_rank'] ?? null,
                'school_name' => $data['school_name'],
                'region' => $data['region'],
                'district' => $data['district'] ?? null,
                'subjects_taught' => $data['subjects_taught'],
            ]);
            $profile->verification_status = TeacherVerificationStatus::Pending;
            $profile->save();

            return $user;
        });
    }

    /**
     * A phone number that was registered but never verified does not lock out its real owner:
     * the unverified account is removed and the new registration takes the number.
     */
    public static function releaseUnverifiedPhone(?string $phone): void
    {
        $phone = Phone::clean($phone);

        if ($phone) {
            User::where('phone', $phone)->whereNull('phone_verified_at')->where('account_type', '!=', AccountType::Staff->value)->get()
                ->each(function (User $stale) {
                    Audit::record('user', 'unverified.released', "Unverified registration replaced for {$stale->name}", $stale);
                    $stale->delete();
                });
        }
    }

    private function newUser(AccountType $type, array $data): User
    {
        self::releaseUnverifiedPhone($data['phone']);

        $user = new User([
            'name' => trim($data['name']),
            'email' => isset($data['email']) && $data['email'] !== '' ? mb_strtolower($data['email']) : null,
            'phone' => Phone::clean($data['phone']),
            'password' => $data['password'],
        ]);
        $user->account_type = $type;
        $user->status = UserStatus::Active;
        $user->save();

        return $user;
    }
}
