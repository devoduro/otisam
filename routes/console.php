<?php

use App\Models\LoginEvent;
use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Schedule;

/*
| Driven by one cron entry (`* * * * * php artisan schedule:run`), because shared hosting cannot
| keep a queue worker running. See docs/DEPLOYMENT.md.
*/

// Drain the queue (notifications) every minute; never overlap with a run that is still going.
Schedule::command('queue:work --stop-when-empty --tries=4 --max-time=50')
    ->everyMinute()
    ->withoutOverlapping(5);

// Housekeeping: one-time codes and sign-in history are not kept longer than needed.
Schedule::call(function () {
    PhoneVerification::where('created_at', '<', now()->subDays(2))->delete();
    LoginEvent::where('created_at', '<', now()->subDays(180))->delete();
})->daily()->name('prune-security-records')->withoutOverlapping();

// Payments: re-verify anything a lost callback/webhook left unsettled.
Schedule::command('payments:reconcile')->everyTenMinutes()->withoutOverlapping();

// Subscription expiry reminders (7 days, 1 day, on the day), once each.
Schedule::command('subscriptions:remind')->dailyAt('08:00')->withoutOverlapping();

// New uploads from the maths YouTube channel(s) become published video lessons.
Schedule::command('videos:import-youtube')->dailyAt('05:30')->withoutOverlapping();
