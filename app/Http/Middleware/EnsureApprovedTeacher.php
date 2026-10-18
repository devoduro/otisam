<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Classes, assignments and shared materials are for teachers an administrator has verified. */
class EnsureApprovedTeacher
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isApprovedTeacher()) {
            return redirect()->route('teach.dashboard')->with('error', 'This part of the teacher area opens once an administrator has approved your account.');
        }

        return $next($request);
    }
}
