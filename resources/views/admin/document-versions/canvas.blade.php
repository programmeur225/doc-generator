@extends('layouts.admin')

@section('title', 'Éditeur de Champs')

@section('content')
<style>
    .content-wrapper { padding: 0 !important; }
    .canvas-topbar {
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px 32px; background: #fff; border-bottom: 1px solid #E2E8F0;
    }
    .page-tabs a {
        margin-right: 16px; color: #64748B; text-decoration: none; font-weight: 500; padding-bottom: 4px;
    }
    .page-tabs a.active { color: #1E3A8A; border-bottom: 2px solid #1E3A8A; }
    .canvas-body { display: flex; height: calc(100vh - 65px); }
    .canvas-stage {
        flex: 1; background: #F1F5F9; background-image: radial-gradient(#CBD5E1 1px, transparent 1px);
        background-size: 16px 16px; display: flex; align-items: center; justify-content: center;
        overflow: auto; padding: 40px;
    }
    .canvas-panel {
        width: 340px; background: #fff; border-left: 1px solid #E2E8F0; padding: 24px; overflow-y: auto;
    }
    .canvas-bottombar {
        display: flex; justify-content: flex-end; gap: 10px;
        padding: 16px 32px; background: #fff; border-top: 1px solid #E2E8F0;
    }
    #canvas-wrapper { box-shadow: 0 4px 16px rgba(0,0,0,0.08); background: #fff; }
    .field-empty-msg { color: #94A3B8; font-size: 0.875rem; }
</style>

<div class="canvas-topbar">
    <div>
        <strong>Éditeur de Champs</strong>
        <span class="text-muted small ms-2">{{ $documentVersion->documentType->name }} — v{{ $documentVersion->version_number }}</span>
    </div>
    <div class="page-tabs">
        @foreach ($documentVersion->pages as $p)
            <a href="{{ route('admin.document-versions.pages.canvas', [$documentVersion, $p]) }}"
               class="{{ $p->id === $page->id ? 'active' : '' }}">Page {{ $p->page_number }}</a>
        @endforeach
    </div>
    <div>
        <button id="btn-add-field" class="btn btn-accent btn-sm">+ Ajouter un champ</button>
        <button type="button" id="btn-compare" class="btn btn-outline-primary btn-sm">
    🔍 Aperçu réel (avec texte)
</button>
    </div>
</div>

<div class="canvas-body">
    <div class="canvas-stage">
        <div id="canvas-wrapper">
            <canvas id="editor-canvas"></canvas>
        </div>
    </div>

    <div class="canvas-panel">
        <h6 class="mb-1">Propriétés du champ</h6>
        <p class="text-muted small mb-3">Édition des métadonnées</p>

        <div id="no-selection" class="field-empty-msg">
            Sélectionne un champ sur le document, ou clique sur "+ Ajouter un champ".
        </div>

        <form id="field-form" style="display:none;">
            <input type="hidden" id="f-id">

            <div class="mb-3">
                <label class="form-label small">Clé variable</label>
                <input type="text" id="f-key" class="form-control form-control-sm">
            </div>

            <div class="mb-3">
                <label class="form-label small">Libellé affiché</label>
                <input type="text" id="f-label" class="form-control form-control-sm">
            </div>

            <div class="mb-3">
                <label class="form-label small">Type de donnée</label>
                <select id="f-type" class="form-select form-select-sm">
                    <option value="text">Texte court</option>
                    <option value="textarea">Texte long</option>
                    <option value="number">Nombre</option>
                    <option value="date">Date</option>
                    <option value="select">Liste déroulante</option>
                    <option value="checkbox">Case à cocher</option>
                </select>
            </div>

            <div class="mb-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="f-auto-font" checked>
                    <label class="form-check-label small" for="f-auto-font">
                        Taille auto (selon la hauteur de la zone)
                    </label>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small">Taille (pt)</label>
                    <input type="number" id="f-font-size" class="form-control form-control-sm" min="6" max="72">
                    <div id="f-font-size-hint" class="form-text small text-muted" style="display:none;">
                        Calculée : <span id="f-font-size-auto">—</span> pt
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label small">Couleur texte</label>
                    <input type="color" id="f-font-color" class="form-control form-control-sm form-control-color w-100">
                </div>
            </div>

            {{-- Couleur de fond de la zone (masque le texte original du template) --}}
            <div class="mb-3">
                <label class="form-label small">Couleur de fond (zone)</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="color" id="f-bg-color" class="form-control form-control-sm form-control-color"
                           value="#FFFFFF" style="width: 48px; flex-shrink: 0;">
                    <input type="text" id="f-bg-color-hex" class="form-control form-control-sm"
                           value="#FFFFFF" maxlength="7" placeholder="#FFFFFF" style="font-family: monospace;">
                    <button type="button" id="btn-eyedropper" class="btn btn-outline-secondary btn-sm"
                            title="Pipette : prélever une couleur sur le document">
                        💉
                    </button>
                </div>
                <div class="form-text small text-muted">
                    Utilise la pipette pour prélever la couleur exacte du template.
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small d-block">Alignement</label>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="align" id="al-left" value="left" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="al-left">G</label>

                    <input type="radio" class="btn-check" name="align" id="al-center" value="center" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="al-center">C</label>

                    <input type="radio" class="btn-check" name="align" id="al-right" value="right" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="al-right">D</label>
                </div>
            </div>

            <hr>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="f-required">
                <label class="form-check-label small" for="f-required">Champ obligatoire</label>
            </div>

            <button type="button" id="btn-delete-field" class="btn btn-outline-danger btn-sm w-100">
                Supprimer ce champ
            </button>
        </form>
    </div>
</div>

<div class="canvas-bottombar">
    <span id="save-status" class="text-muted small align-self-center me-3"></span>
    <a href="{{ route('admin.document-versions.edit', $documentVersion) }}" class="btn btn-outline-secondary btn-sm">
        Retour aux pages
    </a>
    <form action="{{ route('admin.document-versions.publish', $documentVersion) }}" method="POST"
          onsubmit="return confirm('Publier cette version ? Elle ne pourra plus être modifiée ensuite.');">
        @csrf
        <button type="submit" class="btn btn-accent btn-sm">🔒 Publier cette version</button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<script>
(function () {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const IMAGE_URL = "{{ Storage::url($page->image_path) }}";
    const IMAGE_WIDTH = {{ $page->image_width }};
    const IMAGE_HEIGHT = {{ $page->image_height }};
    const PAGE_ID = {{ $page->id }};
    const STORE_URL = "{{ route('admin.variables.store', $page) }}";
    const EXISTING_VARIABLES = @json($variables);

    // Le canvas est affiché à taille réduite (max 700px de large) mais
    // toutes les coordonnées sont converties en % pour rester indépendantes du zoom.
    const MAX_DISPLAY_WIDTH = 700;
    const scale = Math.min(1, MAX_DISPLAY_WIDTH / IMAGE_WIDTH);
    const displayWidth = IMAGE_WIDTH * scale;
    const displayHeight = IMAGE_HEIGHT * scale;

    const canvas = new fabric.Canvas('editor-canvas', {
        width: displayWidth,
        height: displayHeight,
        selection: false,
    });

    fabric.Image.fromURL(IMAGE_URL, function (img) {
        img.scaleToWidth(displayWidth);
        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
    });

    function makeFieldGroup(variable) {
        const left = (variable.position_x / 100) * displayWidth;
        const top = (variable.position_y / 100) * displayHeight;
        const width = (variable.box_width / 100) * displayWidth;
        const height = (variable.box_height / 100) * displayHeight;

        // Le rectangle définit la vraie zone de contenu.
        // On ne met plus le label en négatif pour éviter de fausser
        // getScaledWidth / getScaledHeight du groupe.
        const rect = new fabric.Rect({
            width: width,
            height: height,
            fill: 'rgba(249, 115, 22, 0.08)',
            stroke: '#F97316',
            strokeDashArray: [4, 3],
            strokeWidth: 1.5,
            rx: 2, ry: 2,
            originX: 'left',
            originY: 'top',
        });

        // Label placé à l'intérieur du rectangle (haut gauche)
        // pour que le bounding-box du groupe = la zone réelle.
        const label = new fabric.Text(variable.key || 'champ', {
            fontSize: Math.min(11, Math.max(8, height * 0.35)),
            fill: '#F97316',
            top: 2,
            left: 4,
            fontFamily: 'Inter, sans-serif',
            originX: 'left',
            originY: 'top',
        });

        const group = new fabric.Group([rect, label], {
            left: left,
            top: top,
            originX: 'left',
            originY: 'top',
            hasRotatingPoint: false,
            lockRotation: true,
            subTargetCheck: false,
        });

        group.data = { variableId: variable.id };
        group.setControlsVisibility({ mtr: false });

        return group;
    }

    EXISTING_VARIABLES.forEach(v => canvas.add(makeFieldGroup(v)));
    canvas.renderAll();

    // --- Sélection : remplir le panneau de droite ---
    const panel = document.getElementById('field-form');
    const emptyMsg = document.getElementById('no-selection');

    function showPanelFor(variable) {
        emptyMsg.style.display = 'none';
        panel.style.display = 'block';
        document.getElementById('f-id').value = variable.id;
        document.getElementById('f-key').value = variable.key;
        document.getElementById('f-label').value = variable.label;
        document.getElementById('f-type').value = variable.type;
        document.getElementById('f-font-size').value = variable.font_size || 12;
        document.getElementById('f-font-color').value = variable.font_color || '#000000';
        document.getElementById('f-required').checked = !!variable.is_required;
        document.getElementById('al-' + (variable.text_align || 'left')).checked = true;

        // Couleur de fond de la zone
        const bg = variable.background_color || '#FFFFFF';
        document.getElementById('f-bg-color').value = bg;
        document.getElementById('f-bg-color-hex').value = bg;

        // Taille auto
        const auto = variable.auto_font_size !== false; // true par défaut
        document.getElementById('f-auto-font').checked = auto;
        updateFontSizeUI(auto, variable);
    }

    /** Active/désactive le champ taille manuelle + affiche l'estimation auto */
    function updateFontSizeUI(auto, variable) {
        const input = document.getElementById('f-font-size');
        const hint = document.getElementById('f-font-size-hint');
        const autoSpan = document.getElementById('f-font-size-auto');
        input.disabled = auto;
        hint.style.display = auto ? 'block' : 'none';

        if (auto && variable) {
            // Estimation basée sur la hauteur de la zone (même formule que le PDF)
            // box_height est en % de l'image → on convertit en pt comme le rendu
            // Ici on affiche juste une estimation visuelle proportionnelle
            const boxH = variable.box_height || 5;
            // approximation : on ne connaît pas image_height ici en pt, on montre un ratio
            // L'estimation exacte se fait au rendu. On affiche ~ hauteur relative.
            const est = Math.max(6, Math.min(72, (boxH / 100) * (IMAGE_HEIGHT * 0.75) * 0.62));
            autoSpan.textContent = est.toFixed(1);
        }
    }

    function hidePanel() {
        panel.style.display = 'none';
        emptyMsg.style.display = 'block';
    }

    let currentVariable = null;

    canvas.on('selection:created', e => selectObject(e.selected[0]));
    canvas.on('selection:updated', e => selectObject(e.selected[0]));
    canvas.on('selection:cleared', hidePanel);

    function selectObject(obj) {
        const localVar = EXISTING_VARIABLES.find(v => v.id === obj.data.variableId);
        if (localVar) {
            currentVariable = localVar;
            showPanelFor(localVar);
        }
    }

    // --- Déplacement / redimensionnement -> sauvegarde position ---
    // On calcule à partir du rectangle (premier enfant) pour être
    // indépendant d'éventuels labels ou offsets.
    canvas.on('object:modified', function (e) {
        const obj = e.target;
        if (!obj.data || !obj.data.variableId) return;

        // Position du groupe (origine top-left)
        let positionX = (obj.left / displayWidth) * 100;
        let positionY = (obj.top / displayHeight) * 100;

        // Dimensions : on préfère le rectangle interne s'il existe
        let boxWidth, boxHeight;
        const rect = (obj._objects && obj._objects[0]) ? obj._objects[0] : null;

        if (rect && typeof rect.width === 'number') {
            // Largeur/hauteur du rect multipliées par le scale du groupe
            boxWidth  = (rect.width  * (obj.scaleX || 1) / displayWidth)  * 100;
            boxHeight = (rect.height * (obj.scaleY || 1) / displayHeight) * 100;
        } else {
            // Fallback sur le bounding box du groupe
            boxWidth  = (obj.getScaledWidth()  / displayWidth)  * 100;
            boxHeight = (obj.getScaledHeight() / displayHeight) * 100;
        }

        // Clamp pour rester dans [0, 100]
        positionX = Math.max(0, Math.min(100, positionX));
        positionY = Math.max(0, Math.min(100, positionY));
        boxWidth  = Math.max(0.5, Math.min(100, boxWidth));
        boxHeight = Math.max(0.5, Math.min(100, boxHeight));

        saveVariable(obj.data.variableId, {
            position_x: positionX.toFixed(3),
            position_y: positionY.toFixed(3),
            box_width:  boxWidth.toFixed(3),
            box_height: boxHeight.toFixed(3),
        });

        // Met à jour le cache local + l'estimation de taille auto
        if (currentVariable && currentVariable.id === obj.data.variableId) {
            currentVariable.box_width = boxWidth;
            currentVariable.box_height = boxHeight;
            currentVariable.position_x = positionX;
            currentVariable.position_y = positionY;
            if (currentVariable.auto_font_size !== false) {
                updateFontSizeUI(true, currentVariable);
            }
        }
    });

    // --- Ajouter un champ ---
   let placementMode = false;
const addBtn = document.getElementById('btn-add-field');

addBtn.addEventListener('click', function () {
    if (placementMode) return;
    placementMode = true;
    canvas.defaultCursor = 'crosshair';
    addBtn.textContent = 'Clique sur le document…';
});

canvas.on('mouse:down', function (opt) {
    if (!placementMode) return;
    placementMode = false;
    canvas.defaultCursor = 'default';
    addBtn.textContent = '+ Ajouter un champ';

    const pointer = canvas.getPointer(opt.e);
    const positionX = Math.max(0, Math.min(100, (pointer.x / displayWidth) * 100));
    const positionY = Math.max(0, Math.min(100, (pointer.y / displayHeight) * 100));

    const key = prompt('Clé de la variable (ex: nom_complet) :');
    if (!key) return;
    const label = prompt('Libellé affiché (ex: Nom complet) :', key);
    if (!label) return;

    fetch(STORE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        body: JSON.stringify({
            key, label,
            position_x: positionX.toFixed(3),
            position_y: positionY.toFixed(3),
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.errors) { alert(Object.values(data.errors).flat().join('\n')); return; }
        const variable = data.variable;
        EXISTING_VARIABLES.push(variable);
        const group = makeFieldGroup(variable);
        canvas.add(group);
        canvas.setActiveObject(group);
        selectObject(group);
        canvas.renderAll();
    });
});

    const PREVIEW_URL = "{{ route('admin.document-pages.preview', [$documentVersion, $page]) }}";
const originalBgUrl = "{{ Storage::disk('public')->url($page->image_path) }}";
let comparing = false;

document.getElementById('btn-compare').addEventListener('click', function () {
    comparing = !comparing;
    this.textContent = comparing ? '↩️ Revenir au template' : '🔍 Aperçu réel (avec texte)';

    const url = comparing ? (PREVIEW_URL + '?t=' + Date.now()) : originalBgUrl;

    fabric.Image.fromURL(url, function (img) {
        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
            scaleX: displayWidth / img.width,
            scaleY: displayHeight / img.height,
        });
    }, { crossOrigin: 'anonymous' });
});

    // --- Mise à jour des propriétés depuis le panneau ---
    function saveVariable(id, payload) {
        const status = document.getElementById('save-status');
        status.textContent = 'Enregistrement...';

        fetch(`/admin/variables/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            if (data.variable) {
                const idx = EXISTING_VARIABLES.findIndex(v => v.id === data.variable.id);
                if (idx !== -1) EXISTING_VARIABLES[idx] = data.variable;
                status.textContent = 'Enregistré ✓';
                setTimeout(() => status.textContent = '', 1500);
            }
        });
    }

    ['f-key', 'f-label', 'f-type', 'f-font-size', 'f-font-color', 'f-required'].forEach(id => {
        document.getElementById(id).addEventListener('change', function () {
            if (!currentVariable) return;
            saveVariable(currentVariable.id, {
                key: document.getElementById('f-key').value,
                label: document.getElementById('f-label').value,
                type: document.getElementById('f-type').value,
                font_size: document.getElementById('f-font-size').value,
                font_color: document.getElementById('f-font-color').value,
                is_required: document.getElementById('f-required').checked,
            });
            // Met à jour le label affiché sur le canvas
            const obj = canvas.getActiveObject();
            if (obj && obj._objects && obj._objects[1]) {
                obj._objects[1].set('text', document.getElementById('f-key').value);
                canvas.renderAll();
            }
        });
    });

    document.querySelectorAll('input[name="align"]').forEach(radio => {
        radio.addEventListener('change', function () {
            if (!currentVariable) return;
            saveVariable(currentVariable.id, { text_align: this.value });
        });
    });

    // Taille auto on/off
    document.getElementById('f-auto-font').addEventListener('change', function () {
        if (!currentVariable) return;
        const auto = this.checked;
        saveVariable(currentVariable.id, { auto_font_size: auto });
        currentVariable.auto_font_size = auto;
        updateFontSizeUI(auto, currentVariable);
    });

    // --- Couleur de fond de la zone ---
    function applyBgColor(hex) {
        if (!/^#[0-9A-Fa-f]{6}$/.test(hex)) return;
        document.getElementById('f-bg-color').value = hex;
        document.getElementById('f-bg-color-hex').value = hex.toUpperCase();
        if (currentVariable) {
            saveVariable(currentVariable.id, { background_color: hex.toUpperCase() });
            // Met à jour le cache local
            currentVariable.background_color = hex.toUpperCase();
        }
    }

    document.getElementById('f-bg-color').addEventListener('input', function () {
        applyBgColor(this.value);
    });

    document.getElementById('f-bg-color-hex').addEventListener('change', function () {
        let hex = this.value.trim();
        if (!hex.startsWith('#')) hex = '#' + hex;
        if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
            applyBgColor(hex);
        } else {
            this.value = document.getElementById('f-bg-color').value;
        }
    });

    // Pipette (EyeDropper API — Chrome, Edge, Opera)
    const eyeBtn = document.getElementById('btn-eyedropper');
    if (window.EyeDropper) {
        eyeBtn.addEventListener('click', async function () {
            try {
                const eyeDropper = new EyeDropper();
                const result = await eyeDropper.open();
                applyBgColor(result.sRGBHex);
            } catch (err) {
                // Annulé par l'utilisateur ou erreur
                if (err.name !== 'AbortError') {
                    console.warn('Pipette:', err);
                }
            }
        });
    } else {
        eyeBtn.disabled = true;
        eyeBtn.title = 'Pipette non supportée par ce navigateur (utilise Chrome / Edge)';
        eyeBtn.style.opacity = '0.5';
    }

    document.getElementById('btn-delete-field').addEventListener('click', function () {
        if (!currentVariable) return;
        if (!confirm('Supprimer ce champ ?')) return;

        fetch(`/admin/variables/${currentVariable.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        })
        .then(() => {
            canvas.remove(canvas.getActiveObject());
            const idx = EXISTING_VARIABLES.findIndex(v => v.id === currentVariable.id);
            if (idx !== -1) EXISTING_VARIABLES.splice(idx, 1);
            currentVariable = null;
            hidePanel();
        });
    });
})();
</script>
@endsection
