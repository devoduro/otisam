<?php

namespace App\Models\Curriculum;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationLevel extends Model
{
    protected $fillable = ['name', 'code', 'sort', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function classLevels(): HasMany
    {
        return $this->hasMany(ClassLevel::class)->orderBy('sort')->orderBy('name');
    }

    public function programmes(): HasMany
    {
        return $this->hasMany(Programme::class)->orderBy('sort')->orderBy('name');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class)->orderBy('sort')->orderBy('name');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(CurriculumVersion::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('name');
    }
}
