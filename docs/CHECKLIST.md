# Development Checklist

Status key: `[ ]` pending · `[~]` in progress · `[x]` done (code written) · `[v]` verified (tests pass and checked in the app) · `[!]` blocked

Last updated: 18 Oct 2026

## Phase 0: Analysis & design
- [x] Inspect reference apps (`jhs_portal`, `quizapp`)
- [x] Blueprint (`docs/BLUEPRINT.md`)
- [v] Owner decisions D2, D3, D4, A1, R10 recorded (BLUEPRINT §12)
- [ ] Remaining decisions: D1 name/domain, D5 prices, D6 device limit, D7 teacher plan, D8 promotion-exam authority, D9 email provider

## P0: Foundations
- [v] Laravel 12.70 on PHP 8.2, MySQL/MariaDB, Spatie Permission 6
- [v] Admin console shell ported from quizapp (layout, sidebar, top bar, CSS, sidebar JS without jQuery/SweetAlert), rebranded navy/gold
- [v] Tailwind 4 public/student/teacher layouts with tokens; form components adapted from jhs_portal
- [v] Audit log (RecordActivity middleware from jhs_portal + explicit domain entries; insert-only)
- [v] Scheduler-driven queue for cPanel (`routes/console.php`), housekeeping prune
- [x] `docs/DEPLOYMENT.md` (cPanel). Not yet tried on the real host.

## P1: Identity & RBAC
- [v] Single `users` table with `account_type`; privileges only via roles/permissions; `Gate::before` for Super Admin by role (no user-id bypass)
- [v] Permission catalogue + default roles + Super-Admin-only permissions (`app/Support/Permissions.php`)
- [v] Student sign-up with phone + SMS code (hashed, 10-min expiry, 5 tries, rate limits), optional email, under-18 handling
- [v] Teacher registration → pending → approve / reject / more info → resubmit; SMS + email on decision
- [v] Per-teacher capabilities (only for approved teachers; removed on rejection)
- [v] Sign-in by phone or email, throttling, suspended-account lock-out, login history
- [v] Password reset by SMS code (no account enumeration); forced password change for admin-created accounts
- [v] Staff management (Super Admin), temporary passwords shown once, last-Super-Admin guard
- [v] Role editor (protected Super Admin, built-in roles not renamable, account roles hidden)
- [v] Suspend/reactivate with reason; staff 30-min idle timeout
- [v] Speso SMS gateway ported (same key/sender as jhs_portal) + log gateway for dev/tests; central `Notifier` with idempotency keys and delivery log
- [ ] **Speso live send not yet verified** from this app (needs a test number from the owner)
- [ ] Email password reset for staff without a phone (P1.1)
- [ ] Staff 2FA (Release 2)
- [ ] Teacher verification documents upload (needs private file storage from P3)

## P2: Curriculum
- [v] Tables: education levels, class levels, programmes, subjects (with slug, core/elective), programme–subject (core/elective per programme), curriculum versions (draft/active/retired, effective dates, official source), curriculum nodes (tree with materialised path)
- [v] Starter SHS structure seeder (levels, SHS 1–3, 7 programmes, 28 subjects). **No versions, strands or indicators are seeded**; those come from the official documents
- [v] Admin: levels & classes, programmes (with subject roles), subjects, versions (activating one needs effective date + source; status changes audited)
- [v] Topic-tree editor: add, edit, move (whole subtree, cycle-safe, max 6 levels), reorder, delete with children; official vs supplementary with source reference
- [v] CSV import with dry run (line-by-line errors, nothing written) then all-or-nothing commit; CSV export in the same format; template download
- [v] Student sign-up and profiles use class/programme IDs; inactive classes/programmes disappear from sign-up; existing profiles migrated by name
- [v] Public pages: /subjects, /subjects/{slug} (tree, version picker, drafts hidden), /subjects/{slug}/topics/{id} (breadcrumb, children, official badge)
- [v] Used records protected (subject/version with topics, class/programme with students)
- [ ] Copy a subject tree from one version into a new version (useful when NaCCA revises a curriculum)
- [ ] Academic years / terms tables: moved to P8 (teacher resources), where they are first used
- [ ] Official curriculum content entry: **blocked on the owner supplying GES/NaCCA documents**

