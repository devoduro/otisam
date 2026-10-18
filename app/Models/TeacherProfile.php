<?php

namespace App\Models;

use App\Enums\TeacherVerificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TeacherProfile extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    /** verification_status / verified_*, slug and photo_path are set by the services, never from input. */
    protected $fillable = [
        'staff_number',
        'current_rank',
        'school_name',
        'region',
        'district',
        'subjects_taught',
        'is_public',
        'headline',
        'bio',
        'years_teaching',
    ];

    protected function casts(): array
    {
        return [
            'verification_status' => TeacherVerificationStatus::class,
            'verified_at' => 'datetime',
            'is_public' => 'boolean',
            'years_teaching' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isApproved(): bool
    {
        return $this->verification_status === TeacherVerificationStatus::Approved;
    }

    /** Quick sign-up leaves school and subjects empty; administrators need them to verify a teacher. */
    public function isComplete(): bool
    {
        return filled($this->school_name) && filled($this->subjects_taught) && filled($this->region);
    }

    /** Approved teachers who chose to appear in the public teachers directory. */
    public function scopeListed(Builder $query): Builder
    {
        return $query->where('is_public', true)->whereNotNull('slug')
            ->where('verification_status', TeacherVerificationStatus::Approved->value);
    }

    public function isListed(): bool
    {
        return $this->is_public && $this->slug && $this->isApproved();
    }

    /** Gives the profile its public address the first time it is made public. */
    public function ensureSlug(): void
    {
        if (! $this->slug) {
            $base = Str::limit(Str::slug($this->user?->name ?: 'teacher'), 120, '') ?: 'teacher';
            do {
                $slug = $base.'-'.Str::lower(Str::random(4));
            } while (static::where('slug', $slug)->exists());
            $this->slug = $slug;
        }
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path && is_file(public_path($this->photo_path)) ? asset($this->photo_path) : null;
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim((string) $this->user?->name)))->filter()->take(2)
            ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))->implode('') ?: 'T';
    }

    /** "Core Mathematics, Physics" => ['Core Mathematics', 'Physics']. */
    public function subjectList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/[,;\/]+/', (string) $this->subjects_taught))));
    }
}
