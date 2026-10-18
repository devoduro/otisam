# Next Steps

## Where things stand (18 Oct 2026)
- P0–P4 are built and tested (identity, curriculum, library, subscriptions/Paystack): 83 tests pass. See `docs/CHECKLIST.md`.
- Local app: http://localhost/otisam/public. The local Super Admin is `owner@otisam.test`; its password is `SEED_SUPERADMIN_PASSWORD` in `.env`.
- Local `.env` uses `SMS_PROVIDER=speso` with the jhs_portal key, so registering locally **sends real SMS**. Switch to `SMS_PROVIDER=log` to read codes from `storage/logs/laravel.log` instead.
- Tests always use the log gateway and the `otisam_test` database.
- UI follows jhs_portal (see CHECKLIST "UI restyle"). Public pages use `layouts.app`, sign-in pages `layouts.auth`, students/teachers `layouts.portal`, staff `layouts.admin`. Reuse `x-dashboard-hero`, `x-stat-tile` and `.panel`-style cards (`rounded-md bg-white shadow-sm ring-1 ring-gray-100`).
- A local demo student for UI checks is in `.env` (`DEV_DEMO_STUDENT_*`).
- After changing Blade views that use new Tailwind classes, run `npm run build`. `public/build` is committed for cPanel.

## Code map
- Permissions and roles: `app/Support/Permissions.php`, `database/seeders/RolesAndPermissionsSeeder.php`
- Auth: `app/Http/Controllers/Auth/*`, `app/Services/Auth/*`
- Admin: `app/Http/Controllers/Admin/*`, `resources/views/admin/*`, shell assets in `public/assets/admin/*` (from quizapp)
- Commerce: `app/Services/Commerce/*` (Paystack client, Checkout, **PaymentFulfilment = the only path from payment to subscription**, SubscriptionService), `app/Services/Access/Entitlements.php`, `app/Http/Controllers/Account/*`, `Admin/Commerce/*`, webhook `Webhooks/PaystackWebhookController.php`, commands `payments:reconcile`, `subscriptions:remind`
- Library: `app/Models/Library/*`, `app/Services/Library/*` (UploadInspector, ResourceStorage, PdfPreviewGenerator, ResourceAccess, ResourceService), `app/Http/Controllers/Admin/Library/*`, public `LibraryController` + `ResourceFileController` (the only file-serving path), PDF.js reader `resources/js/pdf-viewer.js`
- Curriculum: `app/Models/Curriculum/*`, `app/Services/Curriculum/CurriculumTree.php` (all tree structure changes), `CurriculumImporter.php` (CSV), `app/Http/Controllers/Admin/Curriculum/*`, public pages in `app/Http/Controllers/Public/SubjectController.php`
- SMS/notifications: `app/Services/Sms/*`, `app/Services/Notifications/Notifier.php`, `app/Jobs/SendNotificationMessage.php`
- Audit: `app/Services/Audit.php`, `app/Http/Middleware/RecordActivity.php`

## Waiting on the owner
1. Official GES/NaCCA SHS curriculum documents for the launch subjects, so versions and topic trees can be entered (by the admin UI or CSV import).
2. A test phone number to confirm a live Speso send from this app.
3. Decisions D1, D5–D9 (BLUEPRINT §12).

## To check P4 against Paystack test mode (owner)
1. Put `PAYSTACK_PUBLIC_KEY=pk_test_...` and `PAYSTACK_SECRET_KEY=sk_test_...` in `.env`, then run `php artisan config:clear`.
2. In Admin → Subscriptions → Plans, set a price on one plan and tick "On sale".
3. Sign in as a student, open /plans, buy with a Paystack test card or test mobile money number.
4. Webhooks cannot reach `localhost`; locally the callback and `php artisan payments:reconcile` settle payments. Test webhooks on a public staging URL.

## Next build: P5 Question bank (BLUEPRINT §8.1)
1. Migrations: `questions`, `question_revisions` (immutable content), `question_keys` (answer keys kept apart), `question_curriculum_node`, `question_imports`.
2. One strategy class per type (MCQ, multi-select, true/false, fill-blank, numerical first) with validate / public payload / mark.
3. Admin editor with preview, revisions, review workflow (draft → in review → published), KaTeX maths, image upload via the library's inspector.
4. Excel/CSV import with dry-run validation (missing keys, duplicates, unknown topics).
5. Tests: no answer key in any public payload, marking per type, import validation.

## P5 progress (stopped mid-way, 18 Oct 2026)
Done:
- Migration `2026_10_18_400001_create_question_bank_tables` (questions, immutable question_revisions, separate question_keys, question_media, curriculum tags, question_imports), migrated locally.
- Models in `app/Models/QuestionBank/` (Question, QuestionRevision, QuestionKey).

Still to do:
1. Type strategies in `app/Services/QuestionBank/Types` (MCQ, multi-select, true/false, fill-blank `[[1]]`, numeric with tolerance): validate / publicPayload (no keys) / mark.
2. Bank service: create/edit (a content change creates a new revision), workflow draft → in_review → published / changes_requested, access rules (keys visible to view-keys, reviewers and the author; promotion questions only with promotion-exams.manage), safe text rendering + KaTeX (npm `katex`, pinned), images via UploadInspector.
3. Admin editor (jhs_portal styling), preview with "try it" marking, review queue, sidebar entry.
4. CSV import with dry run (template, duplicate detection by stem_hash, unknown subject/topic codes).
5. Tests: marking per type, no key in any public payload, workflow permissions, revisions immutable, import validation.
