<?php

namespace App\Services;

use App\Models\DocumentPage;
use App\Models\GeneratedDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;

class DocumentRenderService
{
    /**
     * Génère le PDF final : une page dompdf par page source (image + variables
     * overlayées en position absolue), fusionnées avec FPDI en un seul PDF.
     */
    public function render(GeneratedDocument $generatedDocument): void
    {
        $version = $generatedDocument->documentVersion()
            ->with(['pages' => fn ($q) => $q->orderBy('page_number'), 'pages.variables'])
            ->firstOrFail();

        abort_if($version->pages->isEmpty(), 422, 'Cette version n\'a aucune page à générer.');

        $data = $generatedDocument->data ?? [];

        $merger = new Fpdi();
        $merger->SetAutoPageBreak(false);

        foreach ($version->pages as $page) {
            $pagePdfBinary = $this->renderPage($page, $data);

            $tmpPath = tempnam(sys_get_temp_dir(), 'docpage_') . '.pdf';
            file_put_contents($tmpPath, $pagePdfBinary);

            $merger->setSourceFile($tmpPath);
            $templateId = $merger->importPage(1);
            $size = $merger->getTemplateSize($templateId);

            $merger->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $merger->useTemplate($templateId);

            unlink($tmpPath);
        }

        $filename = 'generated/' . $generatedDocument->document_version_id . '/' . Str::uuid() . '.pdf';

        Storage::disk('public')->makeDirectory(dirname($filename));
        $merger->Output('F', Storage::disk('public')->path($filename));

        $generatedDocument->update([
            'pdf_path' => $filename,
            'status' => 'generated',
        ]);
    }

    /**
     * Rend une seule page à la taille exacte de l'image source
     * (conversion px -> pt à 96dpi, cohérent avec les % du canvas admin).
     */
      public function renderPage(DocumentPage $page, array $data): string
        {
            $documentType = $page->documentVersion->documentType;

            $widthPt = $page->image_width * 0.75;
            $heightPt = $page->image_height * 0.75;

            $html = view('pdf.page', [
                'imagePath' => Storage::disk('public')->path($page->image_path),
                'variables' => $page->variables,
                'data' => $data,
                'widthPt' => $widthPt,
                'heightPt' => $heightPt,
                'customFontRegular' => $documentType->custom_font_regular_path
                    ? Storage::disk('public')->path($documentType->custom_font_regular_path) : null,
                'customFontBold' => $documentType->custom_font_bold_path
                    ? Storage::disk('public')->path($documentType->custom_font_bold_path) : null,
            ])->render();

            $dompdf = Pdf::loadHTML($html)->setPaper([0, 0, $widthPt, $heightPt]);

            return $dompdf->output();
        }

        /**
     * Génère un PNG d'aperçu d'une page (avec des données de test),
     * en utilisant EXACTEMENT le même pipeline que le PDF final.
     */
    public function renderPagePreviewImage(DocumentPage $page, array $data): string
    {
        $pdfBinary = $this->renderPage($page, $data);

        $imagick = new \Imagick();
        $imagick->setResolution(150, 150);
        $imagick->readImageBlob($pdfBinary);
        $imagick->setImageFormat('png');
        $imagick->setImageCompressionQuality(90);
        $binary = $imagick->getImageBlob();
        $imagick->clear();

        return $binary;
    }
}
