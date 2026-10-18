<?php

namespace App\Services\Library;

use App\Jobs\GenerateResourcePreview;
use App\Models\Library\Resource;
use App\Models\Library\ResourceFile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Puts inspected uploads on the private library disk. Replacing a file keeps the old version
 * (not current) for history; only current files are ever served.
 */
class ResourceStorage
{
    public function storeMain(Resource $resource, InspectedUpload $upload, ?User $by): ResourceFile
    {
        $file = $this->store($resource, $upload, ResourceFile::ROLE_MAIN, $by, [
            'preview_status' => $upload->extension === 'pdf' ? 'pending' : 'not_applicable',
        ]);

        // A new main file makes any automatic preview of the old one wrong.
        $this->retireAutomaticPreview($resource);

        if ($file->isPdf()) {
            GenerateResourcePreview::dispatch($file->id)->afterCommit();
        }

        return $file;
    }

    /** An administrator-supplied preview (e.g. when automatic generation is not possible). */
    public function storeManualPreview(Resource $resource, InspectedUpload $upload, ?User $by): ResourceFile
    {
        if ($upload->extension !== 'pdf') {
            throw new UploadRejected('A preview must be a PDF file.');
        }

        $file = $this->store($resource, $upload, ResourceFile::ROLE_PREVIEW, $by, ['preview_status' => 'manual']);
        $resource->mainFile?->forceFill(['preview_status' => 'manual', 'preview_error' => null])->save();

        return $file;
    }

    /** Write raw bytes as the current automatic preview (used by the preview generator). */
    public function storeGeneratedPreview(ResourceFile $main, string $pdfBytes, int $pages): ResourceFile
    {
        $tmp = tempnam(sys_get_temp_dir(), 'pv');
        file_put_contents($tmp, $pdfBytes);

        $upload = new InspectedUpload($tmp, 'preview-'.pathinfo($main->original_name, PATHINFO_FILENAME).'.pdf', 'pdf', 'application/pdf', filesize($tmp), hash_file('sha256', $tmp));
        $preview = $this->store($main->resource, $upload, ResourceFile::ROLE_PREVIEW, null, ['preview_status' => 'auto', 'page_count' => $pages]);
        @unlink($tmp);

        return $preview;
    }

    public function path(ResourceFile $file): string
    {
        return Storage::disk($file->disk)->path($file->path);
    }

    private function retireAutomaticPreview(Resource $resource): void
    {
        ResourceFile::where('resource_id', $resource->id)->where('role', ResourceFile::ROLE_PREVIEW)
            ->where('is_current', true)->where('preview_status', 'auto')->update(['is_current' => false]);
    }

    /** @param array<string, mixed> $extra */
    private function store(Resource $resource, InspectedUpload $upload, string $role, ?User $by, array $extra = []): ResourceFile
    {
        $disk = config('platform.library.disk');
        $ulid = (string) Str::ulid();
        $path = "resources/{$resource->id}/{$role}-{$ulid}.{$upload->extension}";

        $stream = fopen($upload->path, 'rb');
        Storage::disk($disk)->writeStream($path, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }

        return DB::transaction(function () use ($resource, $upload, $role, $by, $extra, $disk, $path, $ulid) {
            $version = (int) ResourceFile::where('resource_id', $resource->id)->where('role', $role)->max('version') + 1;
            ResourceFile::where('resource_id', $resource->id)->where('role', $role)->where('is_current', true)->update(['is_current' => false]);

            $file = new ResourceFile;
            $file->forceFill(array_merge([
                'uuid' => $ulid,
                'resource_id' => $resource->id,
                'role' => $role,
                'disk' => $disk,
                'path' => $path,
                'original_name' => $upload->originalName,
                'mime' => $upload->mime,
                'extension' => $upload->extension,
                'size' => $upload->size,
                'sha256' => $upload->sha256,
                'version' => $version,
                'is_current' => true,
                'created_by' => $by?->id,
            ], $extra))->save();

            return $file;
        });
    }
}
