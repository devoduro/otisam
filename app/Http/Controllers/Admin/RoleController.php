<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Audit;
use App\Support\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Staff role editor, ported from jhs_portal's RoleController:
 * - Super Admin always has every permission and cannot be edited or deleted;
 * - built-in roles can change permissions but cannot be renamed or deleted;
 * - Super-Admin-only permissions cannot be given to any other role;
 * - account roles (Student, Teacher, Teacher (Pending)) are managed by code, not here.
 */
class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::whereNotIn('name', Permissions::ACCOUNT_ROLES)->withCount(['users', 'permissions'])->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.form', ['role' => new Role(), 'rolePermissions' => []]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);

        Audit::record('user', 'role.created', "Role created: {$role->name}", $role, null, ['permissions' => $data['permissions'] ?? []]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role): View|RedirectResponse
    {
        if ($guard = $this->guard($role)) {
            return $guard;
        }

        return view('admin.roles.form', ['role' => $role, 'rolePermissions' => $role->permissions->pluck('name')->all()]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($guard = $this->guard($role)) {
            return $guard;
        }

        if (array_key_exists($role->name, Permissions::DEFAULT_ROLES) && $request->input('name') !== $role->name) {
            return back()->withInput()->withErrors(['name' => "\"{$role->name}\" is a built-in role and cannot be renamed. You can change what it is allowed to do."]);
        }

        $data = $this->validated($request, $role);
        $before = $role->permissions->pluck('name')->all();

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Audit::record('user', 'role.updated', "Role updated: {$role->name}", $role, ['permissions' => $before], ['permissions' => $data['permissions'] ?? []]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($guard = $this->guard($role)) {
            return $guard;
        }
        if (array_key_exists($role->name, Permissions::DEFAULT_ROLES)) {
            return back()->with('error', "\"{$role->name}\" is a built-in role and cannot be deleted.");
        }
        if ($role->users()->exists()) {
            return back()->with('error', 'This role is still assigned to staff. Move them to another role first.');
        }

        Audit::record('user', 'role.deleted', "Role deleted: {$role->name}", $role, ['permissions' => $role->permissions->pluck('name')->all()]);
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }

    private function guard(Role $role): ?RedirectResponse
    {
        if ($role->name === Permissions::SUPER_ADMIN) {
            return redirect()->route('admin.roles.index')->with('error', 'The Super Admin role always has every permission and cannot be changed.');
        }
        if (in_array($role->name, Permissions::ACCOUNT_ROLES, true)) {
            abort(404);
        }

        return null;
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('roles', 'name')->ignore($role?->id), Rule::notIn(Permissions::ACCOUNT_ROLES)],
            'permissions' => ['array'],
            // Super-Admin-only permissions are not in this list, so they can never be granted here.
            'permissions.*' => ['string', Rule::in(Permissions::assignable())],
        ]);
    }
}
