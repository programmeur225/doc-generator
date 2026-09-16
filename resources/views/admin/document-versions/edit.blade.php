@extends('layouts.admin')

@section('title', 'Modifier version ' . $documentVersion->version_number)

@section('content')
    <div class="breadcrumb-custom">
        <a href="{{ route('admin.document-versions.index', $documentVersion->documentType) }}" class="text-decoration-none">
            Versions — {{ $documentVersion->documentType->name }}
        </a> > Version {{ $documentVersion->version_number }}
    </div>
    <h1 class="page-title mb-4">
        Version {{ $documentVersion->version_number }}
        <span class="badge-status badge-draft">Brouillon</span>
    </h1>

    <div class="row g-4">
        <!-- Colonne gauche : notes de version -->
        <div class="col-lg-4">
            <div class="card-item">
                <h6 class="mb-3">Notes internes</h6>
                <form action="{{ route('admin.document-versions.update', $documentVersion) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <textarea name="notes" class="form-control mb-3" rows="4">{{ old('notes', $documentVersion->notes) }}</textarea>
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Enregistrer les notes</button>
                </form>
            </div>

            <div class="card-item">
                <h6 class="mb-3">Ajouter une page</h6>
                <form action="{{ route('admin.document-versions.pages.store', $documentVersion) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="image" class="form-control mb-2" accept=".jpg,.jpeg,.png" required>
                    <div class="form-text mb-3">JPG ou PNG, 800px de large minimum, 8 Mo max.</div>
                    <button type="submit" class="btn btn-accent btn-sm w-100">+ Uploader la page</button>
                </form>
            </div>
        </div>

        <!-- Colonne droite : pages existantes -->
        <div class="col-lg-8">
            <h6 class="mb-3">Pages du document ({{ $documentVersion->pages->count() }})</h6>

            @forelse ($documentVersion->pages as $page)
                <div class="card-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ Storage::url($page->image_path) }}" alt="Page {{ $page->page_number }}"
                             style="width: 90px; height: 120px; object-fit: cover; border-radius: 6px; border: 1px solid #E2E8F0;">
                        <div>
                            <strong>Page {{ $page->page_number }}</strong>
                            <div class="text-muted small">{{ $page->image_width }} x {{ $page->image_height }} px</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.document-versions.pages.canvas', [$documentVersion, $page]) }}"
                           class="btn btn-primary-dark btn-sm">
                            Éditer les champs
                        </a>
                        <form action="{{ route('admin.document-versions.pages.destroy', [$documentVersion, $page]) }}"
                              method="POST" onsubmit="return confirm('Supprimer cette page ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-muted">Aucune page uploadée. Ajoute une image à gauche pour commencer.</div>
            @endforelse
        </div>
    </div>
@endsection
