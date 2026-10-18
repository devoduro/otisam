<?php

namespace App\Http\Controllers\Teach;

use App\Enums\TeacherVerificationStatus;
use App\Http\Controllers\Controller;
use App\Services\Audit;
use App\Services\Library\InspectedUpload;
use App\Services\Library\UploadInspector;
use App\Services\Library\UploadRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Teachers update their professional details. When an admin asked for more information, saving
 * the profile puts it back in the review queue.
 */
class ProfileController extends Controller
{
    public function __construct(private readonly UploadInspector $inspector) {}

    public function edit(Request $request): View
    {
        return view('teach.profile', ['profile' => $request->user()->teacherProfile]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->teacherProfile;

        $account = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email:rfc', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
        ]);
        $data = $request->validate([
            'staff_number' => ['nullable', 'string', 'max:30', Rule::unique('teacher_profiles', 'staff_number')->ignore($profile->user_id, 'user_id')],
            'current_rank' => ['nullable', 'string', 'max:80'],
            'school_name' => ['required', 'string', 'max:150'],
            'region' => ['required', Rule::in(config('platform.regions'))],
            'district' => ['nullable', 'string', 'max:80'],
            'subjects_taught' => ['required', 'string', 'max:255'],
        ]);
        $public = $request->validate([
            'is_public' => ['nullable', 'boolean'],
            'headline' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'years_teaching' => ['nullable', 'integer', 'min:0', 'max:60'],
            'photo' => ['nullable', 'file', 'max:4096'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);
        $photo = $request->hasFile('photo') ? $this->inspectPhoto($request) : null;

        // Everything is validated before anything is saved.
        $user->fill(['name' => trim($account['name']), 'email' => filled($account['email'] ?? null) ? mb_strtolower($account['email']) : null])->save();
        $profile->fill($data);

        // Only the details an administrator checks send a profile back for review.
        $resubmitted = false;
        if (in_array($profile->verification_status, [TeacherVerificationStatus::MoreInfo, TeacherVerificationStatus::Rejected], true) && $profile->isDirty()) {
            $profile->verification_status = TeacherVerificationStatus::Pending;
            $resubmitted = true;
        }

        $profile->fill([
            'is_public' => $request->boolean('is_public'),
            'headline' => $public['headline'] ?? null,
            'bio' => $public['bio'] ?? null,
            'years_teaching' => $public['years_teaching'] ?? null,
        ]);
        if ($profile->is_public) {
            $profile->setRelation('user', $user);
            $profile->ensureSlug();
        }
        if ($photo || $request->boolean('remove_photo')) {
            if ($profile->photo_path && str_starts_with($profile->photo_path, 'uploads/teachers/')) {
                @unlink(public_path($profile->photo_path));
            }
            $profile->photo_path = $photo ? $this->storePhoto($photo) : null;
        }

        $profile->save();

        if ($resubmitted) {
            Audit::record('user', 'teacher.resubmitted', "Teacher resubmitted profile for review: {$request->user()->name}", $request->user());
        }

        return redirect()->route('teach.dashboard')->with('status', $resubmitted
            ? 'Thanks. Your profile is back in the review queue.'
            : ($profile->isListed() ? 'Profile saved. It is listed on the Teachers page.' : 'Profile saved.'));
    }

    /** Photos are re-encoded by the upload inspector (strips anything hidden in the file). */
    private function inspectPhoto(Request $request): InspectedUpload
    {
        try {
            $upload = $this->inspector->inspect($request->file('photo'));
        } catch (UploadRejected $e) {
            throw ValidationException::withMessages(['photo' => $e->getMessage()]);
        }
        if (! in_array($upload->extension, ['jpg', 'png', 'webp'], true)) {
            throw ValidationException::withMessages(['photo' => 'Upload a photo (JPG, PNG or WebP).']);
        }

        return $upload;
    }

    /** Public image, so it lives under public/ (works on cPanel without a storage symlink). */
    private function storePhoto(InspectedUpload $upload): string
    {
        $dir = public_path('uploads/teachers');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $relative = 'uploads/teachers/'.Str::lower((string) Str::ulid()).'.'.$upload->extension;
        copy($upload->path, public_path($relative));

        return $relative;
    }
}
