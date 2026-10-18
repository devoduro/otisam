<?php

namespace App\Services\Library;

use App\Models\Library\ResourceFile;
use App\Models\Setting;
use setasign\Fpdi\Fpdi;
use Throwable;

/**
 * Builds the shortened preview PDF shown to people without full access: the first N pages,
 * stamped "Preview". Only that file is ever sent to them, never the full one with pages hidden.
 *
 * Uses FPDI's free parser, which cannot read PDFs saved with compressed cross-reference streams
 * (common in PDF 1.5+). Those are marked "failed" and an administrator uploads a preview instead.
 */
class PdfPreviewGenerator
{
    public function __construct(private readonly ResourceStorage $storage) {}

    public static function defaultPages(): int
    {
        return max(1, (int) Setting::get('library.preview_pages', config('platform.library.preview_pages')));
    }

    public function generate(ResourceFile $main): void
    {
        $resource = $main->resource;
        $wanted = $resource->preview_pages ?? self::defaultPages();

        try {
            $pdf = new Fpdi;
            // The "PREVIEW" stamp sits near the bottom edge; without this FPDF would start a new page.
            $pdf->SetAutoPageBreak(false);
            $count = $pdf->setSourceFile($this->storage->path($main));
        } catch (Throwable $e) {
            $main->forceFill([
                'preview_status' => 'failed',
                'preview_error' => 'Automatic preview is not possible for this PDF ('.mb_substr($e->getMessage(), 0, 120).'). Upload a preview file instead.',
            ])->save();

            return;
        }

        $main->forceFill(['page_count' => $count])->save();

        // Never let a "preview" be most of the document.
        $pages = min($wanted, intdiv($count, 2));
        if ($pages < 1) {
            $main->forceFill(['preview_status' => 'not_applicable', 'preview_error' => "The document has {$count} page(s), too short for a preview."])->save();
            ResourceFile::where('resource_id', $resource->id)->where('role', ResourceFile::ROLE_PREVIEW)->where('preview_status', 'auto')->update(['is_current' => false]);

            return;
        }

        try {
            for ($i = 1; $i <= $pages; $i++) {
                $template = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($template);

                $pdf->SetFont('Helvetica', 'B', 9);
                $pdf->SetTextColor(19, 41, 75);
                $pdf->SetXY(0, $size['height'] - 8);
                $pdf->Cell($size['width'], 6, 'PREVIEW - page '.$i.' of '.$count.' - '.config('app.name'), 0, 0, 'C');
            }

            $this->storage->storeGeneratedPreview($main, $pdf->Output('S'), $pages);
            $main->forceFill(['preview_status' => 'ready', 'preview_error' => null])->save();
        } catch (Throwable $e) {
            report($e);
            $main->forceFill(['preview_status' => 'failed', 'preview_error' => 'Preview generation failed. Upload a preview file instead.'])->save();
        }
    }
}
