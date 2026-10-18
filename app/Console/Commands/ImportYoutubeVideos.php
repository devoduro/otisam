<?php

namespace App\Console\Commands;

use App\Services\Videos\YoutubeChannelImporter;
use Illuminate\Console\Command;
use RuntimeException;

class ImportYoutubeVideos extends Command
{
    protected $signature = 'videos:import-youtube
        {channel? : Key from config/platform.php video_channels (default: all)}
        {--limit= : Only look at the latest N uploads}
        {--draft : Import as drafts instead of publishing}';

    protected $description = 'Add new uploads from the configured YouTube channels as video lessons';

    public function handle(YoutubeChannelImporter $importer): int
    {
        $keys = $this->argument('channel') ? [$this->argument('channel')] : array_keys(config('platform.video_channels', []));
        $limit = $this->option('limit') !== null ? max(1, (int) $this->option('limit')) : null;
        $failed = false;

        foreach ($keys as $key) {
            try {
                $result = $importer->import($key, $limit, ! $this->option('draft'));
                $this->info("{$key}: {$result['created']} new, {$result['skipped']} already on the platform ({$result['total']} read via ".($result['source'] === 'api' ? 'the YouTube Data API' : 'the RSS feed, latest uploads only').').');
            } catch (RuntimeException $e) {
                $this->error("{$key}: {$e->getMessage()}");
                $failed = true;
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
