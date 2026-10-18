<?php

namespace App\Services\Videos;

use App\Models\Curriculum\Subject;
use App\Models\Videos\VideoLesson;
use App\Services\Audit;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Turns a configured channel's uploads (config/platform.php "video_channels") into published video
 * lessons. Safe to run again: a video already on the platform, including one an admin edited or
 * deleted, is left alone, so admin changes are never overwritten.
 */
class YoutubeChannelImporter
{
    public function __construct(private readonly YoutubeChannelFeed $feed) {}

    /**
     * @return array{created: int, skipped: int, total: int, source: string}
     */
    public function import(string $channelKey, ?int $limit = null, bool $publish = true): array
    {
        $channel = config("platform.video_channels.{$channelKey}");
        if (! $channel) {
            throw new RuntimeException("No video channel called \"{$channelKey}\" in config/platform.php.");
        }

        $core = Subject::where('slug', $channel['subject'])->first();
        if (! $core) {
            throw new RuntimeException("Subject \"{$channel['subject']}\" does not exist.");
        }
        $elective = isset($channel['elective_subject']) ? Subject::where('slug', $channel['elective_subject'])->first() : null;

        $uploads = $this->feed->uploads($channel['channel_id'], $limit);
        $known = VideoLesson::withTrashed()->whereIn('youtube_id', array_column($uploads, 'id'))->pluck('youtube_id')->flip();

        $created = 0;
        // Oldest first, so slugs and ids follow the channel's own order.
        foreach (array_reverse($uploads) as $upload) {
            if (isset($known[$upload['id']]) || $upload['title'] === '') {
                continue;
            }

            $video = new VideoLesson([
                'title' => Str::limit($this->tidyTitle($upload['title']), 200, ''),
                'description' => $upload['description'] ? Str::limit($upload['description'], 5000, '') : null,
                'youtube_id' => $upload['id'],
                'subject_id' => ($elective && $this->isElective($upload['title'], $channel['elective_keywords'] ?? [])) ? $elective->id : $core->id,
                'duration_minutes' => $upload['duration_seconds'] ? max(1, (int) round($upload['duration_seconds'] / 60)) : null,
                'instructor' => $channel['name'],
                'access_level' => $channel['access_level'] ?? 'free',
            ]);
            $video->status = $publish ? 'published' : 'draft';
            $video->published_at = $publish ? ($upload['published_at'] ?? now()) : null;
            $video->save();
            $created++;
        }

        if ($created) {
            Audit::record('content', 'video.imported', "Imported {$created} video lesson(s) from {$channel['name']} on YouTube");
        }

        return [
            'created' => $created,
            'skipped' => count($uploads) - $created,
            'total' => count($uploads),
            'source' => $this->feed->usesApi() ? 'api' : 'rss',
        ];
    }

    private function isElective(string $title, array $keywords): bool
    {
        $title = Str::lower($title);

        return collect($keywords)->contains(fn ($word) => str_contains($title, Str::lower($word)));
    }

    /** "THE CONCEPT OF LIMITS" => "The Concept Of Limits"; mixed-case titles are kept as typed. */
    private function tidyTitle(string $title): string
    {
        $title = trim(preg_replace('/\s+/', ' ', $title));

        return mb_strtoupper($title) === $title && preg_match('/[A-Z]{4}/', $title) ? Str::title(Str::lower($title)) : $title;
    }
}
