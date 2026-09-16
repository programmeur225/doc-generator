@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="breadcrumb-custom">Administration</div>
            <h1 class="page-title">Utilisateurs</h1>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-accent">+ Ajouter un utilisateur</a>
    </div>

    @forelse ($users as $user)
        <div class="card-item d-flex justify-content-between align-items-center">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <strong>{{ $user->name }}</strong>
                    @if ($user->role === 'admin')
                        <span class="badge-status badge-published">Admin</span>
                    @else
                        <span class="badge-status badge-draft">Utilisateur</span>
                    @endif
                </div>
                <div class="text-muted small mt-1">
                    {{ $user->email }} &nbsp;·&nbsp; {{ $user->generated_documents_count }} document(s) généré(s)
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary btn-sm">Modifier</a>
                @if ($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm('Supprimer {{ $user->name }} ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="text-muted">Aucun utilisateur enregistré.</div>
    @endforelse
@endsection
