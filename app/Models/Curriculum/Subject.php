<?php

namespace App\Models\Curriculum;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Subject extends Model
{
    public const CATEGORIES = ['core' => 'Core', 'elective' => 'Elective'];

    protected $fillable = ['education_level_id', 'name', 'code', 'category', 'description', 'sort', 'is_active'];

    /** "English Language · JHS": tells same-named subjects at different levels apart in flat lists. */
    public function labelWithLevel(): string
    {
        return $this->educationLevel ? "{$this->name} · {$this->educationLevel->code}" : $this->name;
    }

    /** Which exam the subject's practice questions imitate: BECE (JHS), WASSCE (SHS) or GES promotion. */
    public function examStyle(): string
    {
        return match ($this->educationLevel?->code) {
            'JHS' => 'BECE',
            EducationLevel::PROFESSIONAL => 'GES promotion',
            default => 'WASSCE',
        };
    }

    public function isProfessional(): bool
    {
        return $this->educationLevel?->code === EducationLevel::PROFESSIONAL;
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        // Slugs are generated once and kept, so public links survive a rename.
        static::creating(function (self $subject) {
            if (! $subject->slug) {
                $base = Str::slug($subject->name) ?: 'subject';
                $slug = $base;
                for ($i = 2; static::where('slug', $slug)->exists(); $i++) {
                    $slug = "{$base}-{$i}";
                }
                $subject->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function programmes(): BelongsToMany
    {
        return $this->belongsToMany(Programme::class)->withPivot('is_core');
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(CurriculumNode::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
