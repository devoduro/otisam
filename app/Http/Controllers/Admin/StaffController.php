<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountType;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Audit;
use App\Support\Permissions;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/** Staff accounts (Super Admin only, via staff.manage). */
class StaffController extends Controller
{
    public function index(): View
    {
        $staff = User::where('account_type', AccountType::Staff->value)->with('roles')->orderBy('name')->paginate(25);

        return view('admin.staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('admin.staff.form', ['member' => new User, 'roles' => $this->staffRoles(), 'memberRoles' => []]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $temporary = Str::password(12, symbols: false);

        $member = DB::transaction(function () use ($data, $temporary) {
            $member = new User(['name' => $data['name'], 'email' => $data['email'] ?? null, 'phone' => $data['phone'] ?? null, 'password' => $temporary]);
            $member->account_type = AccountType::Staff;
            $member->status = UserStatus::Active;
            $member->must_change_password = true;
            $member->save();
            $member->syncRoles($data['roles']);

            Audit::record('user', 'staff.created', "Staff account created: {$member->name}", $member, null, ['roles' => $data['roles']]);

            return $member;
        });

        // Shown once, to the Super Admin only. Never sent by SMS and never logged.
        return redirect()->route('admin.staff.index')
            ->with('success', "Staff account created for {$member->name}.")
            ->with('temporary_password', $temporary);
    }

    public function edit(User $member): View
    {
        abort_unless($member->isStaff(), 404);

        return view('admin.staff.form', ['member' => $member, 'roles' => $this->staffRoles(), 'memberRoles' => $member->roles->pluck('name')->all()]);
    }

    public function update(Request $request, User $member): RedirectResponse
    {
        abort_unless($member->isStaff(), 404);

        $data = $this->validated($request, $member);
        $before = $member->roles->pluck('name')->all();

        if (in_array(Permissions::SUPER_ADMIN, $before, true) && ! in_array(Permissions::SUPER_ADMIN, $data['roles'], true)) {
            if ($member->is($request->user())) {
                return back()->withInput()->with('error', 'You cannot remove the Super Admin role from yourself.');
            }
            if (AccountStatusController::activeSuperAdmins() <= 1) {
                return back()->withInput()->with('error', 'This is the only active Super Admin. Make someone else Super Admin first.');
            }
        }

        DB::transaction(function () use ($member, $data, $before) {
            $member->fill(['name' => $data['name'], 'email' => $data['email'] ?? null, 'phone' => $data['phone'] ?? null])->save();
            $member->syncRoles($data['roles']);

            Audit::record('user', 'staff.updated', "Staff account updated: {$member->name}", $member, ['roles' => $before], ['roles' => $data['roles']]);
        });

        return redirect()->route('admin.staff.index')->with('success', "{$member->name} updated.");
    }

    public function resetPassword(Request $request, User $member): RedirectResponse
    {
        abort_unless($member->isStaff(), 404);

        $temporary = Str::password(12, symbols: false);
        $member->forceFill(['password' => $temporary, 'must_change_password' => true])->save();
        DB::table('sessions')->where('user_id', $member->id)->delete();

        Audit::record('security', 'staff.password-reset', "Temporary password issued: {$member->name}", $member);

        return back()->with('success', "Temporary password issued for {$member->name}.")->with('temporary_password', $temporary);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?User $member = null): array
    {
        $request->merge(['phone' => Phone::clean($request->input('phone'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required_without:phone', 'nullable', 'email:rfc', 'max:150', Rule::unique('users', 'email')->ignore($member?->id)],
            'phone' => ['required_without:email', 'nullable', 'regex:'.Phone::GHANA_MOBILE, Rule::unique('users', 'phone')->ignore($member?->id)],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [Rule::in($this->staffRoles()->pluck('name')->all())],
        ], ['phone.regex' => 'Enter a Ghana mobile number, e.g. 024 123 4567.']);
    }

    private function staffRoles()
    {
        return Role::whereNotIn('name', Permissions::ACCOUNT_ROLES)->orderBy('name')->get();
    }
}
