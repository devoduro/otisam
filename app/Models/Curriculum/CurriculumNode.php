<?php

namespace App\Models\Curriculum;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One entry in a subject's curriculum tree. Structure (version, subject, parent, path, depth,
 * sort) is maintained only by App\Services\Curriculum\CurriculumTree, never from request input.
 */
class CurriculumNode extends Model
{
    public const TYPES = [
        'strand' => 'Strand',
        'sub_strand' => 'Sub-strand',
        'content_standard' => 'Content standard',
        'indicator' => 'Learning indicator',
        'topic' => 'Topic',
        'subtopic' => 'Subtopic',
    ];

    /** Deepest level allowed (0 = top). Six levels cover strand → … → indicator with room to spare. */
    public const MAX_DEPTH = 5;

    protected $fillable = ['class_level_id', 'node_type', 'code', 'title', 'statement', 'is_official', 'source_ref'];

    protected function casts(): array
    {
        return ['is_official' => 'boolean'];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(CurriculumVersion::class, 'curriculum_version_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort')->orderBy('id');
    }

    /** The path a child of this node gets. */
    public function childPath(): string
    {
        return $this->path.$this->id.'/';
    }

    /** @return array<int, int> ancestor ids, root first */
    public function ancestorIds(): array
    {
        return array_values(array_map('intval', array_filter(explode('/', $this->path))));
    }

    public function scopeInTree(Builder $query, int $versionId, int $subjectId): Builder
    {
        return $query->where('curriculum_version_id', $versionId)->where('subject_id', $subjectId);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->node_type] ?? $this->node_type;
    }
}
