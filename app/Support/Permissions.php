<?php

namespace App\Support;

/**
 * The permission catalogue and default roles (BLUEPRINT §3.2), following jhs_portal's
 * RolesAndPermissionsSeeder: one list in code, re-seedable, validated against by the role editor.
 */
final class Permissions
{
    public const SUPER_ADMIN = 'Super Admin';

    public const TEACHER = 'Teacher';

    public const TEACHER_PENDING = 'Teacher (Pending)';

    public const STUDENT = 'Student';

    /** Group label => [permission => description]. */
    public const GROUPS = [
        'Users' => [
            'users.view' => 'View students and teachers',
            'users.manage' => 'Edit students and teachers',
            'users.suspend' => 'Suspend and reactivate accounts',
            'teachers.verify' => 'Approve or reject teacher registrations',
            'teachers.grant-capabilities' => 'Grant teacher capabilities',
            'staff.manage' => 'Create and manage staff accounts',
            'roles.manage' => 'Create and edit roles',
        ],
        'Curriculum' => [
            'curriculum.view-admin' => 'View curriculum admin',
            'curriculum.manage' => 'Edit levels, subjects and topics',
        ],
        'Resources' => [
            'resources.manage' => 'Upload and edit resources',
            'resources.publish' => 'Publish and archive resources',
            'resources.moderate' => 'Review submitted resources',
            'resources.bulk-import' => 'Bulk upload resources',
            'takedowns.manage' => 'Handle copyright takedown requests',
        ],
        'Questions' => [
            'questions.manage' => 'Create and edit questions',
            'questions.review' => 'Approve submitted questions',
            'questions.import' => 'Import questions',
            'questions.view-keys' => 'See answer keys in the bank',
        ],
        'Assessments' => [
            'assessments.manage' => 'Create quizzes and exams',
            'assessments.mark' => 'Mark theory answers',
            'results.view-all' => 'View all results',
            'results.release' => 'Release results',
        ],
        'Promotion exams' => [
            'promotion-exams.manage' => 'Manage promotion exams and their question pools',
            'promotion-exams.release-results' => 'Approve release of promotion exam results',
        ],
        'Tutorials' => [
            'tutorials.manage' => 'Create and edit tutorials',
            'tutorials.moderate' => 'Approve submitted tutorials',
        ],
        'Commerce' => [
            'plans.manage' => 'Manage subscription plans',
            'coupons.manage' => 'Manage coupons',
            'payments.view' => 'View payments',
            'payments.reconcile' => 'Re-verify and reconcile payments',
            'subscriptions.adjust' => 'Grant, extend or revoke subscriptions',
            'refunds.record' => 'Record refunds',
        ],
        'Communication' => [
            'notifications.send' => 'Send announcements and messages',
            'notifications.view-logs' => 'View email and SMS delivery logs',
            'announcements.manage' => 'Manage announcements',
            'templates.manage' => 'Edit message templates',
        ],
        'Reports' => [
            'reports.view' => 'View reports',
            'reports.export' => 'Export reports',
        ],
        'System' => [
            'settings.manage' => 'Edit platform settings',
            'integrations.status' => 'View integration status',
            'audit.view' => 'View the audit log',
            'system.maintenance' => 'Maintenance mode and system tools',
        ],
        'Teacher capabilities' => [
            'teach.upload-resources' => 'Teacher: upload resources for review',
            'teach.author-questions' => 'Teacher: write questions for review',
            'teach.create-assessments' => 'Teacher: create class assessments',
            'teach.publish-tutorials' => 'Teacher: publish tutorials without review',
            'teach.view-own-student-results' => 'Teacher: view results of their own assessments',
        ],
    ];

    /** Only the Super Admin role may hold these; the role editor refuses to grant them. */
    public const SUPER_ADMIN_ONLY = ['staff.manage', 'roles.manage', 'integrations.status', 'system.maintenance'];

    /** Capabilities an administrator can switch on per approved teacher. */
    public const TEACHER_CAPABILITIES = [
        'teach.upload-resources',
        'teach.author-questions',
        'teach.create-assessments',
        'teach.publish-tutorials',
        'teach.view-own-student-results',
    ];

    /** Built-in roles => permissions. Super Admin gets everything regardless of this list. */
    public const DEFAULT_ROLES = [
        self::SUPER_ADMIN => [],
        'Administrator' => [
            'users.view', 'users.manage', 'users.suspend', 'teachers.verify', 'teachers.grant-capabilities',
            'curriculum.view-admin', 'curriculum.manage',
            'resources.manage', 'resources.publish', 'resources.moderate', 'resources.bulk-import', 'takedowns.manage',
            'questions.manage', 'questions.review', 'questions.import', 'questions.view-keys',
            'assessments.manage', 'assessments.mark', 'results.view-all', 'results.release',
            'promotion-exams.manage', 'promotion-exams.release-results',
            'tutorials.manage', 'tutorials.moderate',
            'plans.manage', 'coupons.manage', 'payments.view', 'payments.reconcile', 'subscriptions.adjust', 'refunds.record',
            'notifications.send', 'notifications.view-logs', 'announcements.manage', 'templates.manage',
            'reports.view', 'reports.export', 'settings.manage', 'audit.view',
        ],
        'Content Manager' => [
            'curriculum.view-admin', 'curriculum.manage',
            'resources.manage', 'resources.publish', 'resources.moderate', 'resources.bulk-import',
            'questions.manage', 'questions.review', 'questions.import', 'questions.view-keys',
            'tutorials.manage', 'tutorials.moderate', 'reports.view',
        ],
        'Exams Officer' => [
            'curriculum.view-admin', 'questions.manage', 'questions.review', 'questions.import', 'questions.view-keys',
            'assessments.manage', 'assessments.mark', 'results.view-all', 'results.release',
            'promotion-exams.manage', 'reports.view', 'reports.export',
        ],
        'Finance Officer' => [
            'plans.manage', 'coupons.manage', 'payments.view', 'payments.reconcile', 'subscriptions.adjust', 'refunds.record',
            'reports.view', 'reports.export',
        ],
        'Moderator' => [
            'resources.moderate', 'resources.publish', 'questions.review', 'tutorials.moderate', 'reports.view',
        ],
        self::TEACHER => [],
        self::TEACHER_PENDING => [],
        self::STUDENT => [],
    ];

    /** Roles that belong to non-staff accounts and are never offered in the staff role picker. */
    public const ACCOUNT_ROLES = [self::TEACHER, self::TEACHER_PENDING, self::STUDENT];

    /** @return array<int, string> */
    public static function all(): array
    {
        return array_merge(...array_map('array_keys', array_values(self::GROUPS)));
    }

    /** @return array<int, string> permissions the role editor may assign to a non-Super-Admin role */
    public static function assignable(): array
    {
        return array_values(array_diff(self::all(), self::SUPER_ADMIN_ONLY));
    }

    public static function describe(string $permission): string
    {
        foreach (self::GROUPS as $permissions) {
            if (isset($permissions[$permission])) {
                return $permissions[$permission];
            }
        }

        return $permission;
    }
}
