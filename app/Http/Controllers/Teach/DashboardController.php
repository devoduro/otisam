<?php

namespace App\Http\Controllers\Teach;

use App\Http\Controllers\Controller;
use App\Support\Permissions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('teacherProfile', 'permissions');

        return view('teach.dashboard', [
            'user' => $user,
            'profile' => $user->teacherProfile,
            'capabilities' => array_values(array_intersect(Permissions::TEACHER_CAPABILITIES, $user->permissions->pluck('name')->all())),
        ]);
    }
}
