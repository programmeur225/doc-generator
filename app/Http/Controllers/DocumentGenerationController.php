<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DocumentType;
use App\Models\GeneratedDocument;
use App\Services\DocumentRenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentGenerationController extends Controller
{
    public function __construct(protected DocumentRenderService $renderService)
    {
    }

    public function countries()
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();

        return view('public.countries', compact('countries'));
    }

    public function documents(Country $country)
    {
        $documentTypes = $country->documentTypes()
            ->where('is_active', true)
            ->whereHas('versions', fn ($q) => $q->where('status', 'published'))
            ->with(['publishedVersion'])
            ->orderBy('name')
            ->get();

        return view('public.documents', compact('country', 'documentTypes'));
    }

    public function form(Country $country, DocumentType $documentType)
    {
        abort_unless($documentType->country_id === $country->id, 404);

        $version = $documentType->publishedVersion;
        abort_unless($version, 404, "Aucune version publiée pour ce document.");

        $version->load(['pages', 'variables' => fn ($q) => $q->orderBy('display_order')]);

        $previewPage = $version->pages->first();

        return view('public.form', [
            'country' => $country,
            'documentType' => $documentType,
            'version' => $version,
            'previewPage' => $previewPage,
        ]);
    }

    public function submit(Request $request, DocumentType $documentType)
    {
        $version = $documentType->publishedVersion;
        abort_unless($version, 404);

        $rules = [];
        foreach ($version->variables as $variable) {
            $rules[$variable->key] = $variable->validationRule();
        }

        $validated = $request->validate($rules);

        $generatedDocument = GeneratedDocument::create([
            'document_version_id' => $version->id,
            'user_id' => $request->user()?->id,
            'data' => $validated,
            'status' => 'pending',
        ]);

        try {
            $this->renderService->render($generatedDocument);
            $message = 'Votre document a été généré avec succès.';
        } catch (\Throwable $e) {
            $generatedDocument->update(['status' => 'failed']);
            report($e);
            $message = "Une erreur est survenue lors de la génération. L'équipe technique a été notifiée.";
        }

        return redirect()
            ->route('generate.success', $generatedDocument)
            ->with('success', $message);
    }

    public function success(GeneratedDocument $generatedDocument)
    {
        $generatedDocument->load('documentVersion.documentType');

        return view('public.success', compact('generatedDocument'));
    }

    public function preview(GeneratedDocument $generatedDocument)
    {
        abort_unless($generatedDocument->status === 'generated' && $generatedDocument->pdf_path, 404);
        abort_if($generatedDocument->is_paid, 404); // pas besoin d'aperçu si déjà payé
        abort_unless(Storage::disk('public')->exists($generatedDocument->pdf_path), 404);

        $imagick = new \Imagick();
        $imagick->setResolution(100, 100);
        $imagick->readImage(Storage::disk('public')->path($generatedDocument->pdf_path) . '[0]'); // page 1 seulement
        $imagick->setImageFormat('png');
        $imagick->blurImage(0, 8);

        $draw = new \ImagickDraw();
        $draw->setFillColor(new \ImagickPixel('rgba(0,0,0,0.35)'));
        $draw->setFontSize(48);
        $draw->setGravity(\Imagick::GRAVITY_CENTER);
        $draw->annotation(0, 0, 'APERÇU — NON PAYÉ');
        $imagick->rotateImage(new \ImagickPixel('none'), 0);
        $imagick->annotateImage($draw, 0, 0, -30, 'APERÇU — NON PAYÉ');

        $binary = $imagick->getImageBlob();
        $imagick->clear();

        return response($binary, 200, ['Content-Type' => 'image/png']);
    }
    
    /**
     * Téléchargement du PDF généré.
     * Seul le propriétaire (user_id) ou un admin peut télécharger un document
     * rattaché à un compte. Les documents générés anonymement (user_id null)
     * restent accessibles via le lien direct, comme avant.
     */
   public function download(Request $request, GeneratedDocument $generatedDocument, string $format = 'pdf')
{
    $user = $request->user();

    // Non connecté
    if (! $user) {
        if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'auth_required' => true,
                'message'       => 'Connectez-vous pour télécharger ce document.',
                'login_url'     => route('login', [
                    'redirect' => route('generate.download', [$generatedDocument, $format]),
                ]),
                'document_id'   => $generatedDocument->id,
            ], 401);
        }

        return redirect()->guest(
            route('login', [
                'redirect' => route('generate.download', [$generatedDocument, $format]),
            ])
        );
    }

    // Rattacher un doc anonyme au user qui télécharge
    if ($generatedDocument->user_id === null) {
        $generatedDocument->update(['user_id' => $user->id]);
    }

    $isOwner = $generatedDocument->user_id === $user->id;
    $isAdmin = $user->isAdmin();

    if ($generatedDocument->user_id !== null && ! $isOwner && ! $isAdmin) {
        abort(403);
    }
    if (! $generatedDocument->is_paid) {
    if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
        return response()->json([
            'payment_required' => true,
            'pay_url' => route('generate.pay', $generatedDocument),
        ], 402);
    }

    return redirect()
        ->route('generate.success', $generatedDocument)
        ->with('error', 'Vous devez payer avant de télécharger ce document.');
}
    abort_unless($generatedDocument->status === 'generated' && $generatedDocument->pdf_path, 404);
    abort_unless(\Illuminate\Support\Facades\Storage::disk('public')->exists($generatedDocument->pdf_path), 404);

    $documentType = $generatedDocument->documentVersion->documentType;
    $baseName = \Illuminate\Support\Str::slug($documentType->name) . '-' . $generatedDocument->id;

    if ($format === 'pdf') {
        return \Illuminate\Support\Facades\Storage::disk('public')->download(
            $generatedDocument->pdf_path,
            "{$baseName}.pdf"
        );
    }

    return $this->downloadAsImage($generatedDocument, $baseName);
}

protected function downloadAsImage(GeneratedDocument $generatedDocument, string $baseName)
{
    $imagick = new \Imagick();
    $imagick->setResolution(150, 150);
    $imagick->readImage(Storage::disk('public')->path($generatedDocument->pdf_path));

    if ($imagick->getNumberImages() === 1) {
        $imagick->setImageFormat('png');
        $imagick->setImageCompressionQuality(90);
        $binary = $imagick->getImageBlob();
        $imagick->clear();

        return response($binary, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => "attachment; filename=\"{$baseName}.png\"",
        ]);
    }

    // Plusieurs pages -> zip d'images
    $zipPath = tempnam(sys_get_temp_dir(), 'docimg_') . '.zip';
    $zip = new \ZipArchive();
    $zip->open($zipPath, \ZipArchive::CREATE);

    foreach ($imagick as $index => $page) {
        $page->setImageFormat('png');
        $page->setImageCompressionQuality(90);
        $zip->addFromString("{$baseName}-page-" . ($index + 1) . '.png', $page->getImageBlob());
    }
    $imagick->clear();
    $zip->close();

    return response()->download($zipPath, "{$baseName}.zip")->deleteFileAfterSend(true);
}
}
