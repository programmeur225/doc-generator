@extends('layouts.app')

@section('title', $documentType->name)

@section('content')
<style>
    .preview-wrapper {
        position: relative;
        display: inline-block;
        max-width: 100%;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }
    .preview-wrapper img { display: block; width: 100%; height: auto; }
    .preview-field {
        position: absolute;
        font-family: 'Inter', sans-serif;
        line-height: 1.2;
        white-space: pre-wrap;
        word-break: break-word;
        color: #94A3B8; /* gris tant que vide */
    }
    .preview-field.filled { color: inherit; }
    .form-section { max-width: 480px; }
</style>

<h1 class="fw-bold mb-1">{{ $documentType->name }}</h1>
<p class="text-muted mb-0">Veuillez remplir les informations requises pour finaliser le document.</p>

<x-stepper :current-step="3" />

<div class="row g-4">
    <!-- Colonne gauche : formulaire -->
    <div class="col-lg-6">
        <div class="form-section">
            <form action="{{ route('generate.submit', $documentType) }}" method="POST" id="doc-form">
                @csrf

                @foreach ($version->variables as $variable)
                    <div class="mb-3">
                        <label class="form-label">
                            {{ $variable->label }}
                            @if ($variable->is_required)<span class="text-danger">*</span>@endif
                        </label>

                        @switch($variable->type)
                            @case('textarea')
                                <textarea
                                    name="{{ $variable->key }}"
                                    class="form-control preview-input"
                                    data-key="{{ $variable->key }}"
                                    rows="3"
                                    {{ $variable->is_required ? 'required' : '' }}
                                >{{ old($variable->key) }}</textarea>
                                @break

                            @case('select')
                                <select
                                    name="{{ $variable->key }}"
                                    class="form-select preview-input"
                                    data-key="{{ $variable->key }}"
                                    {{ $variable->is_required ? 'required' : '' }}
                                >
                                    <option value="">-- Sélectionner --</option>
                                    @foreach (($variable->options ?? []) as $option)
                                        <option value="{{ $option }}" {{ old($variable->key) == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @break

                            @case('checkbox')
                                <div class="form-check">
                                    <input type="checkbox" name="{{ $variable->key }}" value="1"
                                           class="form-check-input preview-input" data-key="{{ $variable->key }}"
                                           {{ old($variable->key) ? 'checked' : '' }}>
                                </div>
                                @break

                            @case('date')
                                <input type="date" name="{{ $variable->key }}"
                                       class="form-control preview-input" data-key="{{ $variable->key }}"
                                       value="{{ old($variable->key) }}"
                                       {{ $variable->is_required ? 'required' : '' }}>
                                @break

                            @case('number')
                                <input type="number" name="{{ $variable->key }}"
                                       class="form-control preview-input" data-key="{{ $variable->key }}"
                                       value="{{ old($variable->key) }}"
                                       {{ $variable->is_required ? 'required' : '' }}>
                                @break

                            @default
                                <input type="text" name="{{ $variable->key }}"
                                       class="form-control preview-input" data-key="{{ $variable->key }}"
                                       value="{{ old($variable->key) }}"
                                       placeholder="{{ $variable->placeholder }}"
                                       {{ $variable->is_required ? 'required' : '' }}>
                        @endswitch
                    </div>
                @endforeach

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-accent">📄 Générer le document</button>
                    <a href="{{ route('generate.documents', $country) }}" class="btn btn-outline-secondary">← Retour</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Colonne droite : aperçu en direct -->
    <div class="col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small">👁 Aperçu en direct</span>
            <span class="badge bg-light text-secondary">Brouillon</span>
        </div>

        @if ($previewPage)
            <div class="preview-wrapper" id="preview-wrapper">
                <img src="{{ Storage::url($previewPage->image_path) }}" alt="Aperçu du document">

                @foreach ($version->variables->where('document_page_id', $previewPage->id) as $variable)
                    <span
                        class="preview-field"
                        id="preview-{{ $variable->key }}"
                        style="
                            left: {{ $variable->position_x }}%;
                            top: {{ $variable->position_y }}%;
                            width: {{ $variable->box_width }}%;
                            font-size: {{ $variable->font_size * 0.75 }}px;
                            color: {{ $variable->font_color }};
                            text-align: {{ $variable->text_align }};
                        "
                    >{{ $variable->placeholder ?? '' }}</span>
                @endforeach
            </div>
        @else
            <div class="text-muted">Aucun aperçu disponible.</div>
        @endif
    </div>
</div>

<script>
    document.querySelectorAll('.preview-input').forEach(function (input) {
        input.addEventListener('input', function () {
            const key = input.dataset.key;
            const previewEl = document.getElementById('preview-' + key);
            if (!previewEl) return;

            let value = input.type === 'checkbox' ? (input.checked ? '✓' : '') : input.value;
            previewEl.textContent = value || '';
            previewEl.classList.toggle('filled', !!value);
        });
    });
</script>
@endsection
