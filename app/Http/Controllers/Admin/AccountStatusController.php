<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Audit;
use App\Support\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Suspend / reactivate any account, with the guard rails staff accounts need. */
class AccountStatusController extends Controller
{
    public function suspend(Request $request, User $user): RedirectResponse
    {
        $this->authorizeTarget($request, $user);

        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot suspend your own account.');
        }
        if ($user->isSuperAdmin() && self::activeSuperAdmins() <= 1) {
            return back()->with('error', 'This is the only active Super Admin. Make someone else Super Admin first.');
        }

        $data = $request->validate(['reason' => ['required', 'string', 'max:300']]);

        DB::transaction(function () use ($user, $data) {
            $old = $user->status;
            $user->forceFill(['status' => UserStatus::Suspended])->save();
            DB::table('sessions')->where('user_id', $user->id)->delete();

            Audit::record('user', 'suspend', "Suspended {$user->account_type->label()}: {$user->name}", $user,
                ['status' => $old], ['status' => UserStatus::Suspended, 'reason' => $data['reason']]);
        });

        return back()->with('success', "{$user->name} has been suspended and signed out.");
    }

    public function activate(Request $request, User $user): RedirectResponse
    {
        $this->authorizeTarget($request, $user);

        $old = $user->status;
        $user->forceFill(['status' => UserStatus::Active])->save();
        Audit::record('user', 'activate', "Reactivated {$user->account_type->label()}: {$user->name}", $user, ['status' => $old], ['status' => UserStatus::Active]);

        return back()->with('success', "{$user->name} is active again.");
    }

    public static function activeSuperAdmins(): int
    {
        return User::role(Permissions::SUPER_ADMIN)->where('status', UserStatus::Active->value)->count();
    }

    /** Students and teachers need users.suspend; staff accounts need staff.manage (Super Admin). */
    private function authorizeTarget(Request $request, User $user): void
    {
        abort_unless($request->user()->can($user->isStaff() ? 'staff.manage' : 'users.suspend'), 403);
    }
}
