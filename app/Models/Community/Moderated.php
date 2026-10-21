<?php

namespace App\Models\Community;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/** Shared by Q&A questions and answers: author, reports, hiding and who may see a hidden post. */
trait Moderated
{
    /** Reports go with the post. Answers are removed by the database cascade, so their reports are cleared here too. */
    public static function bootModerated(): void
    {
        static::deleting(function (self $post) {
            $post->reports()->delete();
            if ($post instanceof Question) {
                // Break the question ↔ accepted answer link before the cascade removes the answers.
                Question::whereKey($post->id)->update(['accepted_answer_id' => null]);
                Report::where('reportable_type', (new Answer)->getMorphClass())->whereIn('reportable_id', $post->answers()->select('id'))->delete();
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function openReports(): MorphMany
    {
        return $this->reports()->whereNull('resolved_at');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', 'visible');
    }

    public function isVisible(): bool
    {
        return $this->status === 'visible';
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user && $this->user_id === $user->id;
    }

    /** Hidden posts stay readable by their author and by moderators. */
    public function canBeViewedBy(?User $user): bool
    {
        return $this->isVisible() || $this->isOwnedBy($user) || self::isModerator($user);
    }

    public static function isModerator(?User $user): bool
    {
        return $user && $user->isStaff() && $user->can('tutorials.manage');
    }

    /** First name only, as shown publicly. */
    public function authorFirstName(): string
    {
        return $this->author ? (strtok(trim($this->author->name), ' ') ?: 'Member') : 'Former member';
    }

    /** Hides the post once open reports reach the configured threshold. Returns true if it was hidden now. */
    public function hideIfReportedEnough(): bool
    {
        if (! $this->isVisible() || $this->openReports()->count() < max(1, (int) config('platform.qa.hide_after_reports', 3))) {
            return false;
        }
        $this->forceFill(['status' => 'hidden', 'hidden_reason' => 'reports'])->save();

        return true;
    }

    /** A short label for audit entries and the moderation list. */
    abstract public function label(): string;
}
