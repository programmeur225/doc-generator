@extends('layouts.admin')

@section('title', 'Paramètres')

@section('content')
    <div class="breadcrumb-custom">Administration</div>
    <h1 class="page-title mb-4">Paramètres</h1>

    <div class="card-item" style="max-width: 640px;">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nom du site</label>
                <input type="text" name="site_name" class="form-control"
                       value="{{ old('site_name', $settings['site_name'] ?? 'Juris-Expert') }}" required>
                <div class="form-text">Affiché dans la navbar publique et le titre des pages.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email support</label>
                <input type="email" name="support_email" class="form-control"
                       value="{{ old('support_email', $settings['support_email'] ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Texte de pied de page (optionnel)</label>
                <textarea name="footer_text" class="form-control" rows="2">{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea>
            </div>

            <div class="form-check form-switch mb-4">
                <input type="checkbox" name="maintenance_mode" value="1" class="form-check-input" id="maintenance_mode"
                       {{ old('maintenance_mode', ($settings['maintenance_mode'] ?? '0') === '1') ? 'checked' : '' }}>
                <label class="form-check-label" for="maintenance_mode">
                    Mode maintenance (affiche une bannière d'avertissement côté public)
                </label>
            </div>

            <button type="submit" class="btn btn-accent">Enregistrer les paramètres</button>
        </form>
    </div>
@endsection
