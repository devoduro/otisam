# Next Steps

## Where things stand (18 Oct 2026)
- P0 (foundations) and P1 (identity & RBAC) are built and tested: 39 tests pass. See `docs/CHECKLIST.md`.
- Local app: http://localhost/otisam/public. The local Super Admin is `owner@otisam.test`; its password is `SEED_SUPERADMIN_PASSWORD` in `.env`.
- Local `.env` uses `SMS_PROVIDER=speso` with the jhs_portal key, so registering locally **sends real SMS**. Switch to `SMS_PROVIDER=log` to read codes from `storage/logs/laravel.log` instead.
- Tests always use the log gateway and the `otisam_test` database.

## Code map
- Permissions and roles: `app/Support/Permissions.php`, `database/seeders/RolesAndPermissionsSeeder.php`
- Auth: `app/Http/Controllers/Auth/*`, `app/Services/Auth/*`
- Admin: `app/Http/Controllers/Admin/*`, `resources/views/admin/*`, shell assets in `public/assets/admin/*` (from quizapp)
- SMS/notifications: `app/Services/Sms/*`, `app/Services/Notifications/Notifier.php`, `app/Jobs/SendNotificationMessage.php`
- Audit: `app/Services/Audit.php`, `app/Http/Middleware/RecordActivity.php`

## Waiting on the owner
1. A test phone number to confirm a live Speso send from this app.
2. Decisions D1, D5–D9 (BLUEPRINT §12).
3. Official GES/NaCCA SHS curriculum documents for the launch subjects (needed for P2 content, not for the editor itself).

## Next build: P2 Curriculum
1. Migrations: `education_levels`, `class_levels`, `programmes`, `subjects`, `programme_subject`, `curriculum_versions`, `curriculum_nodes` (tree with `path`), `academic_years`, `terms`.
2. Admin CRUD + tree editor (permission `curriculum.manage`), CSV import of topics with dry-run validation.
3. Migrate `student_profiles.class_level/programme` strings to foreign keys; feed the registration selects from the tables instead of `config/platform.php`.
4. Public subject → topic browsing pages.
5. Tests: curriculum editable without code changes (acceptance criterion 17), permission checks.

After P2: P3 resource library, then P4 Paystack/subscriptions (BLUEPRINT §11).
