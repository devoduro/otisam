<?php

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
    \App\Models\PhoneVerification::where('created_at', '<', now()->subDays(2))->delete();
    \App\Models\LoginEvent::where('created_at', '<', now()->subDays(180))->delete();
})->daily()->name('prune-security-records')->withoutOverlapping();
