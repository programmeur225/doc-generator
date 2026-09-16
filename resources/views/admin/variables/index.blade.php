@extends('layouts.admin')

@section('title', 'Variables')

@section('content')
    <div class="breadcrumb-custom">Administration</div>
    <h1 class="page-title mb-4">Variables — vue d'ensemble</h1>

    <form method="GET" class="card-item mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small">Recherche (clé ou libellé)</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Type de document</label>
                <select name="document_type_id" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach ($documentTypes as $documentType)
                        <option value="{{ $documentType->id }}" {{ request('document_type_id') == $documentType->id ? 'selected' : '' }}>
                            {{ $documentType->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-dark btn-sm">Filtrer</button>
                <a href="{{ route('admin.variables.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    @forelse ($variables as $variable)
        <div class="card-item d-flex justify-content-between align-items-center">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <code>{{ $variable->key }}</code>
                    <strong>{{ $variable->label }}</strong>
                    <span class="badge-status badge-draft">{{ $variable->type }}</span>
                    @if ($variable->is_required)
                        <span class="badge-status badge-archived">Obligatoire</span>
                    @endif
                </div>
                <div class="text-muted small mt-1">
                    {{ $variable->documentVersion->documentType->name }}
                    — v{{ $variable->documentVersion->version_number }}
                    ({{ $variable->documentVersion->documentType->country->name }})
                    &nbsp;·&nbsp; Page {{ $variable->documentPage->page_number }}
                </div>
            </div>
            <div>
                @if ($variable->documentVersion->isEditable())
                    <a href="{{ route('admin.document-versions.pages.canvas', [$variable->documentVersion, $variable->documentPage]) }}"
                       class="btn btn-outline-secondary btn-sm">
                        Éditer
                    </a>
                @else
                    <span class="text-muted small">🔒 Verrouillée</span>
                @endif
            </div>
        </div>
    @empty
        <div class="text-muted">Aucune variable ne correspond à ces critères.</div>
    @endforelse

    <div class="mt-3">{{ $variables->links() }}</div>
@endsection
