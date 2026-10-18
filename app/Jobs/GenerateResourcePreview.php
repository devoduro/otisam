<?php

namespace App\Jobs;

use App\Models\Library\ResourceFile;
use App\Services\Library\PdfPreviewGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateResourcePreview implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(public readonly int $mainFileId) {}

    public function handle(PdfPreviewGenerator $generator): void
    {
        $main = ResourceFile::with('resource')->find($this->mainFileId);

        // Replaced since the job was queued, or an administrator supplied their own preview.
        if (! $main || ! $main->is_current || ! $main->resource || $main->preview_status === 'manual') {
            return;
        }

        $generator->generate($main);
    }
}
