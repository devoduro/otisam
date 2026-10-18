<?php

namespace App\Services\Auth;

use App\Enums\TeacherVerificationStatus;
use App\Models\User;
use App\Services\Audit;
use App\Services\Notifications\Notifier;
use App\Support\Permissions;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Teacher approval workflow: pending → approved | rejected | more_info (→ pending again when the
 * teacher updates their profile). Approval swaps the "Teacher (Pending)" role for "Teacher";
 * extra capabilities are granted separately (BLUEPRINT §3.1).
 */
class TeacherVerificationService
{
    public function __construct(private readonly Notifier $notifier)
    {
    }

    public function decide(User $teacher, TeacherVerificationStatus $decision, ?string $note, User $by): void
    {
        if (!$teacher->isTeacher() || !$teacher->teacherProfile) {
            throw new InvalidArgumentException('Only teacher accounts can be verified.');
        }
        if ($decision === TeacherVerificationStatus::Pending) {
            throw new InvalidArgumentException('Pending is not a decision.');
        }

        DB::transaction(function () use ($teacher, $decision, $note, $by) {
            $profile = $teacher->teacherProfile;
            $old = $profile->verification_status;

            $profile->forceFill([
                'verification_status' => $decision,
                'verified_by' => $by->id,
                'verified_at' => now(),
                'decision_note' => $note,
            ])->save();

            if ($decision === TeacherVerificationStatus::Approved) {
                $teacher->syncRoles([Permissions::TEACHER]);
            } else {
                // Rejected or waiting for information: back to the restricted role and no capabilities.
                $teacher->syncRoles([Permissions::TEACHER_PENDING]);
                $granted = array_intersect(Permissions::TEACHER_CAPABILITIES, $teacher->getDirectPermissions()->pluck('name')->all());
                if ($granted) {
                    $teacher->revokePermissionTo(array_values($granted));
                }
            }

            Audit::record('user', "teacher.{$decision->value}", "Teacher {$decision->label()}: {$teacher->name}", $teacher,
                ['verification_status' => $old], ['verification_status' => $decision, 'note' => $note]);

            $app = config('app.name');
            [$subject, $body] = match ($decision) {
                TeacherVerificationStatus::Approved => ["Your {$app} teacher account is approved", "Hello {$teacher->name}, your {$app} teacher account has been approved. You can now sign in and use the teacher resources."],
                TeacherVerificationStatus::Rejected => ["Your {$app} teacher registration", "Hello {$teacher->name}, we could not approve your {$app} teacher registration." . ($note ? " Reason: {$note}" : '')],
                TeacherVerificationStatus::MoreInfo => ["More information needed for your {$app} teacher account", "Hello {$teacher->name}, we need more information to approve your {$app} teacher account." . ($note ? " {$note}" : '') . ' Please sign in and update your profile.'],
            };

            // Keyed on the decision time so a later, different decision still notifies.
            $this->notifier->toUser($teacher, "teacher.{$decision->value}", 'transactional', $subject, $body,
                "teacher.{$decision->value}:{$teacher->id}:" . $profile->verified_at->timestamp);
        });
    }

    /** @param array<int, string> $capabilities */
    public function setCapabilities(User $teacher, array $capabilities): void
    {
        if (!$teacher->isApprovedTeacher()) {
            throw new InvalidArgumentException('Capabilities can only be granted to approved teachers.');
        }

        $capabilities = array_values(array_intersect($capabilities, Permissions::TEACHER_CAPABILITIES));
        $before = array_values(array_intersect(Permissions::TEACHER_CAPABILITIES, $teacher->getDirectPermissions()->pluck('name')->all()));

        DB::transaction(function () use ($teacher, $capabilities, $before) {
            if ($remove = array_values(array_diff($before, $capabilities))) {
                $teacher->revokePermissionTo($remove);
            }
            if ($add = array_values(array_diff($capabilities, $before))) {
                $teacher->givePermissionTo($add);
            }

            Audit::record('user', 'teacher.capabilities', "Teacher capabilities changed: {$teacher->name}", $teacher,
                ['capabilities' => $before], ['capabilities' => $capabilities]);
        });
    }
}