## P3: Resource library
- [v] Tables: categories, exam types, resources (soft delete), versioned files on a private disk, links (solution / marking scheme / related), curriculum tags, access logs, takedown requests
- [v] Starter categories (12) and exam types (5): structure only, no documents
- [v] Upload checks: extension allowlist + real MIME match, size limit, PDF script/attachment scan, Office macro/ActiveX/OLE scan, image re-encoding, unreadable/quarantined file rejection
- [v] Automatic PDF preview (first N pages, never more than half, stamped PREVIEW) via queued job; manual preview upload when FPDI cannot parse a PDF
- [v] Access policy (`ResourceAccess`): editors see all; free = everyone; premium = entitled users only (P4 entitlement stub returns false); teacher-only = approved teachers; downloads need an account
- [v] Every file streamed by `ResourceFileController` with no-store, nosniff, sandbox CSP; access logged; download counter
- [v] Admin: resource list/filters, create/edit, replace file (history kept), workflow (draft → in review → published/scheduled → archived), links, curriculum tagging, bulk upload (20 files), categories/exam types/preview-length settings, takedown queue
- [v] Business rules: WAEC/BECE papers can only be free and must name the copyright holder; publishing needs a file and a copyright holder; publish/archive needs `resources.publish`
- [v] Public: /library (filters, search), /library/{slug} (metadata, PDF.js reader of full or preview, solutions, topics, related), topic pages list tagged items, /copyright takedown form
- [ ] Download watermarking with the user's name (Release 3)
- [ ] Ghostscript-based preview fallback where the host allows it
- [ ] Unified search across library and curriculum (P11)

## P4: Subscriptions and Paystack
- [v] Tables: plans, coupons, coupon redemptions, payments (pesewas, unique reference/transaction id), webhook events (unique dedupe key), subscriptions (unique payment_id), receipts, refunds
- [v] 3 starter plans (3/6/12 months) seeded **inactive with no price**: prices are owner decision D5
- [v] Checkout: server-side price and coupon, Paystack initialise, placeholder email for phone-only students, 100% coupon activates without Paystack
- [v] One idempotent `PaymentFulfilment::fulfil()` for callback, webhook, reconciliation and admin re-verify: Paystack verify API, reference/amount/currency match, row lock, receipt, SMS + email after commit
- [v] Webhook: HMAC-SHA512 signature on raw body, optional IP allowlist, stored once, processed by queued job; `charge.success` and `refund.processed` handled
- [v] Subscription stacking (renewal starts when current access ends), configurable grace days, `Entitlements` now uses subscriptions; library premium unlocks automatically
- [v] `payments:reconcile` every 10 min (pending → settled, >48 h → abandoned); `subscriptions:remind` daily (7 days, 1 day, expired; once each; skipped after renewal)
- [v] Student pages: /plans, checkout, payment status (auto-refresh while waiting for MoMo), my subscription, printable receipt
- [v] Admin: plans, coupons, payments (filters, totals, Paystack payload, re-verify, record refund), subscribers (grant/extend/revoke with reason), dashboard revenue/subscriber/pending tiles; all audited
- [ ] **Live check against Paystack test mode: blocked until the owner adds test keys to `.env`**
- [ ] Paystack recurring billing (not assumed; renewals are new purchases)
- [ ] Concurrent-session limit per subscriber (decision D6)

## UI restyle to match jhs_portal (18 Oct 2026)
- [v] Design tokens from jhs_portal: Poppins, sky-blue primary scale, slate-900 sidebar, two-tone stat-tile palette, purple sign-in panel (`resources/css/app.css`)
- [v] Components ported: `x-stat-tile`, `x-dashboard-hero` (copied), `x-auth-split` (jhs split-screen sign-in), jhs-style `x-flash`, `x-button`, `x-input` (password show/hide), `x-select`
- [v] Layouts: public (jhs header/footer + examsghana-style phone tab bar), auth (split screen), new student/teacher portal (jhs pupil layout: white sidebar, user menu)
- [v] Admin console: quizapp structure kept, restyled to jhs_portal (dark slate sidebar, sky active item, user block, jhs hero + two-tone tiles on the dashboard, page title in content area, dark mode removed)
- [v] Homepage redesigned with ideas from open-exam-prep.com (search-first hero, real-number strip, browse by subject with counts) and examsghana.com (big category tiles); only real counts are shown, unbuilt sections say "Coming soon"
- [v] Fixed: sign-up class/programme dropdowns submitted the name instead of the id (regression test added)
- [v] Alpine.js 3.14.1 bundled by Vite (no CDN)

