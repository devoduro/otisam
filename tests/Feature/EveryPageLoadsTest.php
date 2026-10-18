<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Smoke test in the jhs_portal style: every page renders for the account that may see it. */
class EveryPageLoadsTest extends TestCase
{
    public function test_public_and_auth_pages_load(): void
    {
        foreach (['home', 'terms', 'privacy', 'login', 'register', 'teachers.register', 'password.request'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_admin_pages_load_for_super_admin(): void
    {
        $owner = User::factory()->staff()->create();
        $student = User::factory()->student()->create();
        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        User::factory()->teacher()->create();

        $this->actingAs($owner);
        foreach ([
            route('admin.dashboard'),
            route('admin.students.index'),
            route('admin.students.index', ['q' => '024', 'status' => 'active', 'verified' => 'yes']),
            route('admin.students.show', $student),
            route('admin.teachers.index'),
            route('admin.teachers.index', ['verification' => 'pending']),
            route('admin.teachers.show', $teacher),
            route('admin.staff.index'),
            route('admin.staff.create'),
            route('admin.staff.edit', $owner),
            route('admin.roles.index'),
            route('admin.roles.create'),
            route('admin.roles.edit', Role::findByName('Content Manager')),
            route('admin.audit.index'),
            route('password.change'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_student_and_teacher_pages_load(): void
    {
        $this->actingAs(User::factory()->student()->create())->get(route('learn.dashboard'))->assertOk();

        $teacher = User::factory()->teacher()->create();
        $this->actingAs($teacher)->get(route('teach.dashboard'))->assertOk();
        $this->actingAs($teacher)->get(route('teach.profile.edit'))->assertOk();
    }

    public function test_wrong_record_type_is_not_found(): void
    {
        $owner = User::factory()->staff()->create();
        $student = User::factory()->student()->create();

        $this->actingAs($owner)->get(route('admin.teachers.show', $student))->assertNotFound();
        $this->get(route('admin.students.show', $owner))->assertNotFound();
    }
}
