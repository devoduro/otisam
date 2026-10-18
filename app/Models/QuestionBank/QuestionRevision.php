<?php

namespace App\Models\QuestionBank;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Immutable question content. The answer key is deliberately NOT a relation here (see QuestionKey). */
class QuestionRevision extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['*'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'media' => 'array'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => false);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
