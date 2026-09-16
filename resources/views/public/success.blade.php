@extends('layouts.app')

@section('title', 'Document généré')

@section('content')
    <div class="text-center py-5">
        @if ($generatedDocument->status === 'generated')
    <div class="fs-1 mb-3">✅</div>
    <h1 class="fw-bold mb-2">Votre document est prêt</h1>
    <p class="text-muted mb-4">
        {{ $generatedDocument->documentVersion->documentType->name }}
        — Référence #{{ $generatedDocument->id }}
    </p>

    @unless ($generatedDocument->is_paid)
        <div class="mb-4">
            <img src="{{ route('generate.preview', $generatedDocument) }}"
                 alt="Aperçu du document"
                 class="img-fluid rounded border"
                 style="max-width: 500px;">
        </div>
        <p class="text-muted mb-3">
            Prix : <strong>{{ number_format($generatedDocument->documentVersion->documentType->price, 0, ',', ' ') }} FCFA</strong>
        </p>
    @endunless

    @guest
        <button type="button" class="btn btn-primary" id="btn-download"
                data-download-url="{{ route('generate.download', $generatedDocument) }}">
            📄 Télécharger
        </button>
    @else
        @if ($generatedDocument->is_paid)
            <a href="{{ route('generate.download', $generatedDocument) }}" class="btn btn-primary" id="btn-download">
                📄 Télécharger
            </a>
        @else
            <form action="{{ route('generate.pay', $generatedDocument) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary" id="btn-download">
                    📄 Télécharger
                </button>
            </form>
        @endif
    @endguest

@elseif ($generatedDocument->status === 'failed')
            <div class="fs-1 mb-3">⚠️</div>
            <h1 class="fw-bold mb-2">La génération a échoué</h1>
            <p class="text-muted mb-4">
                Une erreur technique est survenue. Merci de réessayer ou de contacter le support.
            </p>

        @else
            <div class="fs-1 mb-3">⏳</div>
            <h1 class="fw-bold mb-2">Génération en cours...</h1>
            <p class="text-muted mb-4">Rafraîchissez la page dans quelques instants.</p>
        @endif

        <div class="mt-4">
            <a href="{{ route('generate.countries') }}" class="btn btn-outline-secondary">
                Générer un autre document
            </a>
        </div>
    </div>
    @guest
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="authModalLabel">Connexion requise</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    Pour télécharger votre document, connectez-vous ou créez un compte.
                    Il sera ensuite disponible dans <strong>Mes dossiers</strong>.
                </p>
                <div class="d-grid gap-2">
                    <a href="#" id="auth-login-link" class="btn btn-primary">Se connecter</a>
                    <a href="#" id="auth-register-link" class="btn btn-outline-primary">Créer un compte</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const modalEl = document.getElementById('authModal');
    if (!modalEl) return;
    const modal = new bootstrap.Modal(modalEl);
    const loginLink = document.getElementById('auth-login-link');
    const registerLink = document.getElementById('auth-register-link');

  function openAuthModal(downloadUrl) {
    loginLink.href = @json(route('login')) + '?redirect=' + encodeURIComponent(downloadUrl);
    registerLink.href = @json(route('register')) + '?redirect=' + encodeURIComponent(downloadUrl);
    modal.show();
}

    document.getElementById('btn-download')?.addEventListener('click', function () {
    openAuthModal(this.dataset.downloadUrl);
});
})();
</script>
@endguest
@endsection
