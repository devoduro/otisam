<?php

namespace App\Http\Controllers\Teach;

use App\Enums\TeacherVerificationStatus;
use App\Http\Controllers\Controller;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Teachers update their professional details. When an admin asked for more information, saving
 * the profile puts it back in the review queue.
 */
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('teach.profile', ['profile' => $request->user()->teacherProfile]);
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = $request->user()->teacherProfile;

        $data = $request->validate([
            'staff_number' => ['nullable', 'string', 'max:30', Rule::unique('teacher_profiles', 'staff_number')->ignore($profile->user_id, 'user_id')],
            'current_rank' => ['nullable', 'string', 'max:80'],
            'school_name' => ['required', 'string', 'max:150'],
            'region' => ['required', Rule::in(config('platform.regions'))],
            'district' => ['nullable', 'string', 'max:80'],
            'subjects_taught' => ['required', 'string', 'max:255'],
        ]);

        $profile->fill($data);

        $resubmitted = false;
        if (in_array($profile->verification_status, [TeacherVerificationStatus::MoreInfo, TeacherVerificationStatus::Rejected], true) && $profile->isDirty()) {
            $profile->verification_status = TeacherVerificationStatus::Pending;
            $resubmitted = true;
        }

        $profile->save();

        if ($resubmitted) {
            Audit::record('user', 'teacher.resubmitted', "Teacher resubmitted profile for review: {$request->user()->name}", $request->user());
        }

        return redirect()->route('teach.dashboard')->with('status', $resubmitted
            ? 'Thanks. Your profile is back in the review queue.'
            : 'Profile saved.');
    }
}
