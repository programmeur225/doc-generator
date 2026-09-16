@extends('layouts.admin')

@section('title', 'Modifier pays')

@section('content')
    <div class="breadcrumb-custom">
        <a href="{{ route('admin.countries.index') }}" class="text-decoration-none">Pays</a> > Modifier
    </div>
    <h1 class="page-title mb-4">Modifier {{ $country->name }}</h1>

    <div class="card-item" style="max-width: 560px;">
        <form action="{{ route('admin.countries.update', $country) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nom du pays</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $country->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Code ISO</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $country->code) }}"
                       maxlength="5" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Icône / drapeau (optionnel)</label>
                <input type="text" name="flag_icon" class="form-control"
                       value="{{ old('flag_icon', $country->flag_icon) }}">
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                       {{ old('is_active', $country->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Pays actif (visible côté utilisateur)</label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent">Enregistrer les modifications</button>
                <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
