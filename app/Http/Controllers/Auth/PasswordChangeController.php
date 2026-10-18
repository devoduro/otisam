<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/** Change password while signed in; forced on first login for accounts made by an admin. */
class PasswordChangeController extends Controller
{
    public function edit(Request $request): View
    {
        return view('auth.change-password', ['forced' => $request->user()->must_change_password]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(10)->letters()->numbers()],
        ]);

        $user = $request->user();
        $user->forceFill(['password' => $data['password'], 'must_change_password' => false])->save();

        Auth::logoutOtherDevices($data['password']);
        Audit::record('security', 'password.changed', "Password changed: {$user->name}", $user);

        return redirect()->route($user->homeRoute())->with('success', 'Your password has been changed.');
    }
}
