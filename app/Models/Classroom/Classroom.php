<?php

namespace App\Models\Classroom;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/** A teacher's class. Students join with the join code. */
class Classroom extends Model
{
    use HasUlids;

    protected $fillable = ['name', 'subject_id', 'class_level_id', 'description', 'joining_open'];

    protected function casts(): array
    {
        return ['joining_open' => 'boolean', 'archived_at' => 'datetime'];
    }

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $classroom) {
            $classroom->join_code ??= self::newJoinCode();
        });
    }

    /** Six characters without look-alikes (no 0/O, 1/I/L), easy to read out in class. */
    public static function newJoinCode(): string
    {
        do {
            $code = collect(range(1, 6))->map(fn () => '23456789ABCDEFGHJKMNPQRSTUVWXYZ'[random_int(0, 30)])->implode('');
        } while (static::where('join_code', $code)->exists());

        return $code;
    }

    public static function normaliseCode(?string $code): string
    {
        return Str::upper(preg_replace('/[^A-Za-z0-9]/', '', (string) $code));
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_student', 'classroom_id', 'student_id')->withPivot('joined_at')->orderBy('name');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    public function hasStudent(User $user): bool
    {
        return $this->students()->whereKey($user->id)->exists();
    }
}
