<?php

namespace App\Models\Library;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TakedownRequest extends Model
{
    public const STATUSES = ['open' => 'Open', 'resolved' => 'Resolved (content removed or changed)', 'rejected' => 'Rejected'];

    protected $fillable = ['resource_id', 'resource_url', 'name', 'email', 'phone', 'rights_holder', 'details'];

    protected function casts(): array
    {
        return ['handled_at' => 'datetime'];
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class)->withTrashed();
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
