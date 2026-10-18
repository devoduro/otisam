<?php

namespace Database\Seeders;

use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Creates the permission catalogue and built-in roles from App\Support\Permissions.
 *
 * Safe to re-run: built-in staff roles are given their default permissions only when first
 * created, so changes an administrator made in the role editor survive a reseed. Super Admin is
 * always resynced to every permission.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permissions::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach (Permissions::DEFAULT_ROLES as $name => $permissions) {
            $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);

            if ($name === Permissions::SUPER_ADMIN) {
                $role->syncPermissions(Permissions::all());
            } elseif ($role->wasRecentlyCreated) {
                $role->syncPermissions($permissions);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
