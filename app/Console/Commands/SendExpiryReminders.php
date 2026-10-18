<?php

namespace App\Console\Commands;

use App\Models\Commerce\Subscription;
use App\Services\Notifications\Notifier;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Reminds subscribers 7 days and 1 day before their access ends, and on the day it ends
 * (BLUEPRINT §7.1). Each reminder is sent once (idempotency key per subscription and stage), and
 * only for a user's latest subscription, so someone who already renewed is not nagged.
 */
class SendExpiryReminders extends Command
{
    protected $signature = 'subscriptions:remind';

    protected $description = 'Send subscription expiry reminders (7 days, 1 day, expired)';

    private const STAGES = ['7d' => 7, '1d' => 1, '0d' => 0];

    public function handle(Notifier $notifier): int
    {
        $sent = 0;
        $app = config('app.name');

        foreach (self::STAGES as $stage => $days) {
            $day = now()->startOfDay()->addDays($days);

            Subscription::where('status', 'active')
                ->whereBetween('ends_at', $stage === '0d' ? [now()->subDay(), now()] : [$day, $day->copy()->endOfDay()])
                ->with('user')
                ->each(function (Subscription $subscription) use ($notifier, $stage, $app, &$sent) {
                    $user = $subscription->user;
                    if (! $user || ! $user->isActive()) {
                        return;
                    }

                    // Skip if a later subscription (a renewal) carries the user beyond this one.
                    $latestEnd = Subscription::where('user_id', $user->id)->where('entitlement_key', $subscription->entitlement_key)
                        ->where('status', 'active')->max('ends_at');
                    if ($latestEnd && Carbon::parse($latestEnd)->gt($subscription->ends_at)) {
                        return;
                    }

                    $date = $subscription->ends_at->format('j M Y');
                    $link = route('plans.index');
                    $body = match ($stage) {
                        '7d' => "Your {$app} premium access ends on {$date}. Renew any time at {$link} to keep your solutions and notes.",
                        '1d' => "Reminder: your {$app} premium access ends tomorrow ({$date}). Renew at {$link}",
                        default => "Your {$app} premium access has ended. Renew at {$link} to unlock premium material again.",
                    };

                    $notifier->toUser($user, "subscription.expiry.{$stage}", 'transactional', "{$app} premium access", $body,
                        "subscription.expiry.{$stage}:{$subscription->id}");
                    $sent++;
                });
        }

        $this->info("Queued {$sent} reminder(s).");

        return self::SUCCESS;
    }
}
