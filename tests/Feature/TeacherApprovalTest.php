<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Jobs\SendNotificationMessage;
use App\Models\AuditLog;
use App\Models\NotificationMessage;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/** Acceptance criteria 3 (part) and 4: teacher registration, approval and capabilities. */
class TeacherApprovalTest extends TestCase
{
    public function test_teacher_registers_with_just_phone_and_password_and_waits_in_pending_state(): void
    {
        $this->get('/teachers')->assertOk()->assertSee('Are you a teacher? Join free');

        $this->post(route('teachers.register.store'), [
            'phone' => '0201112222',
            'password' => 'teach1234',
            'password_confirmation' => 'teach1234',
        ])->assertRedirect(route('verify.phone'));

        $teacher = User::where('phone', '0201112222')->firstOrFail();
        $this->assertTrue($teacher->hasRole('Teacher (Pending)'));
        $this->assertStringStartsWith('Teacher ', $teacher->name);
        $this->assertFalse($teacher->teacherProfile->isComplete());
        $this->assertSame(TeacherVerificationStatus::Pending, $teacher->teacherProfile->verification_status);
        $this->assertSame([], $teacher->getAllPermissions()->pluck('name')->all(), 'A pending teacher holds no permissions.');

        $this->post(route('verify.phone.check'), ['code' => $this->lastCodeSentTo('0201112222')]);
        $this->get(route('teach.dashboard'))->assertOk()->assertSee('Your registration is being reviewed')->assertSee('Complete your profile');

        // A teacher can never reach the admin console, approved or not.
        $this->get(route('admin.dashboard'))->assertRedirect(route('teach.dashboard'));

        // Name, school and subjects are added later on the profile.
        $this->put(route('teach.profile.update'), [
            'name' => 'Kwame Asante', 'email' => 'Kwame@example.com',
            'school_name' => 'Prempeh College', 'region' => 'Ashanti', 'subjects_taught' => 'Physics',
        ])->assertRedirect(route('teach.dashboard'));

        $teacher->refresh();
        $this->assertSame(['Kwame Asante', 'kwame@example.com'], [$teacher->name, $teacher->email]);
        $this->assertTrue($teacher->teacherProfile->isComplete());
        $this->get(route('teach.dashboard'))->assertDontSee('Complete your profile');
    }

    public function test_region_and_district_are_optional_but_checked_when_given(): void
    {
        $this->post(route('teachers.register.store'), [
            'phone' => '0201113333', 'password' => 'teach1234', 'password_confirmation' => 'teach1234', 'region' => 'Atlantis',
        ])->assertSessionHasErrors('region');

        $this->post(route('teachers.register.store'), [
            'phone' => '0201113333', 'password' => 'teach1234', 'password_confirmation' => 'teach1234', 'region' => 'Volta', 'district' => 'Ho',
        ])->assertRedirect(route('verify.phone'));

        $profile = User::where('phone', '0201113333')->firstOrFail()->teacherProfile;
        $this->assertSame(['Volta', 'Ho'], [$profile->region, $profile->district]);
    }

