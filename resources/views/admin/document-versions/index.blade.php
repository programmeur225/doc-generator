@extends('layouts.admin')

@section('title', 'Versions — ' . $documentType->name)

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="breadcrumb-custom">
                <a href="{{ route('admin.document-types.index') }}" class="text-decoration-none">Types de documents</a>
                > {{ $documentType->name }}
            </div>
            <h1 class="page-title">Versions</h1>
        </div>
        <a href="{{ route('admin.document-versions.create', $documentType) }}" class="btn btn-accent">
            + Nouvelle version
        </a>
    </div>

    @forelse ($versions as $version)
        <div class="card-item d-flex justify-content-between align-items-center {{ $version->status === 'published' ? 'is-published' : '' }}">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <strong style="font-size: 1.1rem;">Version {{ $version->version_number }}</strong>

                    @if ($version->status === 'draft')
                        <span class="badge-status badge-draft">Brouillon</span>
                    @elseif ($version->status === 'published')
                        <span class="badge-status badge-published">Publiée</span>
                    @else
                        <span class="badge-status badge-archived">Archivée</span>
                    @endif
                </div>
                <div class="text-muted small">
                    📅
                    @if ($version->published_at)
                        Publiée le {{ $version->published_at->translatedFormat('d M Y') }}
                    @else
                        Non publiée
                    @endif
                    &nbsp;·&nbsp; {{ '{}' }} {{ $version->variables_count }} variables
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Dupliquer -->
                <form action="{{ route('admin.document-versions.duplicate', $version) }}" method="POST"
                      class="d-flex gap-1" onsubmit="return promptDuplicate(event, this)">
                    @csrf
                    <input type="hidden" name="version_number" value="">
                    <button type="submit" class="btn btn-outline-secondary btn-sm" title="Dupliquer pour créer une nouvelle version">
                        ⧉
                    </button>
                </form>

                @if ($version->isEditable())
                    <a href="{{ route('admin.document-versions.edit', $version) }}" class="btn btn-outline-secondary btn-sm">
                        Modifier
                    </a>
                    <form action="{{ route('admin.document-versions.publish', $version) }}" method="POST"
                          onsubmit="return confirm('Publier la version {{ $version->version_number }} ? Elle ne pourra plus être modifiée ensuite.');">
                        @csrf
                        <button type="submit" class="btn btn-primary-dark btn-sm">Publier</button>
                    </form>
                @else
                    <button class="btn btn-outline-secondary btn-sm" disabled title="Version verrouillée">
                        🔒 Modifier
                    </button>
                    <a href="{{ route('admin.document-versions.show', $version) }}" class="btn btn-primary-dark btn-sm">Voir</a>
                @endif
            </div>
        </div>
    @empty
        <div class="text-muted">Aucune version créée pour ce document. Commence par en créer une.</div>
    @endforelse

    <script>
        function promptDuplicate(event, form) {
            event.preventDefault();
            const versionNumber = prompt('Numéro de la nouvelle version (ex: 2.1) :');
            if (!versionNumber) return false;
            form.querySelector('input[name="version_number"]').value = versionNumber;
            form.submit();
            return false;
        }
    </script>
@endsection
