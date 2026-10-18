<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationMessage extends Model
{
    public const CHANNEL_SMS = 'sms';
    public const CHANNEL_MAIL = 'mail';

    protected $fillable = [
        'idempotency_key', 'event_key', 'category', 'user_id', 'channel', 'recipient', 'subject', 'body', 'status',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
