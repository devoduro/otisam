<?php

namespace App\Models\QuestionBank;

use App\Models\Curriculum\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** A numbered objective paper for one subject. Questions are kept in order on the pivot. */
class ExamPaper extends Model
{
    protected $fillable = ['number', 'title', 'exam_style', 'duration_minutes'];

    protected function casts(): array
    {
        return ['number' => 'integer', 'duration_minutes' => 'integer', 'published_at' => 'datetime'];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class)->withPivot('position')->orderByPivot('position');
    }

    /**
     * "Core Mathematics Mock 2026": paper 1 takes the current year and each later paper the year
     * before, newest first. These are Otisam's own papers; the year is only a label, not an exam
     * year, so pages next to it say they are not WAEC papers.
     */
    public static function titleFor(string $subjectName, int $number, ?int $baseYear = null): string
    {
        return $subjectName.' Mock '.(($baseYear ?? (int) now()->year) - $number + 1);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function isVisible(): bool
    {
        return $this->status === 'published';
    }
}