    public function test_admin_pages_handle_an_incomplete_teacher_profile(): void
    {
        $this->post(route('teachers.register.store'), ['phone' => '0201114444', 'password' => 'teach1234', 'password_confirmation' => 'teach1234']);
        $teacher = User::where('phone', '0201114444')->firstOrFail();
        $teacher->forceFill(['phone_verified_at' => now()])->save();
        auth()->logout();

        $admin = User::factory()->staff('Administrator')->create();
        $this->actingAs($admin)->get(route('admin.teachers.show', $teacher))->assertOk()->assertSee('Profile incomplete');
        $this->get(route('admin.teachers.index'))->assertOk();
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_admin_approves_teacher_grants_capabilities_and_teacher_is_notified(): void
    {
        $admin = User::factory()->staff('Administrator')->create();
        $teacher = User::factory()->teacher()->create(['email' => 't@example.com']);

        $this->actingAs($admin)
            ->post(route('admin.teachers.decide', $teacher), ['decision' => 'approved'])
            ->assertRedirect(route('admin.teachers.show', $teacher));

        $teacher->refresh();
        $this->assertTrue($teacher->hasRole('Teacher'));
        $this->assertFalse($teacher->hasRole('Teacher (Pending)'));
        $this->assertTrue($teacher->teacherProfile->isApproved());
        $this->assertSame($admin->id, $teacher->teacherProfile->verified_by);

        // SMS + email queued and, with the sync test queue, sent.
        $messages = NotificationMessage::where('user_id', $teacher->id)->where('event_key', 'teacher.approved')->get();
        $this->assertEqualsCanonicalizing(['sms', 'mail'], $messages->pluck('channel')->all());
        $this->assertTrue($messages->every(fn ($m) => $m->status === 'sent'));

        $this->put(route('admin.teachers.capabilities.update', $teacher), ['capabilities' => ['teach.author-questions', 'users.manage']])
            ->assertSessionHasErrors('capabilities.1');

        $this->put(route('admin.teachers.capabilities.update', $teacher), ['capabilities' => ['teach.author-questions', 'teach.upload-resources']])
            ->assertSessionHasNoErrors();
        $this->assertEqualsCanonicalizing(['teach.author-questions', 'teach.upload-resources'], $teacher->fresh()->getDirectPermissions()->pluck('name')->all());

        $this->put(route('admin.teachers.capabilities.update', $teacher), ['capabilities' => ['teach.author-questions']]);
        $this->assertSame(['teach.author-questions'], $teacher->fresh()->getDirectPermissions()->pluck('name')->all());

        $this->assertTrue(AuditLog::where('action', 'teacher.approved')->where('auditable_id', $teacher->id)->exists());
        $this->assertTrue(AuditLog::where('action', 'teacher.capabilities')->exists());
    }

    public function test_capabilities_cannot_be_granted_before_approval(): void
    {
        $admin = User::factory()->staff('Administrator')->create();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($admin)->put(route('admin.teachers.capabilities.update', $teacher), ['capabilities' => ['teach.publish-tutorials']])
            ->assertSessionHas('error');
        $this->assertFalse($teacher->fresh()->hasPermissionTo('teach.publish-tutorials'));
    }

    public function test_rejection_needs_a_reason_and_removes_capabilities(): void
    {
        $admin = User::factory()->staff('Administrator')->create();
        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $teacher->givePermissionTo('teach.publish-tutorials');

        $this->actingAs($admin)->post(route('admin.teachers.decide', $teacher), ['decision' => 'rejected'])->assertSessionHasErrors('note');

        $this->post(route('admin.teachers.decide', $teacher), ['decision' => 'rejected', 'note' => 'Staff number does not match.']);

        $teacher->refresh();
        $this->assertSame(TeacherVerificationStatus::Rejected, $teacher->teacherProfile->verification_status);
        $this->assertTrue($teacher->hasRole('Teacher (Pending)'));
        $this->assertFalse($teacher->hasPermissionTo('teach.publish-tutorials'));
    }

    public function test_more_info_then_profile_update_returns_teacher_to_the_queue(): void
    {
        $admin = User::factory()->staff('Administrator')->create();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($admin)->post(route('admin.teachers.decide', $teacher), ['decision' => 'more_info', 'note' => 'Please add your staff number.']);
        $this->assertSame(TeacherVerificationStatus::MoreInfo, $teacher->fresh()->teacherProfile->verification_status);

        $this->actingAs($teacher->fresh())->put(route('teach.profile.update'), [
            'name' => $teacher->name, 'school_name' => 'Accra Academy', 'region' => 'Greater Accra', 'subjects_taught' => 'Core Mathematics', 'staff_number' => 'GES-12345',
        ])->assertRedirect(route('teach.dashboard'));

        $this->assertSame(TeacherVerificationStatus::Pending, $teacher->fresh()->teacherProfile->verification_status);
    }

    public function test_staff_without_permission_cannot_approve(): void
    {
        $finance = User::factory()->staff('Finance Officer')->create();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($finance)->post(route('admin.teachers.decide', $teacher), ['decision' => 'approved'])->assertForbidden();
        $this->actingAs($finance)->get(route('admin.teachers.index'))->assertForbidden();
        $this->assertFalse($teacher->fresh()->teacherProfile->isApproved());
    }

    public function test_notifications_are_queued_until_the_transaction_commits(): void
    {
        Queue::fake();
        $admin = User::factory()->staff('Administrator')->create();
        $teacher = User::factory()->teacher()->create(['email' => null]);

        $this->actingAs($admin)->post(route('admin.teachers.decide', $teacher), ['decision' => 'approved']);

        Queue::assertPushed(SendNotificationMessage::class, 1);
    }
}
