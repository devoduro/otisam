<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Library\Resource;
use App\Models\Library\ResourceFile;
use App\Services\Library\ResourceAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * The only way library files leave the server. Each request re-checks ResourceAccess, so a link
 * copied from someone else's browser is useless without the same access.
 */
class ResourceFileController extends Controller
{
    public function __construct(private readonly ResourceAccess $access) {}

    /** The shortened preview, shown to everyone who can see the resource. */
    public function preview(Request $request, Resource $resource): Response
    {
        abort_unless($this->access->canSee($request->user(), $resource), 404);

        $file = $resource->previewFile()->first();
        abort_unless($file, 404);

        $this->log($request, $resource, $file, 'view_preview');

        return $this->send($file, 'inline');
    }

    /** The full document, inline (read in the browser). */
    public function view(Request $request, Resource $resource): Response
    {
        abort_unless($this->access->canSee($request->user(), $resource), 404);
        abort_unless($this->access->canViewFull($request->user(), $resource), 403);

        $file = $resource->mainFile()->first();
        abort_unless($file, 404);

        $this->log($request, $resource, $file, 'view_full');

        return $this->send($file, 'inline');
    }

    public function download(Request $request, Resource $resource): Response
    {
        abort_unless($this->access->canSee($request->user(), $resource), 404);

        if (! $request->user() && ! config('platform.library.guest_downloads')) {
            return redirect()->guest(route('login'));
        }
        abort_unless($this->access->canDownload($request->user(), $resource), 403);

        $file = $resource->mainFile()->first();
        abort_unless($file, 404);

        $this->log($request, $resource, $file, 'download');
        Resource::whereKey($resource->id)->increment('download_count');

        return $this->send($file, 'attachment');
    }

    private function send(ResourceFile $file, string $disposition): Response
    {
        $name = Str::slug(pathinfo($file->original_name, PATHINFO_FILENAME)) ?: 'document';

        return Storage::disk($file->disk)->response($file->path, "{$name}.{$file->extension}", [
            'Content-Type' => $file->mime,
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Robots-Tag' => 'noindex, nofollow',
            // If a browser ever opens the file directly, it may not run scripts in it.
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; img-src data:; sandbox",
        ], $disposition);
    }

    private function log(Request $request, Resource $resource, ResourceFile $file, string $action): void
    {
        // Byte-range requests from PDF viewers would otherwise log one row per chunk.
        if ($request->headers->has('Range') && ! str_starts_with((string) $request->header('Range'), 'bytes=0-')) {
            return;
        }

        DB::table('resource_access_logs')->insert([
            'resource_id' => $resource->id,
            'resource_file_id' => $file->id,
            'user_id' => $request->user()?->id,
            'action' => $action,
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
    }
}
