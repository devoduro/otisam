<?php

namespace App\Models\Classroom;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A student's work on one assignment. Written by the services, never mass-assigned from input. */
class Submission extends Model
{
    protected $guarded = ['*'];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'is_late' => 'boolean',
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function isGraded(): bool
    {
        return $this->graded_at !== null;
    }

    public function percent(): ?int
    {
        return ($this->score !== null && (float) $this->max_score > 0) ? (int) round((float) $this->score / (float) $this->max_score * 100) : null;
    }
}
