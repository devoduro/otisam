<?php

namespace App\Http\Controllers\Learn;

use App\Http\Controllers\Controller;
use App\Services\Access\Entitlements;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, Entitlements $entitlements): View
    {
        $user = $request->user()->load('studentProfile.classLevel', 'studentProfile.programme');

        return view('learn.dashboard', ['user' => $user, 'profile' => $user->studentProfile, 'subscription' => $entitlements->currentSubscription($user)]);
    }
}
