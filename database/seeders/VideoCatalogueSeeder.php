<?php

namespace Database\Seeders;

use App\Services\Videos\YoutubeChannelImporter;
use Illuminate\Database\Seeder;

/**
 * Adds every upload listed in data/videos/{channel}.php as a video lesson, filed by topic. Only adds
 * what is missing and only tags videos that have no topic, so it is safe to run again.
 */
class VideoCatalogueSeeder extends Seeder
{
    public function run(YoutubeChannelImporter $importer): void
    {
        foreach (glob(__DIR__.'/data/videos/*.php') ?: [] as $file) {
            $data = require $file;
            // Newest first. Shorts go ahead of the full lessons so that, in a topic's teaching
            // order (oldest first), the lessons come before the one-minute clips.
            $entries = array_merge(
                array_map(fn ($short) => $short + ['short' => true], $data['shorts'] ?? []),
                $data['live'] ?? [],
                $data['videos'] ?? [],
            );

            $result = $importer->importCatalogue($data['channel'], $entries);
            $this->command?->info("{$data['channel']}: {$result['created']} added, {$result['retagged']} filed under a topic, {$result['total']} listed.");
        }
    }
}
