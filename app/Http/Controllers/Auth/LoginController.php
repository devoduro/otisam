<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** One sign-in page for every account type: phone number or email + password. */
class LoginController extends Controller
{
    /** Hash of a random string nobody knows; checked when no account matches. */
    private const DUMMY_HASH = '$2y$12$tsZYAch7gK8VAr4KBiQBJu2bcHbOs5A52XhQt5ve1SBYzoRt3DlfO';

    public function show(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string', 'max:200'],
        ]);

        $identifier = Str::lower(trim($data['login']));
        $throttleKey = 'login:'.sha1($identifier).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'login' => 'Too many sign-in attempts. Try again in '.RateLimiter::availableIn($throttleKey).' seconds.',
            ]);
        }

        $user = User::findByLogin($identifier);
        // Hash even when no account matches, so response time does not reveal which numbers exist.
        $valid = $user
            ? Hash::check($data['password'], $user->password)
            : Hash::check($data['password'], self::DUMMY_HASH) && false;

        LoginEvent::create([
            'user_id' => $valid ? $user->id : null,
            'identifier_hash' => hash('sha256', $identifier),
            'success' => $valid,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
        ]);

        if (! $valid) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages(['login' => 'The phone number/email or password is incorrect.']);
        }

        if (! $user->isActive()) {
            throw ValidationException::withMessages(['login' => 'This account is not active. Please contact support.']);
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route($user->homeRoute()));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
