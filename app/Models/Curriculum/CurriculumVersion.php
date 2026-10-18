<?php

namespace App\Models\Curriculum;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumVersion extends Model
{
    public const STATUSES = [
        'draft' => 'Draft (hidden from the public)',
        'active' => 'Active',
        'retired' => 'Retired (kept for history)',
    ];

    protected $fillable = ['education_level_id', 'name', 'status', 'effective_from', 'effective_to', 'source_document', 'notes'];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(CurriculumNode::class);
    }

    public function isPublic(): bool
    {
        return $this->status !== 'draft';
    }

    public function statusLabel(): string
    {
        return ucfirst($this->status);
    }

    /** Versions learners may browse: everything except drafts. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereIn('status', ['active', 'retired']);
    }

    /** Active first, then by newest effective date. */
    public function scopeCurrentFirst(Builder $query): Builder
    {
        return $query->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'retired' THEN 1 ELSE 2 END")
            ->orderByDesc('effective_from')->orderByDesc('id');
    }
}
