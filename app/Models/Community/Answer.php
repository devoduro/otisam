<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Answer extends Model
{
    use Moderated;

    protected $table = 'qa_answers';

    protected $fillable = ['body'];

    protected function casts(): array
    {
        return ['edited_at' => 'datetime'];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'qa_question_id');
    }

    /** Only approved teachers get the badge, so a pending sign-up cannot claim it. */
    public function byTeacher(): bool
    {
        return (bool) $this->author?->isApprovedTeacher();
    }

    public function label(): string
    {
        return 'Answer: '.Str::limit($this->body, 80);
    }
}
