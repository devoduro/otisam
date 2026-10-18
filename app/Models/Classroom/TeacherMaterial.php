<?php

namespace App\Models\Classroom;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** A lesson plan, note, scheme of work… uploaded by a teacher, private or shared with teachers. */
class TeacherMaterial extends Model
{
    use HasUlids, SoftDeletes;

    public const KINDS = [
        'lesson_plan' => 'Lesson plan',
        'lesson_note' => 'Lesson notes',
        'scheme_of_work' => 'Scheme of work',
        'worksheet' => 'Worksheet / exercise',
        'slides' => 'Slides',
        'assessment' => 'Test / exam paper',
        'other' => 'Other',
    ];

    public const VISIBILITY = ['private' => 'Only me', 'teachers' => 'Teachers only', 'public' => 'Everyone (shown on Downloads)'];

    protected $fillable = ['kind', 'title', 'description', 'subject_id', 'class_level_id', 'curriculum_node_id', 'visibility'];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(CurriculumNode::class, 'curriculum_node_id');
    }

    /** Mine, plus what other teachers have shared. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->where(fn ($q) => $q->where('user_id', $user->id)->orWhereIn('visibility', ['teachers', 'public']));
    }

    public function isShared(): bool
    {
        return in_array($this->visibility, ['teachers', 'public'], true);
    }
}
