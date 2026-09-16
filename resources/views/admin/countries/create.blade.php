@extends('layouts.admin')

@section('title', 'Nouveau pays')

@section('content')
    <div class="breadcrumb-custom">
        <a href="{{ route('admin.countries.index') }}" class="text-decoration-none">Pays</a> > Nouveau
    </div>
    <h1 class="page-title mb-4">Ajouter un pays</h1>

    <div class="card-item" style="max-width: 560px;">
        <form action="{{ route('admin.countries.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nom du pays</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                       placeholder="Ex: Côte d'Ivoire" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Code ISO</label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}"
                       placeholder="Ex: CI" maxlength="5" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Icône / drapeau (optionnel)</label>
                <input type="text" name="flag_icon" class="form-control" value="{{ old('flag_icon') }}"
                       placeholder="Emoji ou chemin d'image">
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                <label class="form-check-label" for="is_active">Pays actif (visible côté utilisateur)</label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent">Enregistrer</button>
                <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
