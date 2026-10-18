<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'account' => \App\Http\Middleware\EnsureAccountType::class,
            'phone.verified' => \App\Http\Middleware\EnsurePhoneVerified::class,
            'password.fresh' => \App\Http\Middleware\RequirePasswordChange::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\EnsureAccountIsActive::class,
            \App\Http\Middleware\StaffSessionTimeout::class,
            \App\Http\Middleware\RecordActivity::class,
        ]);

        $middleware->redirectUsersTo(fn () => route(auth()->user()->homeRoute()));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
