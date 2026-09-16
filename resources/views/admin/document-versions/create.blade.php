@extends('layouts.admin')

@section('title', 'Nouvelle version')

@section('content')
    <div class="breadcrumb-custom">
        <a href="{{ route('admin.document-versions.index', $documentType) }}" class="text-decoration-none">
            Versions — {{ $documentType->name }}
        </a> > Nouvelle
    </div>
    <h1 class="page-title mb-4">Créer une nouvelle version</h1>

    <div class="card-item" style="max-width: 560px;">
        <form action="{{ route('admin.document-versions.store', $documentType) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Numéro de version</label>
                <input type="text" name="version_number" class="form-control" value="{{ old('version_number') }}"
                       placeholder="Ex: 1.0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes internes (optionnel)</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Ex: Ajout du champ ville, correction orthographe">{{ old('notes') }}</textarea>
            </div>

            <div class="alert alert-light border small">
                Cette version sera créée en statut <strong>Brouillon</strong>. Tu pourras ensuite
                uploader l'image du document et positionner les champs dans l'éditeur avant de la publier.
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent">Créer le brouillon</button>
                <a href="{{ route('admin.document-versions.index', $documentType) }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
