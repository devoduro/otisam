<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ResourceCategory extends Model
{
    public const AUDIENCES = ['student' => 'Students', 'teacher' => 'Teachers', 'both' => 'Students and teachers'];

    protected $fillable = ['name', 'audience', 'is_exam_paper', 'sort', 'is_active'];

    protected function casts(): array
    {
        return ['is_exam_paper' => 'boolean', 'is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $category) {
            $category->slug ??= Str::slug($category->name);
        });
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'category_id');
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
