<?php

namespace App\Models;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\Programme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'class_level_id',
        'programme_id',
        'school_name',
        'region',
        'is_minor',
        'guardian_name',
        'guardian_phone',
    ];

    protected function casts(): array
    {
        return [
            'is_minor' => 'boolean',
            'guardian_consent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }
}
