<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Learn;
use App\Http\Controllers\Teach;
use Illuminate\Support\Facades\Route;

/*
| Public
*/
Route::view('/', 'public.home')->name('home');
Route::view('/terms', 'public.terms')->name('terms');
Route::view('/privacy', 'public.privacy')->name('privacy');

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
});
