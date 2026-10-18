<?php

namespace App\Models;

use App\Enums\TeacherVerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherProfile extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    /** verification_status / verified_* are set only by TeacherVerificationService. */
    protected $fillable = [
        'staff_number',
        'current_rank',
        'school_name',
        'region',
        'district',
        'subjects_taught',
    ];

    protected function casts(): array
    {
        return [
            'verification_status' => TeacherVerificationStatus::class,
            'verified_at' => 'datetime',
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
}
