@extends('layouts.app')

@section('title', 'Mes documents')

@section('content')
    <h1 class="fw-bold mb-4">Mes documents</h1>

    @forelse ($generatedDocuments as $doc)
        <div class="doc-card d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <strong>{{ $doc->documentVersion->documentType->name }}</strong>
                    @if ($doc->status === 'generated')
                        <span class="badge bg-success-subtle text-success">Prêt</span>
                    @elseif ($doc->status === 'failed')
                        <span class="badge bg-danger-subtle text-danger">Échoué</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">En cours</span>
                    @endif
                </div>
                <div class="text-muted small">
                    {{ $doc->documentVersion->documentType->country->name }}
                    &nbsp;·&nbsp; {{ $doc->created_at->translatedFormat('d M Y à H:i') }}
                </div>
            </div>

            @if ($doc->status === 'generated')
                <a href="{{ route('generate.download', $doc) }}" class="btn btn-accent btn-sm">⬇ Télécharger</a>
            @elseif ($doc->status === 'failed')
                <a href="{{ route('generate.form', [$doc->documentVersion->documentType->country, $doc->documentVersion->documentType]) }}"
                   class="btn btn-outline-secondary btn-sm">Réessayer</a>
            @endif
        </div>
    @empty
        <p class="text-muted">
            Tu n'as pas encore généré de document.
            <a href="{{ route('generate.countries') }}">Commencer</a>.
        </p>
    @endforelse

    <div class="mt-3">
        {{ $generatedDocuments->links() }}
    </div>
@endsection
