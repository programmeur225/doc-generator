<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $documentTypes = DocumentType::with('country')
            ->withCount('versions')
            ->orderBy('name')
            ->get();

        return view('admin.document-types.index', compact('documentTypes'));
    }

    public function create()
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();

        return view('admin.document-types.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
           'custom_font_regular' => ['nullable', 'file', 'max:2048', function ($attribute, $value, $fail) {
                if ($value && !in_array(strtolower($value->getClientOriginalExtension()), ['ttf', 'otf'])) {
                    $fail('Le fichier doit être un .ttf ou .otf.');
                }
            }],
            'custom_font_bold' => ['nullable', 'file', 'max:2048', function ($attribute, $value, $fail) {
                if ($value && !in_array(strtolower($value->getClientOriginalExtension()), ['ttf', 'otf'])) {
                    $fail('Le fichier doit être un .ttf ou .otf.');
                }
            }],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

       
        if ($request->hasFile('custom_font_regular')) {
            $validated['custom_font_regular_path'] = $request->file('custom_font_regular')
                ->store('fonts/' . Str::slug($validated['name']), 'public');
        }
        if ($request->hasFile('custom_font_bold')) {
            $validated['custom_font_bold_path'] = $request->file('custom_font_bold')
                ->store('fonts/' . Str::slug($validated['name']), 'public');
        }

        DocumentType::create($validated);

        return redirect()
            ->route('admin.document-types.index')
            ->with('success', 'Type de document créé avec succès.');
    }

    public function edit(DocumentType $documentType)
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();

        return view('admin.document-types.edit', compact('documentType', 'countries'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'custom_font_regular' => ['nullable', 'file', 'mimes:ttf,otf', 'max:2048'],
            'custom_font_bold' => ['nullable', 'file', 'mimes:ttf,otf', 'max:2048'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('custom_font_regular')) {
            $validated['custom_font_regular_path'] = $request->file('custom_font_regular')
                ->store('fonts/' . Str::slug($validated['name']), 'public');
        }
        if ($request->hasFile('custom_font_bold')) {
            $validated['custom_font_bold_path'] = $request->file('custom_font_bold')
                ->store('fonts/' . Str::slug($validated['name']), 'public');
        }
        \Illuminate\Support\Arr::except($validated, ['custom_font_regular', 'custom_font_bold']);
        $documentType->update($validated);

        return redirect()
            ->route('admin.document-types.index')
            ->with('success', 'Type de document mis à jour.');
    }

    public function destroy(DocumentType $documentType)
    {
        $documentType->delete();

        return redirect()
            ->route('admin.document-types.index')
            ->with('success', 'Type de document supprimé.');
    }
}
