<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Signs staff out after platform.staff_idle_minutes without a request (from jhs_portal). */
class StaffSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isStaff()) {
            return $next($request);
        }

        $last = $request->session()->get('staff_last_activity');

        if ($last && time() - $last > config('platform.staff_idle_minutes') * 60) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('status', 'You were signed out after a period of inactivity.');
        }

        $request->session()->put('staff_last_activity', time());

        return $next($request);
    }
}
