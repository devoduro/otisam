<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Permissions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Acceptance criterion 18 (identity part) and the privilege-escalation checks in BLUEPRINT §10. */
class AccessControlTest extends TestCase
{
    public function test_guests_are_sent_to_login(): void
    {
        foreach (['admin.dashboard', 'learn.dashboard', 'teach.dashboard'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
    }

    public function test_each_account_type_is_kept_in_its_own_area(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)->get(route('admin.dashboard'))->assertRedirect(route('learn.dashboard'));
        $this->actingAs($student)->get(route('teach.dashboard'))->assertRedirect(route('learn.dashboard'));
        $this->actingAs($student)->get(route('admin.students.index'))->assertRedirect(route('learn.dashboard'));
    }

    public function test_admin_pages_check_permissions_on_the_server(): void
    {
        $moderator = User::factory()->staff('Moderator')->create();

        $this->actingAs($moderator)->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.students.index'))->assertForbidden();
        $this->get(route('admin.staff.index'))->assertForbidden();
        $this->get(route('admin.roles.index'))->assertForbidden();
        $this->get(route('admin.audit.index'))->assertForbidden();
    }

    public function test_administrator_cannot_manage_staff_or_roles(): void
    {
        $admin = User::factory()->staff('Administrator')->create();

        $this->actingAs($admin)->get(route('admin.staff.index'))->assertForbidden();
        $this->post(route('admin.staff.store'), ['name' => 'X', 'email' => 'x@example.com', 'roles' => [Permissions::SUPER_ADMIN]])->assertForbidden();
        $this->post(route('admin.roles.store'), ['name' => 'Sneaky', 'permissions' => ['staff.manage']])->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'x@example.com']);
    }

    public function test_super_admin_only_permissions_cannot_be_put_on_a_role(): void
    {
        $owner = User::factory()->staff()->create();

        $this->actingAs($owner)->post(route('admin.roles.store'), ['name' => 'Deputy', 'permissions' => ['users.view', 'roles.manage']])
            ->assertSessionHasErrors('permissions.1');
        $this->assertDatabaseMissing('roles', ['name' => 'Deputy']);

        $this->post(route('admin.roles.store'), ['name' => 'Deputy', 'permissions' => ['users.view']])->assertRedirect(route('admin.roles.index'));
        $this->assertTrue(Role::findByName('Deputy')->hasPermissionTo('users.view'));
    }

    public function test_super_admin_and_built_in_roles_are_protected(): void
    {
        $owner = User::factory()->staff()->create();
        $superAdmin = Role::findByName(Permissions::SUPER_ADMIN);
        $administrator = Role::findByName('Administrator');

        $this->actingAs($owner)->put(route('admin.roles.update', $superAdmin), ['name' => 'Super Admin', 'permissions' => []])->assertSessionHas('error');
        $this->assertSame(count(Permissions::all()), $superAdmin->fresh()->permissions()->count());

        $this->delete(route('admin.roles.destroy', $administrator))->assertSessionHas('error');
        $this->put(route('admin.roles.update', $administrator), ['name' => 'Renamed', 'permissions' => []])->assertSessionHasErrors('name');
        $this->get(route('admin.roles.edit', Role::findByName(Permissions::STUDENT)))->assertNotFound();
    }

    public function test_the_last_super_admin_cannot_be_suspended_or_demoted(): void
    {
        $owner = User::factory()->staff()->create();
        $second = User::factory()->staff()->create();

        $this->actingAs($owner)->post(route('admin.accounts.suspend', $owner), ['reason' => 'test'])->assertSessionHas('error');

        $this->post(route('admin.accounts.suspend', $second), ['reason' => 'Left the company'])->assertSessionHas('success');
        $this->assertFalse($second->fresh()->isActive());

        // Now $owner is the only active Super Admin; a suspended one does not count.
        $third = User::factory()->staff('Administrator')->create();
        $this->actingAs($owner)->put(route('admin.staff.update', $owner), ['name' => $owner->name, 'email' => $owner->email, 'roles' => ['Administrator']])
            ->assertSessionHas('error');
        $this->assertTrue($owner->fresh()->isSuperAdmin());
    }

    public function test_suspending_staff_needs_super_admin_but_students_need_users_suspend(): void
    {
        $admin = User::factory()->staff('Administrator')->create();
        $other = User::factory()->staff('Finance Officer')->create();
        $student = User::factory()->student()->create();

        $this->actingAs($admin)->post(route('admin.accounts.suspend', $other), ['reason' => 'x'])->assertForbidden();
        $this->post(route('admin.accounts.suspend', $student), ['reason' => 'Shared account'])->assertSessionHas('success');
        $this->assertFalse($student->fresh()->isActive());
    }

    public function test_super_admin_creates_staff_with_temporary_password(): void
    {
        $owner = User::factory()->staff()->create();

        $this->actingAs($owner)->post(route('admin.staff.store'), [
            'name' => 'Efua Owusu', 'email' => 'efua@example.com', 'phone' => '0277000111', 'roles' => ['Finance Officer'],
        ])->assertRedirect(route('admin.staff.index'))->assertSessionHas('temporary_password');

        $staff = User::where('email', 'efua@example.com')->firstOrFail();
        $this->assertTrue($staff->isStaff());
        $this->assertTrue($staff->must_change_password);
        $this->assertTrue($staff->hasRole('Finance Officer'));

        // Account roles are not staff roles.
        $this->post(route('admin.staff.store'), ['name' => 'Y', 'email' => 'y@example.com', 'roles' => ['Student']])->assertSessionHasErrors('roles.0');
    }

    public function test_super_admin_power_comes_from_the_role_not_the_user_id(): void
    {
        $first = User::factory()->student()->create(); // likely id 1 in a fresh database
        $this->actingAs($first)->get(route('admin.dashboard'))->assertRedirect(route('learn.dashboard'));
        $this->assertFalse($first->can('users.manage'));
    }

    public function test_admin_changes_are_written_to_the_audit_log(): void
    {
        $owner = User::factory()->staff()->create();
        $student = User::factory()->student()->create();

        $this->actingAs($owner)->post(route('admin.accounts.suspend', $student), ['reason' => 'Abuse']);

        $this->get(route('admin.audit.index'))->assertOk()->assertSee("Suspended Student: {$student->name}");
    }
}
