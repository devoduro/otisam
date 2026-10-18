<?php

namespace App\Services\Library;

use App\Models\Library\Resource;
use App\Models\User;
use App\Services\Access\Entitlements;

/**
 * The single access policy for library items, applied on every page view, stream and download
 * (BLUEPRINT §7.2). The UI only hides what this already refuses.
 */
class ResourceAccess
{
    public function __construct(private readonly Entitlements $entitlements) {}

    /** Staff who manage or review content see everything, including drafts. */
    public function isEditor(?User $user): bool
    {
        return $user !== null && $user->isStaff() && $user->isActive()
            && $user->canAny(['resources.manage', 'resources.publish', 'resources.moderate']);
    }

    /** May the detail page (title, description, preview) be shown? */
    public function canSee(?User $user, Resource $resource): bool
    {
        return $this->isEditor($user) || $resource->isVisible();
    }

    /** May the full document be read? */
    public function canViewFull(?User $user, Resource $resource): bool
    {
        if ($this->isEditor($user)) {
            return true;
        }
        if (! $resource->isVisible()) {
            return false;
        }
        // Teacher-only material (lesson plans, schemes...) needs an approved teacher account.
        if ($resource->isForTeachersOnly() && ! $user?->isApprovedTeacher()) {
            return false;
        }
        if ($resource->isFree()) {
            return true;
        }

        return $user !== null && $user->isActive() && $this->entitlements->hasPremium($user);
    }

    public function canDownload(?User $user, Resource $resource): bool
    {
        if ($user === null && ! config('platform.library.guest_downloads')) {
            return false;
        }

        return $this->canViewFull($user, $resource);
    }

    /** Why the full document is unavailable, for the detail page. */
    public function lockReason(?User $user, Resource $resource): ?string
    {
        if ($this->canViewFull($user, $resource)) {
            return null;
        }
        if ($resource->isForTeachersOnly() && ! $user?->isApprovedTeacher()) {
            return 'teachers';
        }

        return $user ? 'premium' : 'premium_guest';
    }
}
