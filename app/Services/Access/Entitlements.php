<?php

namespace App\Services\Access;

use App\Models\Commerce\Subscription;
use App\Models\User;
use App\Services\Commerce\SubscriptionService;

/**
 * Answers "does this user currently hold premium access?" (BLUEPRINT §7.2): an active
 * subscription whose period (plus any grace days) covers this moment.
 */
class Entitlements
{
    public function hasPremium(?User $user, string $key = SubscriptionService::ENTITLEMENT): bool
    {
        if (! $user || ! $user->isActive()) {
            return false;
        }

        return Subscription::where('user_id', $user->id)->where('entitlement_key', $key)->current()->exists();
    }

    public function currentSubscription(?User $user, string $key = SubscriptionService::ENTITLEMENT): ?Subscription
    {
        if (! $user) {
            return null;
        }

        return Subscription::where('user_id', $user->id)->where('entitlement_key', $key)->current()
            ->with('plan')->orderByDesc('ends_at')->first();
    }
}
