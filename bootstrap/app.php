<?php

use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureAccountType;
use App\Http\Middleware\EnsureApprovedTeacher;
use App\Http\Middleware\EnsurePhoneVerified;
use App\Http\Middleware\RecordActivity;
use App\Http\Middleware\RequirePasswordChange;
use App\Http\Middleware\StaffSessionTimeout;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'account' => EnsureAccountType::class,
            'phone.verified' => EnsurePhoneVerified::class,
            'password.fresh' => RequirePasswordChange::class,
            'teacher.approved' => EnsureApprovedTeacher::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        // Paystack cannot send a CSRF token; the webhook is authenticated by its HMAC signature instead.
        $middleware->validateCsrfTokens(except: ['webhooks/paystack']);

        $middleware->web(append: [
            EnsureAccountIsActive::class,
            StaffSessionTimeout::class,
            RecordActivity::class,
        ]);

        $middleware->redirectUsersTo(fn () => route(auth()->user()->homeRoute()));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
