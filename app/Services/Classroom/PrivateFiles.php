<?php

namespace App\Services\Classroom;

use App\Services\Library\InspectedUpload;
use App\Services\Library\UploadInspector;
use App\Services\Library\UploadRejected;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Files teachers and students upload (handouts, answers, lesson materials). Each upload goes
 * through the library's UploadInspector (type sniffing, PDF script check, Office macro check,
 * image re-encoding) and is kept on the private library disk; it is only ever served by a
 * controller that has checked who is asking.
 */
class PrivateFiles
{
    public function __construct(private readonly UploadInspector $inspector) {}

    /**
     * @return array{file_path: string, file_name: string, file_mime: string, file_size: int}
     */
    public function store(UploadedFile $file, string $folder, string $field = 'file'): array
    {
        try {
            $upload = $this->inspector->inspect($file);
        } catch (UploadRejected $e) {
            throw ValidationException::withMessages([$field => $e->getMessage()]);
        }

        return $this->put($upload, $folder);
    }

    /** @return array{file_path: string, file_name: string, file_mime: string, file_size: int} */
    private function put(InspectedUpload $upload, string $folder): array
    {
        $path = trim($folder, '/').'/'.now()->format('Y/m').'/'.Str::lower((string) Str::ulid()).'.'.$upload->extension;
        $stream = fopen($upload->path, 'rb');
        Storage::disk('library')->put($path, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }

        return [
            'file_path' => $path,
            'file_name' => Str::limit($upload->originalName, 190, ''),
            'file_mime' => $upload->mime,
            'file_size' => $upload->size,
        ];
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('library')->delete($path);
        }
    }

    public function download(string $path, string $name, string $mime): StreamedResponse
    {
        abort_unless(Storage::disk('library')->exists($path), 404);

        return Storage::disk('library')->download($path, $name, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
