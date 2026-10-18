<?php

namespace App\Services\Library;

/** An upload that passed UploadInspector, ready to be stored. */
final class InspectedUpload
{
    public function __construct(
        public readonly string $path,
        public readonly string $originalName,
        public readonly string $extension,
        public readonly string $mime,
        public readonly int $size,
        public readonly string $sha256,
    ) {}
}
