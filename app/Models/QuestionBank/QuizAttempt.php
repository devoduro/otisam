<?php

namespace App\Models\QuestionBank;

use App\Models\Curriculum\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['subject_id', 'kind', 'exam_paper_id', 'score', 'total', 'seconds', 'late'];

    protected function casts(): array
    {
        return ['score' => 'integer', 'total' => 'integer', 'seconds' => 'integer', 'late' => 'boolean', 'created_at' => 'datetime'];
    }

    /** Saves a marked set for a signed-in student; anyone else is not recorded. */
    public static function recordFor(?User $user, array $attributes): ?self
    {
        if (! $user?->isStudent()) {
            return null;
        }
        $attempt = new self($attributes);
        $attempt->user_id = $user->id;
        $attempt->save();

        return $attempt;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function paper(): BelongsTo
    {
        return $this->belongsTo(ExamPaper::class, 'exam_paper_id');
    }

    public function percent(): int
    {
        return $this->total ? (int) round($this->score / $this->total * 100) : 0;
    }
}
