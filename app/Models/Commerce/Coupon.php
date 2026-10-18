<?php

namespace App\Models\Commerce;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    public const TYPES = ['percent' => 'Percentage off', 'fixed' => 'Fixed amount off'];

    protected $fillable = ['code', 'description', 'type', 'value', 'plan_id', 'starts_at', 'ends_at', 'max_redemptions', 'per_user_limit', 'is_active'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'value' => 'integer',
        ];
    }

    protected function code(): Attribute
    {
        return Attribute::make(set: fn ($v) => strtoupper(trim((string) $v)));
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    /** Discount in minor units for a price, never more than the price. */
    public function discountFor(int $priceMinor): int
    {
        $discount = $this->type === 'percent'
            ? intdiv($priceMinor * min(100, $this->value), 100)
            : $this->value;

        return min($priceMinor, $discount);
    }

    /** Why this coupon cannot be used for this plan and user, or null if it can. */
    public function problemFor(Plan $plan, User $user): ?string
    {
        if (! $this->is_active || ($this->starts_at && $this->starts_at->isFuture()) || ($this->ends_at && $this->ends_at->isPast())) {
            return 'This code is not valid now.';
        }
        if ($this->plan_id && $this->plan_id !== $plan->id) {
            return 'This code is for a different plan.';
        }
        if ($this->max_redemptions !== null && $this->redemptions()->count() >= $this->max_redemptions) {
            return 'This code has been fully used.';
        }
        if ($this->redemptions()->where('user_id', $user->id)->count() >= $this->per_user_limit) {
            return 'You have already used this code.';
        }

        return null;
    }
}
