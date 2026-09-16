<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\GeneratedDocument;
use Illuminate\Http\Request;

class GeneratedDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = GeneratedDocument::with(['documentVersion.documentType.country', 'user'])
            ->latest();

        if ($request->filled('document_type_id')) {
            $query->whereHas(
                'documentVersion',
                fn ($q) => $q->where('document_type_id', $request->document_type_id)
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $generatedDocuments = $query->paginate(25)->withQueryString();

        $documentTypes = DocumentType::orderBy('name')->get();

        return view('admin.generated-documents.index', compact('generatedDocuments', 'documentTypes'));
    }
}
