<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps each account type in its own area: `account:staff` for /admin, `account:teacher` for
 * /teach, `account:student` for /learn. A wrong-area request goes to the user's own dashboard.
 * Fine-grained access inside an area is still decided by permissions.
 */
class EnsureAccountType
{
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! in_array($user->account_type->value, $types, true)) {
            if ($request->expectsJson()) {
                abort(403);
            }

            return redirect()->route($user->homeRoute())->with('error', 'That area is not available to your account.');
        }

        return $next($request);
    }
}
