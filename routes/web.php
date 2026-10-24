<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Learn;
use App\Http\Controllers\Public\DownloadController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LeaderboardController;
use App\Http\Controllers\Public\LessonPlanController;
use App\Http\Controllers\Public\LibraryController;
use App\Http\Controllers\Public\PaperController;
use App\Http\Controllers\Public\PlanController;
use App\Http\Controllers\Public\PracticeController;
use App\Http\Controllers\Public\PromotionController;
use App\Http\Controllers\Public\QaAnswerController;
use App\Http\Controllers\Public\QaController;
use App\Http\Controllers\Public\ReferralController;
use App\Http\Controllers\Public\ResourceFileController;
use App\Http\Controllers\Public\StudyController;
use App\Http\Controllers\Public\SubjectAppController;
use App\Http\Controllers\Public\SubjectController;
use App\Http\Controllers\Public\TeacherDirectoryController;
use App\Http\Controllers\Public\VideoController;
use App\Http\Controllers\Teach;
use App\Http\Controllers\Webhooks;
use Illuminate\Support\Facades\Route;

/*
| Public
*/
Route::get('/', HomeController::class)->name('home');
Route::view('/terms', 'public.terms')->name('terms');
Route::view('/privacy', 'public.privacy')->name('privacy');

Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');
Route::get('/subjects/{subject}/topics/{node}', [SubjectController::class, 'topic'])->whereNumber('node')->name('subjects.topic');

Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
Route::get('/library/{resource}', [LibraryController::class, 'show'])->name('library.show');
// Files are served only through ResourceFileController, which checks access on every request.
Route::get('/library/{resource}/preview', [ResourceFileController::class, 'preview'])->middleware('throttle:120,1')->name('library.preview');
Route::get('/library/{resource}/view', [ResourceFileController::class, 'view'])->middleware('throttle:120,1')->name('library.view');
Route::get('/library/{resource}/download', [ResourceFileController::class, 'download'])->middleware('throttle:30,60')->name('library.download');

Route::get('/teachers', [TeacherDirectoryController::class, 'index'])->name('teachers.index');
// Profile slugs never clash with /teachers/register (registration routes are matched by the guest group below).
Route::get('/teachers/{slug}', [TeacherDirectoryController::class, 'show'])->where('slug', '(?!register$)[a-z0-9-]+')->name('teachers.show');
Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');

Route::get('/practice', [PracticeController::class, 'index'])->name('practice.index');
Route::get('/practice/start', [PracticeController::class, 'start'])->name('practice.start');
Route::post('/practice/mark', [PracticeController::class, 'mark'])->middleware('throttle:30,1')->name('practice.mark');
Route::get('/practice/{subject}', [PracticeController::class, 'show'])->name('practice.show');
// Study by class: level → class → subject hub.
Route::get('/study', [StudyController::class, 'index'])->name('study.index');
Route::get('/study/{level}', [StudyController::class, 'level'])->whereIn('level', ['jhs', 'shs'])->name('study.level');
Route::get('/study/{level}/{subject}', [StudyController::class, 'subject'])->whereIn('level', ['jhs', 'shs'])->name('study.subject');
Route::get('/study/{level}/{subject}/quick', [StudyController::class, 'quick'])->whereIn('level', ['jhs', 'shs'])->name('study.quick');
Route::post('/study/{level}/{subject}/check', [StudyController::class, 'check'])->whereIn('level', ['jhs', 'shs'])->middleware('throttle:120,1')->name('study.check');
Route::get('/leaderboard', LeaderboardController::class)->name('leaderboard');
// WASSCE/BECE-style objective papers built from Otisam's own questions.
Route::prefix('/papers')->name('papers.')->group(function () {
    Route::get('/', [PaperController::class, 'index'])->name('index');
    Route::get('/{subject}', [PaperController::class, 'subject'])->name('subject');
    Route::get('/{subject}/{number}', [PaperController::class, 'show'])->whereNumber('number')->name('show');
    Route::get('/{subject}/{number}/take', [PaperController::class, 'take'])->whereNumber('number')->name('take');
    Route::post('/{subject}/{number}/check', [PaperController::class, 'check'])->whereNumber('number')->middleware('throttle:120,1')->name('check');
    Route::post('/{subject}/{number}/mark', [PaperController::class, 'mark'])->whereNumber('number')->middleware('throttle:30,1')->name('mark');
    Route::get('/{subject}/{number}/solutions', [PaperController::class, 'solutions'])->whereNumber('number')->middleware('auth')->name('solutions');
});
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show');
Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
Route::get('/lesson-plans', [LessonPlanController::class, 'index'])->name('lesson-plans.index');
Route::get('/subject-apps', [SubjectAppController::class, 'index'])->name('subject-apps.index');
Route::get('/lesson-plans/{plan}', [LessonPlanController::class, 'show'])->name('lesson-plans.show');
Route::get('/lesson-plans/{plan}/print', [LessonPlanController::class, 'print'])->name('lesson-plans.print');
Route::get('/lesson-plans/{plan}/download', [LessonPlanController::class, 'download'])->middleware('throttle:30,1')->name('lesson-plans.download');
Route::get('/downloads/teachers/{material}', [DownloadController::class, 'material'])->name('downloads.material');
Route::get('/promotion', PromotionController::class)->name('promotion');
Route::get('/promotion/paper/{paper}', [PromotionController::class, 'paper'])->name('promotion.paper');

