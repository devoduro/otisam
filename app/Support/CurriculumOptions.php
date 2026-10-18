<?php

namespace App\Support;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\Programme;
use Illuminate\Support\Collection;

/**
 * Choices offered on sign-up and profile forms: only active classes and programmes of active
 * education levels, as edited in Admin → Curriculum.
 */
class CurriculumOptions
{
    /** @return Collection<int, ClassLevel> */
    public static function classLevels(): Collection
    {
        return ClassLevel::query()
            ->where('class_levels.is_active', true)
            ->where('education_levels.is_active', true)
            ->join('education_levels', 'education_levels.id', '=', 'class_levels.education_level_id')
            ->orderBy('education_levels.sort')->orderBy('class_levels.sort')->orderBy('class_levels.name')
            ->select('class_levels.*')
            ->get();
    }

    /** @return Collection<int, Programme> */
    public static function programmes(): Collection
    {
        return Programme::active()
            ->whereHas('educationLevel', fn ($q) => $q->where('is_active', true))
            ->orderBy('sort')->orderBy('name')
            ->get();
    }

    /** @return array<int, string> id => label, for <x-select> */
    public static function classLevelChoices(): array
    {
        return self::classLevels()->pluck('name', 'id')->all();
    }

    /** @return array<int, string> */
    public static function programmeChoices(): array
    {
        return self::programmes()->pluck('name', 'id')->all();
    }
}
