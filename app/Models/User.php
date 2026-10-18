<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\UserStatus;
use App\Support\Permissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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

        $phone = \App\Support\Phone::clean($login);

        return $phone ? static::where('phone', $phone)->first() : null;
    }
}