/*
| Community Q&A ("Q&A" in menus; not the question bank). Anyone reads; students and teachers post.
*/
Route::get('/questions', [QaController::class, 'index'])->name('qa.index');
Route::get('/questions/{question}', [QaController::class, 'show'])->whereNumber('question')->name('qa.show');
Route::middleware(['auth', 'account:student,teacher', 'phone.verified', 'password.fresh'])->name('qa.')->group(function () {
    Route::get('/questions/ask', [QaController::class, 'create'])->name('create');
    Route::post('/questions', [QaController::class, 'store'])->middleware('throttle:5,10')->name('store');
    Route::get('/questions/{question}/edit', [QaController::class, 'edit'])->name('edit');
    Route::put('/questions/{question}', [QaController::class, 'update'])->middleware('throttle:20,10')->name('update');
    Route::delete('/questions/{question}', [QaController::class, 'destroy'])->name('destroy');
    Route::post('/questions/{question}/answers', [QaAnswerController::class, 'store'])->middleware('throttle:10,10')->name('answers.store');
    Route::get('/answers/{answer}/edit', [QaAnswerController::class, 'edit'])->name('answers.edit');
    Route::put('/answers/{answer}', [QaAnswerController::class, 'update'])->middleware('throttle:20,10')->name('answers.update');
    Route::delete('/answers/{answer}', [QaAnswerController::class, 'destroy'])->name('answers.destroy');
});
// Accepting (asker or moderator) and reporting (anyone signed in) are checked in the controller.
Route::middleware(['auth', 'password.fresh'])->name('qa.')->group(function () {
    Route::post('/questions/{question}/accept/{answer}', [QaController::class, 'accept'])->middleware('throttle:30,1')->name('accept');
    Route::post('/questions/{question}/report', [QaController::class, 'reportQuestion'])->middleware('throttle:20,10')->name('report');
    Route::post('/answers/{answer}/report', [QaController::class, 'reportAnswer'])->middleware('throttle:20,10')->name('answers.report');
});

// Paystack sends the browser back here after checkout. The reference is re-verified server-side.
Route::get('/payments/paystack/callback', [Account\CheckoutController::class, 'callback'])->middleware('throttle:30,1')->name('payments.callback');
// Signed server-to-server notifications from Paystack (CSRF-exempt in bootstrap/app.php).
Route::post('/webhooks/paystack', Webhooks\PaystackWebhookController::class)->middleware('throttle:300,1')->name('webhooks.paystack');

Route::get('/copyright', [LibraryController::class, 'copyright'])->name('copyright');
Route::post('/copyright', [LibraryController::class, 'takedown'])->middleware('throttle:5,60')->name('copyright.takedown');

