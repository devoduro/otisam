<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Invite links (/r/{code}). Teachers share theirs with colleagues: the code is remembered for
 * 30 days and credited when the colleague registers as a teacher.
 */
class ReferralController extends Controller
{
    public const COOKIE = 'otisam_ref';

    public function __invoke(Request $request, string $code): RedirectResponse
    {
        $referrer = User::where('referral_code', strtolower($code))->first();
        $response = redirect()->route($referrer?->isTeacher() ? 'teachers.register' : 'register');

        if ($referrer && ! $request->user()) {
            $request->session()->put('referral_code', $referrer->referral_code);
            $response->withCookie(cookie(self::COOKIE, $referrer->referral_code, 60 * 24 * 30));
        }

        return $response;
    }

    /** Records who invited a newly registered user, then forgets the code. */
    public static function credit(Request $request, User $user): void
    {
        $referrer = self::referrerFor($request);
        if ($referrer && $referrer->id !== $user->id && ! $user->referred_by) {
            $user->forceFill(['referred_by' => $referrer->id])->save();
        }
        $request->session()->forget('referral_code');
        cookie()->queue(cookie()->forget(self::COOKIE));
    }

    /** The user who invited the person registering in this request, if any. */
    public static function referrerFor(Request $request): ?User
    {
        $code = $request->session()->get('referral_code') ?? $request->cookie(self::COOKIE);

        return is_string($code) && $code !== '' ? User::where('referral_code', $code)->first() : null;
    }
}
