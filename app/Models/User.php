<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\UserStatus;
use App\Models\Classroom\Classroom;
use App\Support\Permissions;
use App\Support\Phone;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * `account_type`, `status` and the verification timestamps are deliberately not fillable:
     * they are set explicitly by the registration and admin services, never from request input.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'account_type' => AccountType::class,
            'status' => UserStatus::class,
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'must_change_password' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    /** Classes this student has joined. */
    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'classroom_student', 'student_id', 'classroom_id')->withPivot('joined_at');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by');
    }

    /** The code in this user's invite link, created the first time it is needed. */
    public function referralCode(): string
    {
        if (! $this->referral_code) {
            do {
                $code = Str::lower(Str::random(8));
            } while (self::where('referral_code', $code)->exists());
            $this->forceFill(['referral_code' => $code])->saveQuietly();
        }

        return $this->referral_code;
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function isStudent(): bool
    {
        return $this->account_type === AccountType::Student;
    }

    public function isTeacher(): bool
    {
        return $this->account_type === AccountType::Teacher;
    }

    public function isStaff(): bool
    {
        return $this->account_type === AccountType::Staff;
    }

    public function isSuperAdmin(): bool
    {
        return $this->isStaff() && $this->hasRole(Permissions::SUPER_ADMIN);
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function isApprovedTeacher(): bool
    {
        return $this->isTeacher() && $this->teacherProfile?->isApproved();
    }

    /** Where this account lands after signing in. */
    public function homeRoute(): string
    {
        return match ($this->account_type) {
            AccountType::Staff => 'admin.dashboard',
            AccountType::Teacher => 'teach.dashboard',
            AccountType::Student => 'learn.dashboard',
        };
    }

    /** Find an account by phone (any common Ghana format) or email. */
    public static function findByLogin(string $login): ?self
    {
        $login = trim($login);

        if (str_contains($login, '@')) {
            return static::where('email', mb_strtolower($login))->first();
        }

        $phone = Phone::clean($login);

        return $phone ? static::where('phone', $phone)->first() : null;
    }
}
