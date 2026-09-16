@extends('layouts.app')

@section('title', 'Générer un document')

@section('content')
    <h1 class="fw-bold mb-1">Générer un document</h1>
    <p class="text-muted mb-0">Sélectionnez la juridiction applicable à votre besoin.</p>

    <x-stepper :current-step="1" />

    <div class="mb-4" style="max-width: 500px;">
        <input type="text" id="country-search" class="form-control form-control-lg"
               placeholder="🔍 Rechercher un pays...">
    </div>

    <div class="row g-3" id="country-list">
        @forelse ($countries as $country)
            <div class="col-md-3 col-6 country-item" data-name="{{ Str::lower($country->name) }}">
                <a href="{{ route('generate.documents', $country) }}" class="text-decoration-none text-dark">
                    <div class="country-card text-center">
                        <div class="fs-1 mb-2">🌍</div>
                        <strong>{{ $country->name }}</strong>
                    </div>
                </a>
            </div>
        @empty
            <p class="text-muted">Aucun pays disponible pour le moment.</p>
        @endforelse
    </div>

    <script>
        document.getElementById('country-search').addEventListener('input', function (e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.country-item').forEach(item => {
                item.style.display = item.dataset.name.includes(term) ? '' : 'none';
            });
        });
    </script>
@endsection
