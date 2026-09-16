<?php

namespace App\Http\Controllers;

use App\Models\GeneratedDocument;
use Illuminate\Http\Request;

class MyDocumentsController extends Controller
{
    public function index(Request $request)
    {
        $generatedDocuments = GeneratedDocument::with('documentVersion.documentType.country')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('public.my-documents', compact('generatedDocuments'));
    }
}
