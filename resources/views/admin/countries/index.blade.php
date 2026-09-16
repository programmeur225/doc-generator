@extends('layouts.admin')

@section('title', 'Pays')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="breadcrumb-custom">Administration</div>
            <h1 class="page-title">Pays</h1>
        </div>
        <a href="{{ route('admin.countries.create') }}" class="btn btn-accent">
            + Ajouter un pays
        </a>
    </div>

    @forelse ($countries as $country)
        <div class="card-item d-flex justify-content-between align-items-center">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <strong>{{ $country->name }}</strong>
                    <span class="badge-status badge-draft">{{ $country->code }}</span>
                    @unless ($country->is_active)
                        <span class="badge-status badge-archived">Inactif</span>
                    @endunless
                </div>
                <div class="text-muted small mt-1">
                    {{ $country->document_types_count }} type(s) de document associé(s)
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.countries.edit', $country) }}" class="btn btn-outline-secondary btn-sm">
                    Modifier
                </a>
                <form action="{{ route('admin.countries.destroy', $country) }}" method="POST"
                      onsubmit="return confirm('Supprimer ce pays ? Cette action supprimera aussi ses documents associés.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-muted">Aucun pays enregistré pour le moment.</div>
    @endforelse
@endsection
