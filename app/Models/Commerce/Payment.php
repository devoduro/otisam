<?php

namespace App\Models\Commerce;

use App\Models\User;
use App\Support\Money;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** All fields are set by the commerce services; nothing here is mass-assignable from requests. */
class Payment extends Model
{
    use HasUlids;

    public const STATUSES = [
        'initiated' => 'Started',
        'pending' => 'Waiting for confirmation',
        'success' => 'Paid',
        'failed' => 'Failed',
        'abandoned' => 'Abandoned',
        'refunded' => 'Refunded',
    ];

    protected $guarded = ['*'];

    protected $hidden = ['provider_payload', 'authorization_url'];

    protected function casts(): array
    {
        return [
            'provider_payload' => 'array',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
            'amount_minor' => 'integer',
            'list_price_minor' => 'integer',
            'discount_minor' => 'integer',
        ];
    }

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, ['success', 'failed', 'abandoned', 'refunded'], true);
    }

    public function amount(): string
    {
        return Money::format($this->amount_minor, $this->currency);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
