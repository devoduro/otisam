<?php

namespace App\Http\Controllers\Learn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('studentProfile');

        return view('learn.dashboard', ['user' => $user, 'profile' => $user->studentProfile]);
    }
}
