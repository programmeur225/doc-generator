@extends('layouts.admin')

@section('title', 'Types de documents')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="breadcrumb-custom">Administration</div>
            <h1 class="page-title">Types de documents</h1>
        </div>
        <a href="{{ route('admin.document-types.create') }}" class="btn btn-accent">
            + Ajouter un type de document
        </a>
    </div>

    @forelse ($documentTypes as $documentType)
        <div class="card-item d-flex justify-content-between align-items-center">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <strong>{{ $documentType->name }}</strong>
                    <span class="badge-status badge-draft">{{ $documentType->country->name }}</span>
                    @unless ($documentType->is_active)
                        <span class="badge-status badge-archived">Inactif</span>
                    @endunless
                </div>
                <div class="text-muted small mt-1">
                    {{ $documentType->description ?? 'Pas de description' }}
                    · {{ $documentType->versions_count }} version(s)
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.document-versions.index', $documentType) }}" class="btn btn-primary-dark btn-sm">
                    Voir les versions
                </a>
                <a href="{{ route('admin.document-types.edit', $documentType) }}" class="btn btn-outline-secondary btn-sm">
                    Modifier
                </a>
                <form action="{{ route('admin.document-types.destroy', $documentType) }}" method="POST"
                      onsubmit="return confirm('Supprimer ce type de document et toutes ses versions ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-muted">Aucun type de document enregistré pour le moment.</div>
    @endforelse
@endsection
