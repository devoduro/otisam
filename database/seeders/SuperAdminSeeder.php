<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\UserStatus;
use App\Models\User;
use App\Support\Permissions;
use App\Support\Phone;
use Illuminate\Database\Seeder;

/**
 * Creates the first Super Admin from SEED_SUPERADMIN_* in .env, if no Super Admin exists yet.
 * The account must change its password at first sign-in.
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (User::role(Permissions::SUPER_ADMIN)->exists()) {
            $this->command?->info('A Super Admin already exists; nothing to do.');

            return;
        }

        $email = env('SEED_SUPERADMIN_EMAIL') ?: null;
        $phone = Phone::clean(env('SEED_SUPERADMIN_PHONE')) ?: null;
        $password = env('SEED_SUPERADMIN_PASSWORD');

        if ((! $email && ! $phone) || ! $password) {
            $this->command?->warn('Set SEED_SUPERADMIN_EMAIL or SEED_SUPERADMIN_PHONE, and SEED_SUPERADMIN_PASSWORD, in .env, then run: php artisan db:seed --class=SuperAdminSeeder');

            return;
        }

        $user = new User(['name' => env('SEED_SUPERADMIN_NAME', 'Platform Owner'), 'email' => $email ? mb_strtolower($email) : null, 'phone' => $phone, 'password' => $password]);
        $user->account_type = AccountType::Staff;
        $user->status = UserStatus::Active;
        $user->must_change_password = true;
        $user->save();
        $user->assignRole(Permissions::SUPER_ADMIN);

        $this->command?->info("Super Admin created for {$user->name}. They must change the password at first sign-in.");
    }
}
