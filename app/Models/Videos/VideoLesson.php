<?php

namespace App\Models\Videos;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class VideoLesson extends Model
{
    use SoftDeletes;

    public const STATUSES = ['draft' => 'Draft', 'pending' => 'Awaiting review', 'published' => 'Published', 'archived' => 'Archived'];

    protected $fillable = ['title', 'description', 'youtube_id', 'subject_id', 'class_level_id', 'curriculum_node_id', 'duration_minutes', 'instructor', 'access_level'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'duration_minutes' => 'integer'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $video) {
            if (! $video->slug) {
                $base = Str::limit(Str::slug($video->title), 180, '') ?: 'video';
                $slug = $base;
                for ($i = 2; static::withTrashed()->where('slug', $slug)->exists(); $i++) {
                    $slug = "{$base}-{$i}";
                }
                $video->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * The 11-character YouTube id from any common link form (watch?v=, youtu.be/, embed/, shorts/,
     * live/) or a bare id. Null if it is not a YouTube video.
     */
    public static function parseYoutubeId(?string $input): ?string
    {
        $input = trim((string) $input);

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $input)) {
            return $input;
        }

        $parts = parse_url($input);
        $host = Str::lower($parts['host'] ?? '');
        if (! in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be', 'www.youtube-nocookie.com', 'youtube-nocookie.com'], true)) {
            return null;
        }

        if ($host === 'youtu.be') {
            $candidate = trim($parts['path'] ?? '', '/');
        } else {
            parse_str($parts['query'] ?? '', $query);
            $candidate = $query['v'] ?? (preg_match('#^/(?:embed|shorts|live|v)/([^/?]+)#', $parts['path'] ?? '', $m) ? $m[1] : '');
        }

        return preg_match('/^[A-Za-z0-9_-]{11}$/', (string) $candidate) ? $candidate : null;
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(CurriculumNode::class, 'curriculum_node_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function isVisible(): bool
    {
        return $this->status === 'published' && ! $this->trashed();
    }

    public function isFree(): bool
    {
        return $this->access_level === 'free';
    }

    public function thumbnailUrl(): string
    {
        return "https://i.ytimg.com/vi/{$this->youtube_id}/hqdefault.jpg";
    }

    /** Privacy-enhanced embed: no YouTube cookies until the viewer presses play. */
    public function embedUrl(): string
    {
        return "https://www.youtube-nocookie.com/embed/{$this->youtube_id}?rel=0&modestbranding=1&autoplay=1";
    }
}
