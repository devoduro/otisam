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

## P2–P11
Not started. See BLUEPRINT §11.

## Test report (18 Oct 2026)
`php artisan test` → **39 passed, 245 assertions**, against the MySQL test database `otisam_test`.

## Acceptance criteria (master prompt §19)
| # | Criterion | Phase | Status |
|---|---|---|---|
| 1 | Guest browses public resources and attempts an eligible public quiz | P3, P6 | [ ] |
| 2 | Student registers, verifies, signs in | P1 | [v] `StudentRegistrationTest` |
| 3 | Unapproved teacher cannot publish public tutorials | P1, P9 | [~] pending teachers hold no permissions; tutorials not built |
| 4 | Admin approves teacher and manages their permissions | P1 | [v] `TeacherApprovalTest` |
| 5 | Admin uploads a past-question paper and links its solution | P3 | [ ] |
| 6 | Premium preview only, without entitlement | P3, P4 | [ ] |
| 7 | Verified Paystack transaction activates the correct subscription | P4 | [ ] |
| 8 | Unverified/failed/forged payment does not activate | P4 | [ ] |
| 9 | Duplicate webhook does not duplicate payment/subscription | P4 | [ ] |
| 10 | Valid subscriber accesses premium resources | P4 | [ ] |
| 11 | Expired subscription cannot access protected content | P4 | [ ] |
| 12 | Students practise objective and supported theory questions | P6, P8 | [ ] |
| 13 | Timed exams save answers, enforce deadlines, record submissions | P6 | [ ] |
| 14 | Results follow configured marking rules | P6 | [ ] |
| 15 | Promotion exams restricted to eligible teachers | P10 | [ ] |
| 16 | Email/SMS generated and delivery status tracked | P7 | [~] logged with sent/failed status; Speso delivery reports pending |
| 17 | Curriculum managed without code changes | P2 | [ ] |
| 18 | No access to other users' private records/results/files | all | [~] identity area covered by `AccessControlTest` |
| 19 | Usable on mobile, tablet, desktop | all | [~] P1 pages checked at 375px and desktop |
| 20 | Backup, restore and deployment documented and tested | P11 | [~] deployment documented |
