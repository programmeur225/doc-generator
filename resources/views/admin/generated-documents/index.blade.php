@extends('layouts.admin')

@section('title', 'Documents générés')

@section('content')
    <h1 class="page-title mb-4">Documents générés</h1>

    <form method="GET" class="card-item mb-4">
        <div class="row g-3 align-items-end">
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
            <div class="col-md-3">
                <label class="form-label small">Statut</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="generated" {{ request('status') === 'generated' ? 'selected' : '' }}>Généré</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échoué</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Du</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Au</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-dark btn-sm">Filtrer</button>
                <a href="{{ route('admin.generated-documents.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    @forelse ($generatedDocuments as $doc)
        <div class="card-item d-flex justify-content-between align-items-center">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <strong>#{{ $doc->id }} — {{ $doc->documentVersion->documentType->name }}</strong>
                    @if ($doc->status === 'generated')
                        <span class="badge-status badge-published">Généré</span>
                    @elseif ($doc->status === 'failed')
                        <span class="badge-status badge-archived">Échoué</span>
                    @else
                        <span class="badge-status badge-draft">En attente</span>
                    @endif
                </div>
                <div class="text-muted small">
                    🌍 {{ $doc->documentVersion->documentType->country->name }}
                    &nbsp;·&nbsp; 👤 {{ $doc->user?->name ?? 'Anonyme' }}
                    &nbsp;·&nbsp; 📅 {{ $doc->created_at->translatedFormat('d M Y à H:i') }}
                </div>
            </div>
            @if ($doc->status === 'generated')
                <a href="{{ route('generate.download', $doc) }}" class="btn btn-outline-secondary btn-sm">⬇ Télécharger</a>
            @endif
        </div>
    @empty
        <div class="text-muted">Aucun document généré pour l'instant.</div>
    @endforelse

    <div class="mt-3">
        {{ $generatedDocuments->links() }}
    </div>
@endsection
