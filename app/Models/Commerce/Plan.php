<?php

namespace App\Models\Commerce;

use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Plan extends Model
{
    protected $fillable = ['name', 'description', 'duration_months', 'price_minor', 'currency', 'benefits', 'is_active', 'is_featured', 'sort'];

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'price_minor' => 'integer',
            'duration_months' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $plan) {
            if (! $plan->slug) {
                $base = Str::slug($plan->name) ?: 'plan';
                $slug = $base;
                for ($i = 2; static::where('slug', $slug)->exists(); $i++) {
                    $slug = "{$base}-{$i}";
                }
                $plan->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('duration_months');
    }

    public function price(): string
    {
        return Money::format($this->price_minor, $this->currency);
    }

    /** Price per month, for comparing plans. */
    public function monthlyPrice(): string
    {
        return Money::format(intdiv($this->price_minor, max(1, $this->duration_months)), $this->currency);
    }

    public function durationLabel(): string
    {
        return $this->duration_months === 12 ? '12 months (1 year)' : $this->duration_months.' '.Str::plural('month', $this->duration_months);
    }
}
