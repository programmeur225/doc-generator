<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentPage;
use App\Models\DocumentType;
use App\Models\Variable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VariableController extends Controller
{
    /**
     * Vue globale : toutes les variables, tous documents confondus.
     */
    public function index(Request $request)
    {
        $query = Variable::with(['documentVersion.documentType.country', 'documentPage'])
            ->orderBy('key');

        if ($request->filled('document_type_id')) {
            $query->whereHas(
                'documentVersion',
                fn ($q) => $q->where('document_type_id', $request->document_type_id)
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('key', 'like', "%{$search}%")
                ->orWhere('label', 'like', "%{$search}%"));
        }

        $variables = $query->paginate(30)->withQueryString();
        $documentTypes = DocumentType::orderBy('name')->get();

        return view('admin.variables.index', compact('variables', 'documentTypes'));
    }

    /**
     * Crée un nouveau champ avec des valeurs par défaut,
     * positionné au centre de la page. Le canvas se charge
     * ensuite du repositionnement via update().
     */
    public function store(Request $request, DocumentPage $documentPage)
    {
        $version = $documentPage->documentVersion;
        abort_if(! $version->isEditable(), 403, 'Version verrouillée.');

        $validated = $request->validate([
            'key' => [
                'required', 'string', 'max:100', 'alpha_dash',
                Rule::unique('variables', 'key')->where('document_version_id', $version->id),
            ],
            'label' => ['required', 'string', 'max:255'],
            'position_x' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'position_y' => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ]);

        $nextOrder = $version->variables()->max('display_order') + 1;

        $variable = Variable::create([
            'document_version_id' => $version->id,
            'document_page_id' => $documentPage->id,
            'key' => $validated['key'],
            'label' => $validated['label'],
            'type' => 'text',
            'is_required' => true,
            'display_order' => $nextOrder,
            'position_x' => $validated['position_x'] ?? 30.000,
            'position_y' => $validated['position_y'] ?? 30.000,
            'box_width' => 25.000,
            'box_height' => 5.000,
            'font_family' => 'DejaVu Sans',
            'font_size' => 12,
            'font_color' => '#000000',
            'background_color' => '#FFFFFF',
            'auto_font_size' => true,
            'text_align' => 'left',
        ]);

        return response()->json(['variable' => $variable]);
    }

    /**
     * Mise à jour d'un champ : position/taille (drag & drop du canvas)
     * OU métadonnées (formulaire de propriétés à droite).
     */
    public function update(Request $request, Variable $variable)
    {
        abort_if(! $variable->documentVersion->isEditable(), 403, 'Version verrouillée.');

        $validated = $request->validate([
            'key' => [
                'sometimes', 'required', 'string', 'max:100', 'alpha_dash',
                Rule::unique('variables', 'key')
                    ->where('document_version_id', $variable->document_version_id)
                    ->ignore($variable->id),
            ],
            'label' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', Rule::in(['text', 'number', 'date', 'select', 'textarea', 'checkbox'])],
            'options' => ['sometimes', 'nullable', 'array'],
            'is_required' => ['sometimes', 'boolean'],
            'font_size' => ['sometimes', 'integer', 'min:6', 'max:72'],
            'font_color' => ['sometimes', 'string', 'max:7'],
            'background_color' => ['sometimes', 'string', 'max:20'],
            'auto_font_size' => ['sometimes', 'boolean'],
            'text_align' => ['sometimes', Rule::in(['left', 'center', 'right'])],
            'position_x' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'position_y' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'box_width' => ['sometimes', 'numeric', 'min:1', 'max:100'],
            'box_height' => ['sometimes', 'numeric', 'min:1', 'max:100'],
        ]);

        $variable->update($validated);

        return response()->json(['variable' => $variable->fresh()]);
    }

    public function destroy(Variable $variable)
    {
        abort_if(! $variable->documentVersion->isEditable(), 403, 'Version verrouillée.');

        $variable->delete();

        return response()->json(['success' => true]);
    }
}
