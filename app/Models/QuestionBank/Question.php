<?php

namespace App\Models\QuestionBank;

use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Identity and metadata of a question. Content lives in revisions; answers in question_keys. */
class Question extends Model
{
    use HasUlids, SoftDeletes;

    public const STATUSES = ['draft' => 'Draft', 'in_review' => 'In review', 'changes_requested' => 'Changes requested', 'published' => 'Published', 'archived' => 'Archived'];

    /** Types learners can practise: marked automatically, except theory, which they compare with a model answer. */
    public const PRACTICE_TYPES = ['mcq', 'true_false', 'fill_in', 'theory'];

    /** Practice types the computer can mark. */
    public const OBJECTIVE_TYPES = ['mcq', 'true_false', 'fill_in'];

    public const CONFIDENTIALITY = ['practice' => 'Practice (self-test)', 'exam' => 'Formal exams only', 'promotion' => 'Teacher promotion exams'];

    protected $guarded = ['*'];

    protected function casts(): array
    {
        return ['default_marks' => 'decimal:2', 'reviewed_at' => 'datetime', 'difficulty' => 'integer'];
    }

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(QuestionRevision::class)->orderByDesc('revision_no');
    }

    public function currentRevision(): BelongsTo
    {
        return $this->belongsTo(QuestionRevision::class, 'current_revision_id');
    }

    public function curriculumNodes(): BelongsToMany
    {
        return $this->belongsToMany(CurriculumNode::class);
    }
}
