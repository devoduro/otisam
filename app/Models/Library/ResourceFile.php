<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A stored file. Never exposed by path: always streamed by ResourceFileController. */
class ResourceFile extends Model
{
    use HasUlids;

    public const ROLE_MAIN = 'main';

    public const ROLE_PREVIEW = 'preview';

    protected $fillable = [];

    protected $hidden = ['disk', 'path', 'sha256'];

    protected function casts(): array
    {
        return ['size' => 'integer', 'page_count' => 'integer', 'is_current' => 'boolean'];
    }

    /** The ULID goes in `uuid`; the integer id stays the primary key. */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function isPdf(): bool
    {
        return $this->extension === 'pdf';
    }

    public function humanSize(): string
    {
        $kb = $this->size / 1024;

        return $kb >= 1024 ? number_format($kb / 1024, 1).' MB' : number_format(max(1, $kb)).' KB';
    }
}
