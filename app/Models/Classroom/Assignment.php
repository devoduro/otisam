<?php

namespace App\Models\Classroom;

use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionRevision;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Work a teacher sets for a class: a written assignment or an auto-marked quiz. */
class Assignment extends Model
{
    use HasUlids, SoftDeletes;

    public const TYPES = ['assignment' => 'Assignment', 'quiz' => 'Quiz'];

    public const STATUSES = ['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed'];

    protected $fillable = ['type', 'title', 'instructions', 'due_at', 'time_limit_minutes', 'max_marks', 'show_answers', 'shuffle_options', 'block_copy'];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'published_at' => 'datetime',
            'show_answers' => 'boolean',
            'shuffle_options' => 'boolean',
            'block_copy' => 'boolean',
            'max_marks' => 'decimal:2',
            'time_limit_minutes' => 'integer',
        ];
    }

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'assignment_question')->withPivot(['question_revision_id', 'sort', 'marks'])->orderByPivot('sort');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereIn('status', ['published', 'closed']);
    }

    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
    }

    public function isPublished(): bool
    {
        return in_array($this->status, ['published', 'closed'], true);
    }

    /** Students can still start or hand in work (late work is accepted until the teacher closes it). */
    public function isOpen(): bool
    {
        return $this->status === 'published';
    }

    public function isOverdue(): bool
    {
        return $this->due_at !== null && $this->due_at->isPast();
    }

    /**
     * A question's options in the order this student sees them, each with the letter shown on
     * screen ('label'). The submitted value stays the option's own id, so marking is unchanged.
     * Shuffled per student and question with a fixed seed, so reloading keeps the same order.
     *
     * @return array<int, array{id: string, text: string, label: string}>
     */
    public function optionsFor(?QuestionRevision $revision, Submission $submission, string $questionUlid): array
    {
        $options = array_values($revision?->payload['options'] ?? []);
        if ($this->shuffle_options && count($options) > 1) {
            mt_srand(crc32($submission->id.':'.$questionUlid));
            shuffle($options);
            mt_srand();
        }

        foreach ($options as $i => $option) {
            $options[$i]['label'] = $this->shuffle_options ? chr(65 + $i) : $option['id'];
        }

        return $options;
    }

    public function totalMarks(): float
    {
        return $this->isQuiz() ? (float) $this->questions->sum('pivot.marks') : (float) ($this->max_marks ?? 0);
    }
}
