<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentVersion;
use App\Models\DocumentPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentPageController extends Controller
{
    /**
     * Upload une nouvelle image de page pour une version (brouillon uniquement).
     */
    public function store(Request $request, DocumentVersion $documentVersion)
    {
        abort_if(! $documentVersion->isEditable(), 403, 'Cette version est verrouillée, impossible d\'ajouter une page.');

        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:8192', 'dimensions:min_width=800'],
        ]);

        $file = $request->file('image');

        // Chemin de stockage : documents/{version_id}/page-{n}.ext
        $nextPageNumber = $documentVersion->pages()->max('page_number') + 1;
        $filename = "page-{$nextPageNumber}." . $file->getClientOriginalExtension();
        $path = $file->storeAs("documents/{$documentVersion->id}", $filename, 'public');

        // Récupération des dimensions réelles de l'image
        [$width, $height] = getimagesize($file->getRealPath());

        $page = DocumentPage::create([
            'document_version_id' => $documentVersion->id,
            'page_number' => $nextPageNumber,
            'image_path' => $path,
            'image_width' => $width,
            'image_height' => $height,
        ]);

        return redirect()
            ->route('admin.document-versions.edit', $documentVersion)
            ->with('success', "Page {$page->page_number} ajoutée ({$width}x{$height}px).");
    }

    /**
     * Affiche l'éditeur canvas pour positionner les variables sur cette page.
     */
    public function canvas(DocumentVersion $documentVersion, DocumentPage $documentPage)
    {
        abort_unless($documentPage->document_version_id === $documentVersion->id, 404);

        $documentVersion->load('pages');
        $variables = $documentPage->variables()->orderBy('display_order')->get();

        return view('admin.document-versions.canvas', [
            'documentVersion' => $documentVersion,
            'page' => $documentPage,
            'variables' => $variables,
        ]);
    }

    /**
     * Supprime une page (et son image du disque) — brouillon uniquement.
     */
    public function destroy(DocumentVersion $documentVersion, DocumentPage $documentPage)
    {
        abort_if(! $documentVersion->isEditable(), 403, 'Version verrouillée.');
        abort_unless($documentPage->document_version_id === $documentVersion->id, 404);

        Storage::disk('public')->delete($documentPage->image_path);
        $documentPage->delete();

        return redirect()
            ->route('admin.document-versions.edit', $documentVersion)
            ->with('success', 'Page supprimée.');
    }
    /**
     * Aperçu réel (vrai rendu PDF→image) avec données de test,
     * pour comparer visuellement au template original.
     */
    public function preview(DocumentVersion $documentVersion, DocumentPage $documentPage, \App\Services\DocumentRenderService $renderService)
    {
        abort_unless($documentPage->document_version_id === $documentVersion->id, 404);

        $documentPage->load('variables');

        $sampleData = [];
        foreach ($documentPage->variables as $variable) {
            $sampleData[$variable->key] = $variable->type === 'checkbox'
                ? true
                : ($variable->placeholder ?: $variable->label);
        }

        $binary = $renderService->renderPagePreviewImage($documentPage, $sampleData);

        return response($binary, 200, ['Content-Type' => 'image/png']);
    }
}
