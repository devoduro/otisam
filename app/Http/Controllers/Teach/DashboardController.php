<?php

namespace App\Http\Controllers\Teach;

use App\Http\Controllers\Controller;
use App\Models\Classroom\Classroom;
use App\Models\Classroom\Submission;
use App\Models\Classroom\TeacherMaterial;
use App\Support\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('teacherProfile', 'permissions');
        $classIds = Classroom::where('teacher_id', $user->id)->whereNull('archived_at')->pluck('id');
        $referrals = $user->referrals()->with('teacherProfile')->get();

        return view('teach.dashboard', [
            'user' => $user,
            'profile' => $user->teacherProfile,
            'capabilities' => array_values(array_intersect(Permissions::TEACHER_CAPABILITIES, $user->permissions->pluck('name')->all())),
            'stats' => [
                'classes' => $classIds->count(),
                'students' => DB::table('classroom_student')->whereIn('classroom_id', $classIds)->distinct()->count('student_id'),
                'to_mark' => Submission::whereNotNull('submitted_at')->whereNull('graded_at')
                    ->whereHas('assignment', fn ($q) => $q->where('teacher_id', $user->id))->count(),
                'shared_materials' => TeacherMaterial::whereIn('visibility', ['teachers', 'public'])->count(),
            ],
            'referralLink' => route('referral', $user->referralCode()),
            'referrals' => [
                'total' => $referrals->count(),
                'approved' => $referrals->filter(fn ($r) => $r->teacherProfile?->isApproved())->count(),
            ],
        ]);
    }
}
