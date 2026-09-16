<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use Illuminate\Http\Request;

class DocumentVersionController extends Controller
{
    public function index(DocumentType $documentType)
    {
        $versions = $documentType->versions()
            ->withCount('variables')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.document-versions.index', compact('documentType', 'versions'));
    }

    /**
     * Vue globale : toutes les versions, tous documents confondus.
     */
    public function all(Request $request)
    {
        $query = DocumentVersion::with(['documentType.country'])
            ->withCount('variables')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $versions = $query->paginate(25)->withQueryString();

        return view('admin.document-versions.all', compact('versions'));
    }

    public function create(DocumentType $documentType)
    {
        return view('admin.document-versions.create', compact('documentType'));
    }

    public function store(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'version_number' => [
                'required',
                'string',
                'max:20',
                'unique:document_versions,version_number,NULL,id,document_type_id,' . $documentType->id,
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $version = $documentType->versions()->create([
            'version_number' => $validated['version_number'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'draft',
            'created_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('admin.document-versions.index', $documentType)
            ->with('success', "Version {$version->version_number} créée en brouillon.");
    }

    /**
     * Aperçu en lecture seule d'une version publiée ou archivée.
     */
    public function show(DocumentVersion $documentVersion)
    {
        $documentVersion->load([
            'documentType',
            'creator',
            'pages.variables' => fn ($q) => $q->orderBy('display_order'),
        ]);

        return view('admin.document-versions.show', compact('documentVersion'));
    }

    public function edit(DocumentVersion $documentVersion)
    {
        abort_if(! $documentVersion->isEditable(), 403, 'Cette version est publiée ou archivée, elle ne peut plus être modifiée.');

        return view('admin.document-versions.edit', compact('documentVersion'));
    }

    public function update(Request $request, DocumentVersion $documentVersion)
    {
        abort_if(! $documentVersion->isEditable(), 403, 'Version non modifiable.');

        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $documentVersion->update($validated);

        return redirect()
            ->route('admin.document-versions.index', $documentVersion->documentType)
            ->with('success', 'Version mise à jour.');
    }

    public function publish(DocumentVersion $documentVersion)
    {
        abort_if($documentVersion->status !== 'draft', 400, 'Seul un brouillon peut être publié.');

        DocumentVersion::where('document_type_id', $documentVersion->document_type_id)
            ->where('status', 'published')
            ->update(['status' => 'archived']);

        $documentVersion->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.document-versions.index', $documentVersion->documentType)
            ->with('success', "Version {$documentVersion->version_number} publiée.");
    }

    public function duplicate(Request $request, DocumentVersion $documentVersion)
    {
        $validated = $request->validate([
            'version_number' => [
                'required',
                'string',
                'max:20',
                'unique:document_versions,version_number,NULL,id,document_type_id,' . $documentVersion->document_type_id,
            ],
        ]);

        $newVersion = $documentVersion->duplicate($validated['version_number']);

        return redirect()
            ->route('admin.document-versions.index', $documentVersion->documentType)
            ->with('success', "Version {$newVersion->version_number} créée à partir de {$documentVersion->version_number}.");
    }

    public function destroy(DocumentVersion $documentVersion)
    {
        abort_if($documentVersion->status === 'published', 403, 'Impossible de supprimer une version publiée.');

        $documentType = $documentVersion->documentType;
        $documentVersion->delete();

        return redirect()
            ->route('admin.document-versions.index', $documentType)
            ->with('success', 'Version supprimée.');
    }
}
