<?php

namespace App\Models\Commerce;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/** Created only by SubscriptionService. */
class Subscription extends Model
{
    public const SOURCES = ['payment' => 'Payment', 'admin_grant' => 'Granted by staff', 'coupon_free' => 'Free with coupon'];

    protected $guarded = ['*'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'grace_ends_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    /** Grants access at this moment (including any grace period). */
    public function scopeCurrent(Builder $query, ?\DateTimeInterface $at = null): Builder
    {
        $at ??= now();

        return $query->where('status', 'active')
            ->where('starts_at', '<=', $at)
            ->where(fn ($q) => $q->where('ends_at', '>', $at)->orWhere('grace_ends_at', '>', $at));
    }

    /** Effective end of access. */
    public function accessEndsAt(): Carbon
    {
        return $this->grace_ends_at && $this->grace_ends_at->gt($this->ends_at) ? $this->grace_ends_at : $this->ends_at;
    }

    public function stateLabel(): string
    {
        return match (true) {
            $this->status !== 'active' => ucfirst($this->status),
            $this->starts_at->isFuture() => 'Starts '.$this->starts_at->format('j M Y'),
            $this->accessEndsAt()->isPast() => 'Expired',
            $this->ends_at->isPast() => 'In grace period',
            default => 'Active',
        };
    }
}
