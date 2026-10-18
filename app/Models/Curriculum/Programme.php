<?php

namespace App\Models\Curriculum;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Programme extends Model
{
    protected $fillable = ['education_level_id', 'name', 'description', 'sort', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class)->withPivot('is_core')->orderBy('subjects.sort')->orderBy('subjects.name');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
