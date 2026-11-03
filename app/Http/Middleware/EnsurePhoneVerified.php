<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Students and teachers must confirm their phone number with an SMS code before going further.
 * Student accounts a teacher created from a class list have no phone and are exempt.
 */
class EnsurePhoneVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isStaff() && ! $user->isClassManaged() && $user->phone_verified_at === null) {
            return redirect()->route('verify.phone');
        }

        return $next($request);
    }
}