## Menus: Video lessons, Downloads, Promotion (19 Oct 2026)
- [v] Video lessons: staff add YouTube lessons (any link form, embedded privacy-enhanced, never re-hosted), subject/class/topic, free or premium, draft/published/archived; public list with search and subject filter; watch page loads the player only on tap; premium locked without a subscription. Teacher submissions and moderation remain P9.
- [v] Downloads: all published documents with a file, each showing Download / Sign in / Premium; signed-in users see "My downloads" (from the access log). Files still go through the access-checked endpoint.
- [v] Promotion: information page for teacher promotion practice exams; states clearly they are not open yet and not official GES exams; shows a signed-in teacher's verification status. The exams themselves are P10.
- [v] Menus added to the public header (menu button below 1280px), phone tab bar, footer, student/teacher sidebar (Promotion for teachers only) and admin sidebar (Content → Video lessons)
- Tests: 90 passed, 662 assertions

## P5–P11
Not started. See BLUEPRINT §11.

## Test report (18 Oct 2026, after P4 + UI restyle)
`php artisan test` → **84 passed, 604 assertions**, against the MySQL test database `otisam_test`.

## Acceptance criteria (master prompt §19)
| # | Criterion | Phase | Status |
|---|---|---|---|
| 1 | Guest browses public resources and attempts an eligible public quiz | P3, P6 | [~] browsing verified (`ResourceLibraryTest`); quizzes in P6 |
| 2 | Student registers, verifies, signs in | P1 | [v] `StudentRegistrationTest` |
| 3 | Unapproved teacher cannot publish public tutorials | P1, P9 | [~] pending teachers hold no permissions; tutorials not built |
| 4 | Admin approves teacher and manages their permissions | P1 | [v] `TeacherApprovalTest` |
| 5 | Admin uploads a past-question paper and links its solution | P3 | [v] `ResourceLibraryTest::test_paper_and_linked_solution` |
| 6 | Premium preview only, without entitlement | P3, P4 | [v] guest and non-subscriber get preview only; subscriber gets full access |
| 7 | Verified Paystack transaction activates the correct subscription | P4 | [v] `PaymentsTest` (with Paystack faked over HTTP; live test-mode check pending keys) |
| 8 | Unverified/failed/forged payment does not activate | P4 | [v] failed, forged redirect, unknown reference, amount and currency mismatch |
| 9 | Duplicate webhook does not duplicate payment/subscription | P4 | [v] 3 identical webhooks + callback + reconcile → 1 event, 1 subscription; DB unique key enforced |
| 10 | Valid subscriber accesses premium resources | P4 | [v] premium document opens after verified payment |
| 11 | Expired subscription cannot access protected content | P4 | [v] access closes after the period (and after grace days) |
| 12 | Students practise objective and supported theory questions | P6, P8 | [ ] |
| 13 | Timed exams save answers, enforce deadlines, record submissions | P6 | [ ] |
| 14 | Results follow configured marking rules | P6 | [ ] |
| 15 | Promotion exams restricted to eligible teachers | P10 | [ ] |
| 16 | Email/SMS generated and delivery status tracked | P7 | [~] logged with sent/failed status; Speso delivery reports pending |
| 17 | Curriculum managed without code changes | P2 | [v] `CurriculumAdminTest` |
| 18 | No access to other users' private records/results/files | all | [~] identity area covered by `AccessControlTest` |
| 19 | Usable on mobile, tablet, desktop | all | [~] P1–P2 pages checked at 375px and desktop |
| 20 | Backup, restore and deployment documented and tested | P11 | [~] deployment documented |
