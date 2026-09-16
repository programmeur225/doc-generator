@extends('layouts.admin')

@section('title', 'Toutes les versions')

@section('content')
    <div class="breadcrumb-custom">Administration</div>
    <h1 class="page-title mb-4">Versions — vue d'ensemble</h1>

    <form method="GET" class="card-item mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Statut</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publiée</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archivée</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-dark btn-sm">Filtrer</button>
                <a href="{{ route('admin.versions.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    @forelse ($versions as $version)
        <div class="card-item d-flex justify-content-between align-items-center {{ $version->status === 'published' ? 'is-published' : '' }}">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <strong>{{ $version->documentType->name }} — v{{ $version->version_number }}</strong>
                    @if ($version->status === 'draft')
                        <span class="badge-status badge-draft">Brouillon</span>
                    @elseif ($version->status === 'published')
                        <span class="badge-status badge-published">Publiée</span>
                    @else
                        <span class="badge-status badge-archived">Archivée</span>
                    @endif
                </div>
                <div class="text-muted small">
                    🌍 {{ $version->documentType->country->name }}
                    &nbsp;·&nbsp; {{ '{}' }} {{ $version->variables_count }} variables
                </div>
            </div>
            <div class="d-flex gap-2">
                @if ($version->isEditable())
                    <a href="{{ route('admin.document-versions.edit', $version) }}" class="btn btn-outline-secondary btn-sm">Modifier</a>
                @else
                    <a href="{{ route('admin.document-versions.show', $version) }}" class="btn btn-primary-dark btn-sm">Voir</a>
                @endif
                <a href="{{ route('admin.document-versions.index', $version->documentType) }}" class="btn btn-outline-secondary btn-sm">
                    Toutes les versions de ce document
                </a>
            </div>
        </div>
    @empty
        <div class="text-muted">Aucune version créée pour l'instant.</div>
    @endforelse

    <div class="mt-3">{{ $versions->links() }}</div>
@endsection
