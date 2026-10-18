<?php

namespace App\Services\Library;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Checks an uploaded document before it is stored (BLUEPRINT §10.1, "Uploads"):
 * - extension allowlist, and the real MIME type (finfo) must match the extension;
 * - size limit;
 * - PDFs must really be PDFs and must not contain scripts, launch actions or embedded files;
 * - Office files must be genuine OOXML packages without macros or ActiveX;
 * - images are decoded and re-encoded with GD, dropping metadata and anything hidden after the image data.
 *
 * Shared hosting rarely offers an antivirus daemon. A ClamAV check can be added here when the
 * host provides one.
 */
class UploadInspector
{
    /** Tokens that make a PDF able to run code or carry other files. */
    private const PDF_FORBIDDEN = ['/JavaScript', '/JS ', '/JS(', '/JS<', '/Launch', '/EmbeddedFile', '/RichMedia', '/XFA'];

    /**
     * @throws UploadRejected
     */
    public function inspect(UploadedFile $file): InspectedUpload
    {
        if (! $file->isValid()) {
            throw new UploadRejected('The upload did not complete. Please try again.');
        }

        $allowed = config('platform.library.allowed');
        $extension = Str::lower($file->getClientOriginalExtension());

        if (! array_key_exists($extension, $allowed)) {
            throw new UploadRejected('Files of type .'.($extension ?: '?').' are not allowed. Allowed: '.implode(', ', array_keys($allowed)).'.');
        }

        $maxBytes = config('platform.library.max_upload_mb') * 1024 * 1024;
        if ($file->getSize() > $maxBytes || $file->getSize() === 0) {
            throw new UploadRejected('Files must be between 1 byte and '.config('platform.library.max_upload_mb').' MB.');
        }

        $path = $file->getRealPath();
        // An antivirus on the server may quarantine a suspicious upload before we read it.
        $mime = $path && is_readable($path) ? @(new \finfo(FILEINFO_MIME_TYPE))->file($path) : false;
        if ($mime === false) {
            throw new UploadRejected('The file could not be read. It may have been blocked by a virus scanner.');
        }

        if (! in_array($mime, $allowed[$extension], true)) {
            throw new UploadRejected("The file's contents do not match its .{$extension} extension.");
        }

        match ($extension) {
            'pdf' => $this->checkPdf($path),
            'docx', 'pptx', 'xlsx' => $this->checkOfficePackage($path, $extension),
            default => null,
        };

        if (in_array($extension, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            $path = $this->reencodeImage($path, $extension);
            $mime = $allowed[$extension][0];
        }

        $canonical = $extension === 'jpeg' ? 'jpg' : $extension;
        $mime = in_array($canonical, ['docx', 'pptx', 'xlsx'], true) ? $allowed[$canonical][0] : $mime;

        return new InspectedUpload(
            path: $path,
            originalName: Str::limit(basename($file->getClientOriginalName()), 250, ''),
            extension: $canonical,
            mime: $mime,
            size: filesize($path),
            sha256: hash_file('sha256', $path),
        );
    }

    private function checkPdf(string $path): void
    {
        $handle = fopen($path, 'rb');
        $head = fread($handle, 1024);
        fclose($handle);

        if (! str_contains((string) $head, '%PDF-')) {
            throw new UploadRejected('This is not a valid PDF file.');
        }

        // Scan the raw bytes in chunks (overlapping so a token split across chunks is still found).
        // This is a heuristic: a token hidden inside a compressed object stream is not visible here,
        // which is one more reason files are only ever served with nosniff/attachment headers and
        // rendered by PDF.js (which does not run PDF scripts).
        $handle = fopen($path, 'rb');
        $carry = '';
        while (! feof($handle)) {
            $chunk = $carry.fread($handle, 1024 * 1024);
            foreach (self::PDF_FORBIDDEN as $token) {
                if (str_contains($chunk, $token)) {
                    fclose($handle);

                    throw new UploadRejected('This PDF contains scripts, attachments or interactive content, which are not allowed. Please re-save it as a plain PDF (e.g. "Print to PDF").');
                }
            }
            $carry = substr($chunk, -32);
        }
        fclose($handle);
    }

    private function checkOfficePackage(string $path, string $extension): void
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new UploadRejected("This is not a valid .{$extension} file.");
        }

        $expected = ['docx' => 'word/', 'pptx' => 'ppt/', 'xlsx' => 'xl/'][$extension];
        $hasContentTypes = false;
        $hasMainPart = false;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            $lower = Str::lower($name);

            // printerSettings*.bin is normal in Office files; macros, ActiveX and embedded OLE objects are not allowed.
            $embeddedProgram = str_contains($lower, '/embeddings/') && (str_ends_with($lower, '.bin') || str_ends_with($lower, '.exe'));
            if (str_contains($lower, 'vbaproject') || str_contains($lower, 'activex') || $embeddedProgram || str_contains($lower, '..')) {
                $zip->close();

                throw new UploadRejected('Office files with macros, ActiveX controls or embedded programs are not allowed.');
            }
            $hasContentTypes = $hasContentTypes || $name === '[Content_Types].xml';
            $hasMainPart = $hasMainPart || str_starts_with($name, $expected);
        }
        $zip->close();

        if (! $hasContentTypes || ! $hasMainPart) {
            throw new UploadRejected("This is not a valid .{$extension} file.");
        }
    }

    private function reencodeImage(string $path, string $extension): string
    {
        if (@getimagesize($path) === false) {
            throw new UploadRejected('This image could not be read.');
        }

        $image = match ($extension) {
            'png' => @imagecreatefrompng($path),
            'webp' => @imagecreatefromwebp($path),
            default => @imagecreatefromjpeg($path),
        };

        if ($image === false) {
            throw new UploadRejected('This image could not be read.');
        }

        $out = tempnam(sys_get_temp_dir(), 'img');
        $ok = match ($extension) {
            'png' => (function () use ($image, $out) {
                imagesavealpha($image, true);

                return imagepng($image, $out, 6);
            })(),
            'webp' => imagewebp($image, $out, 85),
            default => imagejpeg($image, $out, 85),
        };
        imagedestroy($image);

        if (! $ok) {
            throw new UploadRejected('This image could not be processed.');
        }

        return $out;
    }
}
