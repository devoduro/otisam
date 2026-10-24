<?php

namespace App\Services\Videos;

use App\Models\Curriculum\Subject;
use App\Models\Videos\VideoLesson;
use App\Services\Audit;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Turns a configured channel's uploads (config/platform.php "video_channels") into published video
 * lessons, each filed under a curriculum topic by VideoTopicClassifier. Safe to run again: a video
 * already on the platform, including one an admin edited or deleted, is left alone, so admin
 * changes are never overwritten. Clips that are not lessons (competition footage, greetings) are
 * kept as drafts for an admin to decide on.
 */
class YoutubeChannelImporter
{
    public function __construct(
        private readonly YoutubeChannelFeed $feed,
        private readonly VideoTopicClassifier $topics,
    ) {}

    /**
     * @return array{created: int, skipped: int, total: int, source: string}
     */
    public function import(string $channelKey, ?int $limit = null, bool $publish = true): array
    {
        $channel = $this->channel($channelKey);
        $uploads = $this->feed->uploads($channel['channel_id'], $limit);

        $created = $this->create($channel, $uploads, $publish);
        $this->retag(array_column($uploads, 'id'));

        return [
            'created' => $created,
            'skipped' => count($uploads) - $created,
            'total' => count($uploads),
            'source' => $this->feed->usesApi() ? 'api' : 'rss',
        ];
    }

    /**
     * Adds a channel's back catalogue from a saved list (database/seeders/data/videos), for when
     * no YouTube API key is set and the RSS feed only reaches the latest 15 uploads.
     *
     * @param  list<array{id: string, title: string, short?: bool}>  $entries  newest first
     * @return array{created: int, retagged: int, total: int}
     */
    public function importCatalogue(string $channelKey, array $entries, bool $publish = true): array
    {
        $channel = $this->channel($channelKey);
        // The saved list has no dates; spacing them a minute apart keeps the channel's order.
        $now = CarbonImmutable::now();
        $uploads = array_map(fn ($entry, $i) => [
            'id' => $entry['id'],
            'title' => $entry['title'],
            'description' => null,
            'published_at' => $now->subMinutes($i),
            'duration_seconds' => ! empty($entry['short']) ? 60 : null,
        ], $entries, array_keys($entries));

        $created = $this->create($channel, $uploads, $publish);

        return ['created' => $created, 'retagged' => $this->retag(array_column($entries, 'id')), 'total' => count($entries)];
    }

    /**
     * Files videos that have no topic yet. Videos an admin has already given a topic keep it.
     *
     * @param  list<string>  $youtubeIds
     */
    public function retag(array $youtubeIds): int
    {
        $count = 0;
        VideoLesson::whereIn('youtube_id', $youtubeIds)->whereNull('curriculum_node_id')->each(function (VideoLesson $video) use (&$count) {
            if ($match = $this->topics->resolve($video->title)) {
                $video->forceFill([
                    'subject_id' => $match['subject']->id,
                    'curriculum_node_id' => $match['node']->id,
                    'class_level_id' => $match['node']->class_level_id ?? $video->class_level_id,
                ])->save();
                $count++;
            }
        });

        return $count;
    }

    private function channel(string $channelKey): array
    {
        $channel = config("platform.video_channels.{$channelKey}");
        if (! $channel) {
            throw new RuntimeException("No video channel called \"{$channelKey}\" in config/platform.php.");
        }

        return $channel;
    }

    private function create(array $channel, array $uploads, bool $publish): int
    {
        $core = Subject::where('slug', $channel['subject'])->first();
        if (! $core) {
            throw new RuntimeException("Subject \"{$channel['subject']}\" does not exist.");
        }
        $elective = isset($channel['elective_subject']) ? Subject::where('slug', $channel['elective_subject'])->first() : null;

        $known = VideoLesson::withTrashed()->whereIn('youtube_id', array_column($uploads, 'id'))->pluck('youtube_id')->flip();

        $created = 0;
        // Oldest first, so slugs and ids follow the channel's own order.
        foreach (array_reverse($uploads) as $upload) {
            if (isset($known[$upload['id']]) || $upload['title'] === '') {
                continue;
            }

            $title = $this->tidyTitle($upload['title']);
            $match = $this->topics->resolve($upload['title']);
            $video = new VideoLesson([
                'title' => Str::limit($title, 200, ''),
                'description' => $upload['description'] ? Str::limit($upload['description'], 5000, '') : null,
                'youtube_id' => $upload['id'],
                'subject_id' => $match['subject']->id
                    ?? (($elective && $this->isElective($upload['title'], $channel['elective_keywords'] ?? [])) ? $elective->id : $core->id),
                'curriculum_node_id' => $match['node']->id ?? null,
                'class_level_id' => $match['node']->class_level_id ?? null,
                'duration_minutes' => $upload['duration_seconds'] ? max(1, (int) round($upload['duration_seconds'] / 60)) : null,
                'instructor' => $channel['name'],
                'access_level' => $channel['access_level'] ?? 'free',
            ]);
            $live = $publish && $this->topics->isLesson($upload['title']);
            $video->status = $live ? 'published' : 'draft';
            $video->published_at = $live ? ($upload['published_at'] ?? now()) : null;
            $video->save();
            $created++;
            $known[$upload['id']] = true;
        }

        if ($created) {
            Audit::record('content', 'video.imported', "Imported {$created} video lesson(s) from {$channel['name']} on YouTube");
        }

        return $created;
    }

    private function isElective(string $title, array $keywords): bool
    {
        $title = Str::lower($title);

        return collect($keywords)->contains(fn ($word) => str_contains($title, Str::lower($word)));
    }

    /**
     * "THE CONCEPT OF LIMITS" => "The Concept Of Limits"; mixed-case titles are kept as typed.
     * The channel's "Lesson 5| Indices || Part 1|||" separators become "Lesson 5 – Indices – Part 1".
     */
    private function tidyTitle(string $title): string
    {
        $title = trim(preg_replace('/\s+/', ' ', $title));
        $title = trim(preg_replace('/\s*\|+\s*/', ' – ', $title), ' –');

        return mb_strtoupper($title) === $title && preg_match('/[A-Z]{4}/', $title) ? Str::title(Str::lower($title)) : $title;
    }
}
