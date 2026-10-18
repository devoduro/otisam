<?php

namespace App\Models\Library;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Programme;
use App\Models\Curriculum\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * A library item. Workflow fields (status, publish dates, counters, author) are set by
 * ResourceService, never mass-assigned from a request.
 */
class Resource extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'draft' => 'Draft',
        'in_review' => 'In review',
        'published' => 'Published',
        'archived' => 'Archived',
    ];

    public const ACCESS_LEVELS = ['free' => 'Free', 'premium' => 'Premium'];

    public const RELATIONS = [
        'solution' => 'Solution / worked answers',
        'marking_scheme' => 'Marking scheme',
        'related' => 'Related material',
    ];

    protected $fillable = [
        'category_id', 'title', 'description', 'audience', 'education_level_id', 'class_level_id', 'programme_id',
        'subject_id', 'exam_type_id', 'exam_year', 'paper_number', 'curriculum_version_id', 'access_level',
        'preview_pages', 'copyright_holder', 'licence', 'permission_reference',
    ];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
            'published_at' => 'datetime',
            'exam_year' => 'integer',
            'preview_pages' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Slug made once from the title; kept on rename so shared links keep working.
        static::creating(function (self $resource) {
            if (! $resource->slug) {
                $base = Str::limit(Str::slug($resource->title), 180, '') ?: 'resource';
                $slug = $base;
                for ($i = 2; static::withTrashed()->where('slug', $slug)->exists(); $i++) {
                    $slug = "{$base}-{$i}";
                }
                $resource->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    public function curriculumVersion(): BelongsTo
    {
        return $this->belongsTo(CurriculumVersion::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ResourceFile::class);
    }

    public function mainFile(): HasOne
    {
        return $this->hasOne(ResourceFile::class)->where('role', ResourceFile::ROLE_MAIN)->where('is_current', true);
    }

    public function previewFile(): HasOne
    {
        return $this->hasOne(ResourceFile::class)->where('role', ResourceFile::ROLE_PREVIEW)->where('is_current', true);
    }

    /** Items attached to this one: its solutions, marking schemes, related material. */
    public function linkedResources(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'resource_links', 'resource_id', 'linked_resource_id')
            ->withPivot('relation')->withTimestamps();
    }

    /** Items this one is attached to, e.g. the paper a solution belongs to. */
    public function linkedFrom(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'resource_links', 'linked_resource_id', 'resource_id')
            ->withPivot('relation')->withTimestamps();
    }

    public function curriculumNodes(): BelongsToMany
    {
        return $this->belongsToMany(CurriculumNode::class);
    }

    /** Visible to the public: published, and its scheduled time (if any) has arrived. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('resources.status', 'published')
            ->where(fn ($q) => $q->whereNull('resources.publish_at')->orWhere('resources.publish_at', '<=', now()));
    }

    public function isVisible(): bool
    {
        return $this->status === 'published' && ($this->publish_at === null || $this->publish_at->isPast()) && ! $this->trashed();
    }

    public function isFree(): bool
    {
        return $this->access_level === 'free';
    }

    public function isForTeachersOnly(): bool
    {
        return $this->audience === 'teacher';
    }

    /** Short "WASSCE May/June 2023 · Paper 2" style label for exam papers. */
    public function examLabel(): ?string
    {
        $parts = array_filter([
            $this->examType?->name,
            $this->exam_year,
            $this->paper_number ? 'Paper '.$this->paper_number : null,
        ]);

        return $parts ? implode(' · ', $parts) : null;
    }
}
