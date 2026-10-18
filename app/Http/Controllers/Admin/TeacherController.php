<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountType;
use App\Enums\TeacherVerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\TeacherVerificationService;
use App\Support\Permissions;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'verification' => ['nullable', Rule::enum(TeacherVerificationStatus::class)],
            'status' => ['nullable', 'in:active,suspended,deactivated'],
        ]);

        $teachers = User::query()
            ->where('account_type', AccountType::Teacher->value)
            ->with('teacherProfile')
            ->when($filters['q'] ?? null, function ($query, $q) {
                $phone = Phone::clean($q);
                $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")
                    ->orWhereHas('teacherProfile', fn ($p) => $p->where('staff_number', 'like', "%{$q}%")->orWhere('school_name', 'like', "%{$q}%"))
                    ->when($phone && strlen($phone) >= 4, fn ($p) => $p->orWhere('phone', 'like', "%{$phone}%")));
            })
            ->when($filters['verification'] ?? null, fn ($query, $v) => $query->whereHas('teacherProfile', fn ($p) => $p->where('verification_status', $v)))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderByRaw("(SELECT verification_status = 'pending' FROM teacher_profiles WHERE teacher_profiles.user_id = users.id) DESC")
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.teachers.index', compact('teachers', 'filters'));
    }

    public function show(User $teacher): View
    {
        abort_unless($teacher->isTeacher(), 404);

        $teacher->load('teacherProfile.verifier', 'permissions');

        return view('admin.teachers.show', [
            'teacher' => $teacher,
            'capabilities' => Permissions::TEACHER_CAPABILITIES,
            'granted' => $teacher->permissions->pluck('name')->all(),
        ]);
    }

    public function decide(Request $request, User $teacher, TeacherVerificationService $service): RedirectResponse
    {
        abort_unless($teacher->isTeacher(), 404);

        $data = $request->validate([
            'decision' => ['required', Rule::in([TeacherVerificationStatus::Approved->value, TeacherVerificationStatus::Rejected->value, TeacherVerificationStatus::MoreInfo->value])],
            'note' => ['nullable', 'required_unless:decision,approved', 'string', 'max:500'],
        ], ['note.required_unless' => 'Tell the teacher why, so they know what to do next.']);

        $decision = TeacherVerificationStatus::from($data['decision']);
        $service->decide($teacher, $decision, $data['note'] ?? null, $request->user());

        return redirect()->route('admin.teachers.show', $teacher)->with('success', "{$teacher->name}: {$decision->label()}. The teacher has been notified.");
    }

    public function capabilities(Request $request, User $teacher, TeacherVerificationService $service): RedirectResponse
    {
        abort_unless($teacher->isTeacher(), 404);
        $teacher->load('teacherProfile');

        $data = $request->validate([
            'capabilities' => ['array'],
            'capabilities.*' => [Rule::in(Permissions::TEACHER_CAPABILITIES)],
        ]);

        if (!$teacher->isApprovedTeacher()) {
            return back()->with('error', 'Approve the teacher before granting capabilities.');
        }

        $service->setCapabilities($teacher, $data['capabilities'] ?? []);

        return back()->with('success', 'Capabilities updated.');
    }
}
