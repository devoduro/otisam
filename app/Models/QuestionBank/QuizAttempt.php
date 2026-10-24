<?php

namespace App\Models\QuestionBank;

use App\Models\Curriculum\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Keeps what was answered for each question of a marked set. Each result carries the question,
     * the answer given, whether it was right and, for theory, 'theory' => true (not scored).
     *
     * @param  iterable<int, array<string, mixed>>  $results
     */
    public function recordAnswers(iterable $results): void
    {
        $rows = [];
        $now = now();
        foreach ($results as $row) {
            $rows[] = [
                'quiz_attempt_id' => $this->id,
                'question_id' => $row['question']->id,
                'given' => isset($row['given']) ? mb_substr((string) $row['given'], 0, 3000) : null,
                'is_correct' => ! empty($row['theory']) ? null : (bool) $row['right'],
                'created_at' => $now,
            ];
        }
        if ($rows) {
            QuizAttemptAnswer::insert($rows);
        }
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
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
