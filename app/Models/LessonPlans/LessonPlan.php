<?php

namespace App\Models\LessonPlans;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LessonPlan extends Model
{
    use SoftDeletes;

    public const STATUSES = ['draft' => 'Private draft', 'pending' => 'Awaiting review', 'published' => 'Published', 'archived' => 'Archived'];

    /** The three lesson phases, in teaching order. */
    public const PHASES = [
        'starter' => ['Starter', 'Get learners thinking: recall, a question, a short activity.'],
        'main_activities' => ['Main activities', 'What the teacher and learners do, step by step.'],
        'reflection' => ['Reflection', 'How the lesson closes: summary, learners say what they learnt.'],
    ];

    protected $fillable = [
        'title', 'subject_id', 'class_level_id', 'curriculum_node_id', 'term', 'week', 'strand', 'sub_strand',
        'content_standard', 'indicators', 'core_competencies', 'key_vocabulary', 'lessons_per_week', 'duration_minutes',
        'class_size', 'resources', 'starter', 'main_activities', 'reflection', 'assessment', 'differentiation',
        'homework', 'teacher_notes',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'term' => 'integer',
            'week' => 'integer',
            'lessons_per_week' => 'integer',
            'duration_minutes' => 'integer',
            'class_size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $plan) {
            if (! $plan->slug) {
                $base = Str::limit(Str::slug($plan->title), 180, '') ?: 'lesson-plan';
                $slug = $base;
                for ($i = 2; static::withTrashed()->where('slug', $slug)->exists(); $i++) {
                    $slug = "{$base}-{$i}";
                }
                $plan->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function isVisible(): bool
    {
        return $this->status === 'published' && ! $this->trashed();
    }

    /** Owner, or staff who moderate tutorials, may see a plan that is not public. */
    public function canBeViewedBy(?User $user): bool
    {
        if ($this->isVisible()) {
            return true;
        }

        return $user && ($this->created_by === $user->id || ($user->isStaff() && $user->can('tutorials.manage')));
    }

    /** "Term 1 · Week 3" or whichever parts are known. */
    public function weekLabel(): ?string
    {
        return collect([$this->term ? "Term {$this->term}" : null, $this->week ? "Week {$this->week}" : null])->filter()->implode(' · ') ?: null;
    }

    /** Strand → sub-strand, falling back to the linked topic's title. */
    public function strandLabel(): ?string
    {
        return collect([$this->strand, $this->sub_strand])->filter()->implode(' → ') ?: $this->topic?->title;
    }

    /** Text fields as a list: one item per non-empty line, leading bullets removed. */
    public static function lines(?string $text): array
    {
        return collect(preg_split('/\R/', (string) $text))
            ->map(fn ($line) => trim(preg_replace('/^\s*(?:[-*•]|\d+[.)])\s*/u', '', $line)))
            ->filter()->values()->all();
    }

    public function authorName(): string
    {
        return $this->creator?->name ?? config('app.name');
    }
}