/*
| Authentication
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [Auth\LoginController::class, 'show'])->name('login');
    Route::post('/login', [Auth\LoginController::class, 'login'])->middleware('throttle:20,1')->name('login.attempt');

    Route::get('/register', [Auth\StudentRegisterController::class, 'show'])->name('register');
    Route::post('/register', [Auth\StudentRegisterController::class, 'store'])->middleware('throttle:10,1')->name('register.store');

    Route::get('/teachers/register', [Auth\TeacherRegisterController::class, 'show'])->name('teachers.register');
    Route::post('/teachers/register', [Auth\TeacherRegisterController::class, 'store'])->middleware('throttle:10,1')->name('teachers.register.store');

    Route::get('/forgot-password', [Auth\PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [Auth\PasswordResetController::class, 'send'])->middleware('throttle:10,1')->name('password.send');
    Route::get('/reset-password', [Auth\PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [Auth\PasswordResetController::class, 'update'])->middleware('throttle:10,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [Auth\LoginController::class, 'logout'])->name('logout');

    Route::get('/verify-phone', [Auth\PhoneVerificationController::class, 'show'])->name('verify.phone');
    Route::post('/verify-phone', [Auth\PhoneVerificationController::class, 'verify'])->middleware('throttle:10,1')->name('verify.phone.check');
    Route::post('/verify-phone/resend', [Auth\PhoneVerificationController::class, 'resend'])->middleware('throttle:5,1')->name('verify.phone.resend');

    Route::get('/account/password', [Auth\PasswordChangeController::class, 'edit'])->name('password.change');
    Route::put('/account/password', [Auth\PasswordChangeController::class, 'update'])->name('password.change.update');
});

/*
| Buying and managing premium access (students and teachers).
*/
Route::middleware(['auth', 'account:student,teacher', 'phone.verified', 'password.fresh'])->prefix('account')->name('account.')->group(function () {
    Route::get('/subscription', [Account\SubscriptionController::class, 'index'])->name('subscription');
    Route::get('/checkout/{plan}', [Account\CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout/{plan}', [Account\CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
    Route::get('/payments/{payment}', [Account\CheckoutController::class, 'payment'])->name('payments.show');
    Route::post('/payments/{payment}/check', [Account\CheckoutController::class, 'check'])->middleware('throttle:12,1')->name('payments.check');
    Route::get('/payments/{payment}/receipt', [Account\SubscriptionController::class, 'receipt'])->name('payments.receipt');
});

/*
| Student area
*/
Route::middleware(['auth', 'account:student', 'phone.verified', 'password.fresh'])->prefix('learn')->name('learn.')->group(function () {
    Route::get('/', Learn\DashboardController::class)->name('dashboard');
    Route::get('/progress', Learn\ProgressController::class)->name('progress');

    // Classes, assignments and quizzes from teachers
    Route::get('/classes', [Learn\ClassController::class, 'index'])->name('classes.index');
    Route::post('/classes/join', [Learn\ClassController::class, 'join'])->middleware('throttle:10,1')->name('classes.join');
    Route::get('/classes/{classroom}', [Learn\ClassController::class, 'show'])->name('classes.show');
    Route::delete('/classes/{classroom}', [Learn\ClassController::class, 'leave'])->name('classes.leave');
    Route::get('/work/{assignment}', [Learn\AssignmentController::class, 'show'])->name('assignments.show');
    Route::get('/work/{assignment}/quiz', [Learn\AssignmentController::class, 'take'])->name('assignments.take');
    Route::post('/work/{assignment}/quiz', [Learn\AssignmentController::class, 'submitQuiz'])->middleware('throttle:20,1')->name('assignments.submit-quiz');
    Route::post('/work/{assignment}/quiz/left', [Learn\AssignmentController::class, 'left'])->middleware('throttle:60,1')->name('assignments.left');
    Route::post('/work/{assignment}/hand-in', [Learn\AssignmentController::class, 'submitWork'])->middleware('throttle:20,1')->name('assignments.submit-work');
    Route::get('/work/{assignment}/handout', [Learn\AssignmentController::class, 'handout'])->name('assignments.handout');
    Route::get('/work/{assignment}/my-file', [Learn\AssignmentController::class, 'myFile'])->name('assignments.my-file');
});

/*
| Teacher area. Pending teachers can reach their dashboard and profile; everything else that
| needs approval checks permissions as it is built.
*/
Route::middleware(['auth', 'account:teacher', 'phone.verified', 'password.fresh'])->prefix('teach')->name('teach.')->group(function () {
    Route::get('/', Teach\DashboardController::class)->name('dashboard');
    Route::get('/profile', [Teach\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [Teach\ProfileController::class, 'update'])->name('profile.update');

    // Classes, assignments, quizzes and shared materials: approved teachers only.
    Route::middleware('teacher.approved')->group(function () {
        Route::resource('classes', Teach\ClassroomController::class)->parameters(['classes' => 'classroom'])->except('destroy');
        Route::post('/classes/{classroom}/archive', [Teach\ClassroomController::class, 'archive'])->name('classes.archive');
        Route::post('/classes/{classroom}/code', [Teach\ClassroomController::class, 'newCode'])->name('classes.code');
        Route::delete('/classes/{classroom}/students/{student}', [Teach\ClassroomController::class, 'removeStudent'])->name('classes.students.remove');

        Route::get('/classes/{classroom}/work/create', [Teach\AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/classes/{classroom}/work', [Teach\AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/work/{assignment}', [Teach\AssignmentController::class, 'show'])->name('assignments.show');
        Route::get('/work/{assignment}/edit', [Teach\AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/work/{assignment}', [Teach\AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/work/{assignment}', [Teach\AssignmentController::class, 'destroy'])->name('assignments.destroy');
        Route::post('/work/{assignment}/publish', [Teach\AssignmentController::class, 'publish'])->name('assignments.publish');
        Route::post('/work/{assignment}/close', [Teach\AssignmentController::class, 'close'])->name('assignments.close');
        Route::post('/work/{assignment}/questions/bank', [Teach\AssignmentController::class, 'addFromBank'])->name('assignments.questions.bank');
        Route::post('/work/{assignment}/questions', [Teach\AssignmentController::class, 'addOwnQuestion'])->name('assignments.questions.store');
        Route::delete('/work/{assignment}/questions/{question}', [Teach\AssignmentController::class, 'removeQuestion'])->name('assignments.questions.destroy');
        Route::put('/work/{assignment}/submissions/{submission}', [Teach\AssignmentController::class, 'grade'])->name('assignments.grade');
        Route::get('/work/{assignment}/handout', [Teach\AssignmentController::class, 'handout'])->name('assignments.handout');
        Route::get('/work/{assignment}/submissions/{submission}/file', [Teach\AssignmentController::class, 'submissionFile'])->name('assignments.submission-file');

        Route::resource('videos', Teach\VideoController::class)->except('show');
        Route::resource('lesson-plans', Teach\LessonPlanController::class)->except('show');

        Route::get('/materials/{material}/download', [Teach\MaterialController::class, 'download'])->name('materials.download');
        Route::resource('materials', Teach\MaterialController::class)->except('show');
    });
});

// Invite links
Route::get('/r/{code}', ReferralController::class)->where('code', '[A-Za-z0-9]{6,12}')->name('referral');

/*
| Admin console. Every group checks a permission on the server; the sidebar @can checks only
| decide what is shown.
*/
Route::middleware(['auth', 'account:staff', 'password.fresh'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::middleware('permission:users.view')->group(function () {
        Route::get('/students', [Admin\StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [Admin\StudentController::class, 'show'])->name('students.show');
        Route::get('/teachers', [Admin\TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/{teacher}', [Admin\TeacherController::class, 'show'])->name('teachers.show');
    });

    Route::post('/teachers/{teacher}/decision', [Admin\TeacherController::class, 'decide'])->middleware('permission:teachers.verify')->name('teachers.decide');
    Route::put('/teachers/{teacher}/capabilities', [Admin\TeacherController::class, 'capabilities'])->middleware('permission:teachers.grant-capabilities')->name('teachers.capabilities.update');

    // Who may suspend whom (users.suspend vs staff.manage) is checked inside the controller.
    Route::post('/accounts/{user}/suspend', [Admin\AccountStatusController::class, 'suspend'])->name('accounts.suspend');
    Route::post('/accounts/{user}/activate', [Admin\AccountStatusController::class, 'activate'])->name('accounts.activate');

    Route::middleware('permission:staff.manage')->group(function () {
        Route::get('/staff', [Admin\StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [Admin\StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [Admin\StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{member}/edit', [Admin\StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{member}', [Admin\StaffController::class, 'update'])->name('staff.update');
        Route::post('/staff/{member}/reset-password', [Admin\StaffController::class, 'resetPassword'])->name('staff.reset-password');
    });

    Route::middleware('permission:roles.manage')->group(function () {
        Route::resource('roles', Admin\RoleController::class)->except('show');
    });

    Route::get('/audit-log', [Admin\AuditLogController::class, 'index'])->middleware('permission:audit.view')->name('audit.index');

    // Summary pages behind the dashboard cards: open to all staff, with names and numbers shortened.
    Route::prefix('overview')->name('overview.')->group(function () {
        Route::get('/students', [Admin\OverviewController::class, 'students'])->name('students');
        Route::get('/teachers', [Admin\OverviewController::class, 'teachers'])->name('teachers');
        Route::get('/staff', [Admin\OverviewController::class, 'staff'])->name('staff');
        Route::get('/sms', [Admin\OverviewController::class, 'sms'])->name('sms');
    });

    Route::middleware('permission:questions.manage|questions.review|questions.view-keys')->group(function () {
        Route::get('/questions', [Admin\QuestionBankController::class, 'index'])->name('questions.index');
        Route::get('/questions/{question}', [Admin\QuestionBankController::class, 'show'])->name('questions.show');
    });
    Route::middleware('permission:notifications.view-logs')->group(function () {
        Route::get('/messages', [Admin\MessageLogController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [Admin\MessageLogController::class, 'show'])->name('messages.show');
    });
    Route::middleware('permission:reports.view|results.view-all')->group(function () {
        Route::get('/practice-attempts', [Admin\PracticeAttemptController::class, 'index'])->name('attempts.index');
        Route::get('/practice-attempts/{attempt}', [Admin\PracticeAttemptController::class, 'show'])->name('attempts.show');
    });

    /*
    | Commerce (BLUEPRINT §7).
    */
    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('/settings/homepage', [Admin\HomepageSettingsController::class, 'edit'])->name('settings.homepage');
        Route::put('/settings/homepage', [Admin\HomepageSettingsController::class, 'update'])->name('settings.homepage.update');
        Route::delete('/settings/homepage', [Admin\HomepageSettingsController::class, 'destroy'])->name('settings.homepage.destroy');
    });

    Route::middleware('permission:tutorials.manage')->group(function () {
        Route::resource('videos', Admin\VideoLessonController::class)->except('show');
        Route::post('/videos/{video}/status', [Admin\VideoLessonController::class, 'status'])->name('videos.status');
        Route::post('/videos-import/youtube', [Admin\VideoLessonController::class, 'importYoutube'])->name('videos.import-youtube');

        Route::get('/moderation', [Admin\ContentModerationController::class, 'index'])->name('moderation.index');
        Route::post('/moderation/videos/{video}/approve', [Admin\ContentModerationController::class, 'approveVideo'])->name('moderation.videos.approve');
        Route::post('/moderation/videos/{video}/reject', [Admin\ContentModerationController::class, 'rejectVideo'])->name('moderation.videos.reject');
        Route::get('/moderation/materials/{material}/download', [Admin\ContentModerationController::class, 'downloadMaterial'])->name('moderation.materials.download');
        Route::get('/lesson-plans', [Admin\LessonPlanController::class, 'index'])->name('lesson-plans.index');
        Route::post('/lesson-plans/{plan}/approve', [Admin\LessonPlanController::class, 'approve'])->name('lesson-plans.approve');
        Route::post('/lesson-plans/{plan}/reject', [Admin\LessonPlanController::class, 'reject'])->name('lesson-plans.reject');
        Route::post('/lesson-plans/{plan}/archive', [Admin\LessonPlanController::class, 'archive'])->name('lesson-plans.archive');
        Route::post('/moderation/materials/{material}/hide', [Admin\ContentModerationController::class, 'hideMaterial'])->name('moderation.materials.hide');
    });

    // Community Q&A reports.
    Route::middleware('permission:tutorials.manage')->prefix('qa')->name('qa.')->group(function () {
        Route::get('/', [Admin\QaModerationController::class, 'index'])->name('index');
        Route::post('/{type}/{id}/hide', [Admin\QaModerationController::class, 'hide'])->whereIn('type', ['question', 'answer'])->whereNumber('id')->name('hide');
        Route::post('/{type}/{id}/restore', [Admin\QaModerationController::class, 'restore'])->whereIn('type', ['question', 'answer'])->whereNumber('id')->name('restore');
        Route::delete('/{type}/{id}', [Admin\QaModerationController::class, 'destroy'])->whereIn('type', ['question', 'answer'])->whereNumber('id')->name('destroy');
    });

    Route::prefix('commerce')->name('commerce.')->group(function () {
        Route::middleware('permission:plans.manage')->group(function () {
            Route::resource('plans', Admin\Commerce\PlanController::class)->except('show');
            Route::put('/grace', [Admin\Commerce\PlanController::class, 'updateGrace'])->name('grace');
        });
        Route::middleware('permission:coupons.manage')->group(function () {
            Route::resource('coupons', Admin\Commerce\CouponController::class)->except(['show', 'destroy']);
        });
        Route::middleware('permission:payments.view')->group(function () {
            Route::get('/payments', [Admin\Commerce\PaymentController::class, 'index'])->name('payments.index');
            Route::get('/payments/{payment}', [Admin\Commerce\PaymentController::class, 'show'])->name('payments.show');
        });
        Route::post('/payments/{payment}/reverify', [Admin\Commerce\PaymentController::class, 'reverify'])->middleware(['permission:payments.reconcile', 'throttle:20,1'])->name('payments.reverify');
        Route::post('/payments/{payment}/refund', [Admin\Commerce\PaymentController::class, 'refund'])->middleware('permission:refunds.record')->name('payments.refund');

        Route::middleware('permission:subscriptions.adjust|payments.view')->group(function () {
            Route::get('/subscriptions', [Admin\Commerce\SubscriptionController::class, 'index'])->name('subscriptions.index');
        });
        Route::middleware('permission:subscriptions.adjust')->group(function () {
            Route::post('/subscriptions/grant', [Admin\Commerce\SubscriptionController::class, 'grant'])->name('subscriptions.grant');
            Route::post('/subscriptions/{subscription}/revoke', [Admin\Commerce\SubscriptionController::class, 'revoke'])->name('subscriptions.revoke');
        });
    });

    /*
    | Library. Editing needs resources.manage; publishing/archiving is checked again in the
    | controller (resources.publish).
    */
    Route::prefix('library')->name('library.')->group(function () {
        // Reviewers and publishers can open items; only editors can change them.
        Route::middleware('permission:resources.manage|resources.publish|resources.moderate')->group(function () {
            Route::get('/resources', [Admin\Library\ResourceController::class, 'index'])->name('resources.index');
            Route::get('/resources/{resource}/edit', [Admin\Library\ResourceController::class, 'edit'])->name('resources.edit');
            Route::post('/resources/{resource}/status', [Admin\Library\ResourceController::class, 'status'])->name('resources.status');
        });

        Route::middleware('permission:resources.manage')->group(function () {
            Route::get('/resources/bulk', [Admin\Library\ResourceController::class, 'bulkCreate'])->middleware('permission:resources.bulk-import')->name('resources.bulk');
            Route::post('/resources/bulk', [Admin\Library\ResourceController::class, 'bulkStore'])->middleware('permission:resources.bulk-import')->name('resources.bulk.store');
            Route::resource('resources', Admin\Library\ResourceController::class)->except(['index', 'show', 'edit']);
            Route::post('/resources/{resource}/links', [Admin\Library\ResourceController::class, 'link'])->name('resources.links.store');
            Route::delete('/resources/{resource}/links/{linked}/{relation}', [Admin\Library\ResourceController::class, 'unlink'])->name('resources.links.destroy');

            Route::get('/settings', [Admin\Library\LibrarySettingsController::class, 'index'])->name('settings');
            Route::put('/settings/preview', [Admin\Library\LibrarySettingsController::class, 'updatePreview'])->name('settings.preview');
            Route::post('/categories', [Admin\Library\LibrarySettingsController::class, 'storeCategory'])->name('categories.store');
            Route::put('/categories/{category}', [Admin\Library\LibrarySettingsController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{category}', [Admin\Library\LibrarySettingsController::class, 'destroyCategory'])->name('categories.destroy');
            Route::post('/exam-types', [Admin\Library\LibrarySettingsController::class, 'storeExamType'])->name('exam-types.store');
            Route::put('/exam-types/{examType}', [Admin\Library\LibrarySettingsController::class, 'updateExamType'])->name('exam-types.update');
        });

        Route::middleware('permission:takedowns.manage')->group(function () {
            Route::get('/takedowns', [Admin\Library\TakedownController::class, 'index'])->name('takedowns.index');
            Route::put('/takedowns/{takedown}', [Admin\Library\TakedownController::class, 'update'])->name('takedowns.update');
        });
    });

    /*
    | Curriculum: reading needs curriculum.view-admin, any change needs curriculum.manage.
    */
    Route::prefix('curriculum')->name('curriculum.')->group(function () {
        Route::middleware('permission:curriculum.view-admin|curriculum.manage')->group(function () {
            Route::get('/levels', [Admin\Curriculum\LevelController::class, 'index'])->name('levels.index');
            Route::get('/programmes', [Admin\Curriculum\ProgrammeController::class, 'index'])->name('programmes.index');
            Route::get('/subjects', [Admin\Curriculum\SubjectController::class, 'index'])->name('subjects.index');
            Route::get('/versions', [Admin\Curriculum\VersionController::class, 'index'])->name('versions.index');
            Route::get('/versions/{version}', [Admin\Curriculum\VersionController::class, 'show'])->whereNumber('version')->name('versions.show');
            Route::get('/versions/{version}/subjects/{subject}', [Admin\Curriculum\TreeController::class, 'show'])->whereNumber('version')->name('tree.show');
            Route::get('/versions/{version}/subjects/{subject}/export', [Admin\Curriculum\TreeController::class, 'export'])->name('tree.export');
            Route::get('/import-template', [Admin\Curriculum\TreeController::class, 'template'])->name('tree.template');
        });

        Route::middleware('permission:curriculum.manage')->group(function () {
            Route::post('/levels', [Admin\Curriculum\LevelController::class, 'store'])->name('levels.store');
            Route::put('/levels/{level}', [Admin\Curriculum\LevelController::class, 'update'])->name('levels.update');
            Route::post('/levels/{level}/classes', [Admin\Curriculum\LevelController::class, 'storeClass'])->name('classes.store');
            Route::put('/classes/{class}', [Admin\Curriculum\LevelController::class, 'updateClass'])->name('classes.update');
            Route::delete('/classes/{class}', [Admin\Curriculum\LevelController::class, 'destroyClass'])->name('classes.destroy');

            Route::resource('programmes', Admin\Curriculum\ProgrammeController::class)->except(['index', 'show']);
            Route::resource('subjects', Admin\Curriculum\SubjectController::class)->except(['index', 'show']);
            Route::resource('versions', Admin\Curriculum\VersionController::class)->except(['index', 'show']);

            Route::prefix('/versions/{version}/subjects/{subject}')->name('tree.')->group(function () {
                Route::post('/nodes', [Admin\Curriculum\TreeController::class, 'store'])->name('store');
                Route::get('/nodes/{node}/edit', [Admin\Curriculum\TreeController::class, 'edit'])->name('edit');
                Route::put('/nodes/{node}', [Admin\Curriculum\TreeController::class, 'update'])->name('update');
                Route::post('/nodes/{node}/shift', [Admin\Curriculum\TreeController::class, 'shift'])->name('shift');
                Route::delete('/nodes/{node}', [Admin\Curriculum\TreeController::class, 'destroy'])->name('destroy');
                Route::get('/import', [Admin\Curriculum\TreeController::class, 'importForm'])->name('import');
                Route::post('/import/preview', [Admin\Curriculum\TreeController::class, 'importPreview'])->name('import.preview');
                Route::post('/import/commit', [Admin\Curriculum\TreeController::class, 'importCommit'])->name('import.commit');
            });
        });
    });
});
