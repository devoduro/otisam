<?php

namespace App\Models\Community;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    public const REASONS = [
        'spam' => 'Spam or advertising',
        'rude' => 'Rude or bullying',
        'cheating' => 'Exam leak or cheating',
        'wrong' => 'Wrong or misleading',
        'other' => 'Something else',
    ];

    protected $table = 'qa_reports';

    protected $fillable = ['user_id', 'reason', 'note'];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /** How many posts have at least one open report. */
    public static function openPostCount(): int
    {
        return static::query()->fromSub(static::whereNull('resolved_at')->select('reportable_type', 'reportable_id')->distinct(), 'open_posts')->count();
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
