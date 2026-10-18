<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Sms\LogSmsGateway;
use App\Services\Sms\SmsGateway;
use App\Services\Sms\SpesoSmsGateway;
use App\Support\Permissions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Everything that sends SMS asks for SmsGateway and gets whichever provider
        // SMS_PROVIDER names (the jhs_portal pattern).
        $this->app->singleton(SmsGateway::class, fn () => match (config('services.sms_provider')) {
            'speso' => SpesoSmsGateway::fromConfig(),
            default => new LogSmsGateway(),
        });
    }

    public function boot(): void
    {
        // Super Admin passes every permission check. It is tied to the role on a staff account,
        // never to a user id (quizapp's `id === 1` bypass is deliberately not carried over).
        Gate::before(fn (User $user) => $user->isSuperAdmin() && $user->isActive() ? true : null);

        // Catch N+1 queries during development and tests.
        Model::preventLazyLoading(!$this->app->isProduction());

        Paginator::useBootstrapFive();
    }
}
