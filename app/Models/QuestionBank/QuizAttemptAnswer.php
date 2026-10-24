<?php

namespace App\Models\QuestionBank;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One answered question inside a saved QuizAttempt. */
class QuizAttemptAnswer extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['quiz_attempt_id', 'question_id', 'given', 'is_correct'];

    protected function casts(): array
    {
        return ['is_correct' => 'boolean', 'created_at' => 'datetime'];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
