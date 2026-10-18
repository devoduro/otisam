<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Learn;
use App\Http\Controllers\Public\LibraryController;
use App\Http\Controllers\Public\PlanController;
use App\Http\Controllers\Public\ResourceFileController;
use App\Http\Controllers\Public\SubjectController;
use App\Http\Controllers\Teach;
use App\Http\Controllers\Webhooks;
use Illuminate\Support\Facades\Route;

/*
| Public
*/
Route::view('/', 'public.home')->name('home');
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

Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');

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
});

/*
| Teacher area. Pending teachers can reach their dashboard and profile; everything else that
| needs approval checks permissions as it is built.
*/
Route::middleware(['auth', 'account:teacher', 'phone.verified', 'password.fresh'])->prefix('teach')->name('teach.')->group(function () {
    Route::get('/', Teach\DashboardController::class)->name('dashboard');
    Route::get('/profile', [Teach\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [Teach\ProfileController::class, 'update'])->name('profile.update');
});

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

    /*
    | Commerce (BLUEPRINT §7).
    */
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
