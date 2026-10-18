<?php

namespace App\Services\Videos;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Lists the public uploads of a YouTube channel.
 *
 * With YOUTUBE_API_KEY set, the YouTube Data API returns every upload (with durations). Without a
 * key, the channel's public RSS feed is used, which only carries the latest 15 uploads: enough to
 * keep up with new videos once the back catalogue has been imported.
 */
class YoutubeChannelFeed
{
    private const API = 'https://www.googleapis.com/youtube/v3';

    /**
     * @return list<array{id: string, title: string, description: ?string, published_at: ?CarbonImmutable, duration_seconds: ?int}>
     */
    public function uploads(string $channelId, ?int $limit = null): array
    {
        if (! preg_match('/^UC[A-Za-z0-9_-]{22}$/', $channelId)) {
            throw new RuntimeException('That is not a YouTube channel id (it should start with "UC").');
        }

        return filled(config('services.youtube.key'))
            ? $this->fromApi($channelId, $limit)
            : $this->fromRss($channelId, $limit);
    }

    public function usesApi(): bool
    {
        return filled(config('services.youtube.key'));
    }

    private function fromApi(string $channelId, ?int $limit): array
    {
        $key = config('services.youtube.key');
        // Every channel's uploads playlist is its id with "UC" replaced by "UU".
        $playlist = 'UU'.substr($channelId, 2);
        $videos = [];
        $pageToken = null;

        do {
            $page = Http::timeout(20)->retry(2, 500, throw: false)->get(self::API.'/playlistItems', array_filter([
                'part' => 'snippet,contentDetails,status',
                'playlistId' => $playlist,
                'maxResults' => 50,
                'pageToken' => $pageToken,
                'key' => $key,
            ]));
            if (! $page->successful()) {
                throw new RuntimeException('YouTube Data API error: '.($page->json('error.message') ?? 'HTTP '.$page->status()));
            }

            $batch = [];
            foreach ($page->json('items', []) as $item) {
                $id = $item['contentDetails']['videoId'] ?? null;
                if (! $id || ($item['status']['privacyStatus'] ?? 'public') !== 'public') {
                    continue;
                }
                $batch[$id] = [
                    'id' => $id,
                    'title' => trim($item['snippet']['title'] ?? ''),
                    'description' => trim($item['snippet']['description'] ?? '') ?: null,
                    'published_at' => isset($item['contentDetails']['videoPublishedAt']) ? CarbonImmutable::parse($item['contentDetails']['videoPublishedAt']) : null,
                    'duration_seconds' => null,
                ];
            }

            if ($batch) {
                $details = Http::timeout(20)->retry(2, 500, throw: false)->get(self::API.'/videos', [
                    'part' => 'contentDetails', 'id' => implode(',', array_keys($batch)), 'key' => $key,
                ]);
                foreach ($details->json('items', []) as $detail) {
                    if (isset($batch[$detail['id']])) {
                        $batch[$detail['id']]['duration_seconds'] = self::isoDurationToSeconds($detail['contentDetails']['duration'] ?? null);
                    }
                }
                array_push($videos, ...array_values($batch));
            }

            $pageToken = $page->json('nextPageToken');
        } while ($pageToken && ($limit === null || count($videos) < $limit));

        return $limit === null ? $videos : array_slice($videos, 0, $limit);
    }

    private function fromRss(string $channelId, ?int $limit): array
    {
        $response = Http::timeout(20)->retry(2, 500, throw: false)->get('https://www.youtube.com/feeds/videos.xml', ['channel_id' => $channelId]);
        if (! $response->successful()) {
            throw new RuntimeException('Could not read the channel feed (HTTP '.$response->status().').');
        }

        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->body(), options: LIBXML_NONET);
        libxml_use_internal_errors($previous);
        if ($xml === false) {
            throw new RuntimeException('The channel feed was not valid XML.');
        }

        $videos = [];
        foreach ($xml->entry as $entry) {
            $id = (string) $entry->children('yt', true)->videoId;
            if (! preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) {
                continue;
            }
            $media = $entry->children('media', true)->group;
            $videos[] = [
                'id' => $id,
                'title' => trim((string) $entry->title),
                'description' => trim((string) ($media?->description ?? '')) ?: null,
                'published_at' => (string) $entry->published ? CarbonImmutable::parse((string) $entry->published) : null,
                'duration_seconds' => null,
            ];
        }

        return $limit === null ? $videos : array_slice($videos, 0, $limit);
    }

    /** "PT1H2M3S" => 3723. */
    public static function isoDurationToSeconds(?string $iso): ?int
    {
        if (! $iso || ! preg_match('/^P(?:(\d+)D)?T?(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?$/', $iso, $m)) {
            return null;
        }

        return ((int) ($m[1] ?? 0)) * 86400 + ((int) ($m[2] ?? 0)) * 3600 + ((int) ($m[3] ?? 0)) * 60 + (int) ($m[4] ?? 0);
    }
}
