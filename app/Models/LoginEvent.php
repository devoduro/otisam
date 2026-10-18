<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'identifier_hash', 'success', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return ['success' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
