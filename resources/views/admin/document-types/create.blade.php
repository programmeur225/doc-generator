@extends('layouts.admin')

@section('title', 'Nouveau type de document')

@section('content')
    <div class="breadcrumb-custom">
        <a href="{{ route('admin.document-types.index') }}" class="text-decoration-none">Types de documents</a> > Nouveau
    </div>
    <h1 class="page-title mb-4">Ajouter un type de document</h1>

    <div class="card-item" style="max-width: 640px;">
        <form action="{{ route('admin.document-types.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Pays</label>
                <select name="country_id" class="form-select" required>
                    <option value="">-- Sélectionner un pays --</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nom du document</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                       placeholder="Ex: Attestation de travail" required>
                <div class="form-text">Le slug sera généré automatiquement.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="Courte description affichée à l'utilisateur">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Icône (optionnel)</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon') }}"
                       placeholder="Nom d'icône ou emoji">
            </div>
            <div class="mb-3">
                <label class="form-label">Police régulière (.ttf/.otf)</label>
                <input type="file" name="custom_font_regular" class="form-control" accept=".ttf,.otf">
            </div>
            <div class="mb-3">
                <label class="form-label">Police grasse (.ttf/.otf, optionnel)</label>
                <input type="file" name="custom_font_bold" class="form-control" accept=".ttf,.otf">
            </div>
            <div class="form-check mb-4">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                <label class="form-check-label" for="is_active">Actif (visible côté utilisateur)</label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent">Enregistrer</button>
                <a href="{{ route('admin.document-types.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
