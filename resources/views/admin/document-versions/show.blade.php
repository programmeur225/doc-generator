@extends('layouts.admin')

@section('title', 'Aperçu — Version ' . $documentVersion->version_number)

@section('content')
    <div class="breadcrumb-custom">
        <a href="{{ route('admin.document-versions.index', $documentVersion->documentType) }}" class="text-decoration-none">
            Versions — {{ $documentVersion->documentType->name }}
        </a> > Version {{ $documentVersion->version_number }} (aperçu)
    </div>

    <div class="d-flex justify-content-between align-items-start mb-4">
        <h1 class="page-title mb-0">
            Version {{ $documentVersion->version_number }}
            @if ($documentVersion->status === 'published')
                <span class="badge-status badge-published">Publiée</span>
            @else
                <span class="badge-status badge-archived">Archivée</span>
            @endif
        </h1>
        <a href="{{ route('admin.document-versions.index', $documentVersion->documentType) }}" class="btn btn-outline-secondary btn-sm">
            ← Retour aux versions
        </a>
    </div>

    <div class="card-item mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted small">Publiée le</div>
                <strong>{{ $documentVersion->published_at?->translatedFormat('d M Y à H:i') ?? '—' }}</strong>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Créée par</div>
                <strong>{{ $documentVersion->creator?->name ?? '—' }}</strong>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Variables (total)</div>
                <strong>{{ $documentVersion->pages->sum(fn ($p) => $p->variables->count()) }}</strong>
            </div>
        </div>
        @if ($documentVersion->notes)
            <hr>
            <div class="text-muted small mb-1">Notes internes</div>
            <div>{{ $documentVersion->notes }}</div>
        @endif
    </div>

    <h6 class="mb-3">Pages ({{ $documentVersion->pages->count() }})</h6>

    @forelse ($documentVersion->pages as $page)
        <div class="card-item">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <strong>Page {{ $page->page_number }}</strong>
                <span class="text-muted small">
                    {{ $page->image_width }} x {{ $page->image_height }} px · {{ $page->variables->count() }} champ(s)
                </span>
            </div>

            <div style="position: relative; display: inline-block; max-width: 100%;">
                <img src="{{ Storage::url($page->image_path) }}" alt="Page {{ $page->page_number }}"
                     style="max-width: 100%; height: auto; display: block; border: 1px solid #E2E8F0; border-radius: 6px;">

                @foreach ($page->variables as $variable)
                    <div title="{{ $variable->label }} ({{ $variable->key }})" style="
                        position: absolute;
                        left: {{ $variable->position_x }}%;
                        top: {{ $variable->position_y }}%;
                        width: {{ $variable->box_width }}%;
                        height: {{ $variable->box_height }}%;
                        border: 1px dashed #F97316;
                        background: rgba(249, 115, 22, 0.08);
                        font-size: {{ min($variable->font_size, 12) }}px;
                        color: {{ $variable->font_color }};
                        text-align: {{ $variable->text_align }};
                        overflow: hidden;
                        padding: 1px 2px;
                        box-sizing: border-box;
                        pointer-events: none;
                    ">{{ $variable->label }}</div>
                @endforeach
            </div>

            @if ($page->variables->isNotEmpty())
                <table class="table table-sm mt-3">
                    <thead>
                        <tr>
                            <th>Clé</th>
                            <th>Libellé</th>
                            <th>Type</th>
                            <th>Obligatoire</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($page->variables as $variable)
                            <tr>
                                <td><code>{{ $variable->key }}</code></td>
                                <td>{{ $variable->label }}</td>
                                <td>{{ $variable->type }}</td>
                                <td>{{ $variable->is_required ? 'Oui' : 'Non' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @empty
        <div class="text-muted">Aucune page sur cette version.</div>
    @endforelse
@endsection
