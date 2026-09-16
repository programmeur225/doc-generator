@extends('layouts.app')

@section('title', 'Sélection du document')

@section('content')
    <h1 class="fw-bold mb-1">Sélection du Document</h1>
    <p class="text-muted mb-0">Pays sélectionné : <strong>{{ $country->name }}</strong></p>

    <x-stepper :current-step="2" />

    <div class="row g-3">
        @forelse ($documentTypes as $documentType)
            <div class="col-md-6">
                <a href="{{ route('generate.form', [$country, $documentType]) }}" class="text-decoration-none text-dark">
                    <div class="doc-card h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="fs-2">📄</div>
                            <span class="badge bg-light text-secondary">
                                v{{ $documentType->publishedVersion->version_number }}
                            </span>
                        </div>
                        <h5 class="fw-bold">{{ $documentType->name }}</h5>
                        <p class="text-muted small mb-0">{{ $documentType->description }}</p>
                    </div>
                </a>
            </div>
        @empty
            <p class="text-muted">
                Aucun document disponible pour {{ $country->name }} pour le moment.
                <a href="{{ route('generate.countries') }}">Choisir un autre pays</a>.
            </p>
        @endforelse
    </div>

    <div class="mt-4">
        <a href="{{ route('generate.countries') }}" class="btn btn-outline-secondary">← Changer de pays</a>
    </div>
@endsection
