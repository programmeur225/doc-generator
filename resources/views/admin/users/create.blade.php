@extends('layouts.admin')

@section('title', 'Nouvel utilisateur')

@section('content')
    <div class="breadcrumb-custom">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">Utilisateurs</a> > Nouveau
    </div>
    <h1 class="page-title mb-4">Ajouter un utilisateur</h1>

    <div class="card-item" style="max-width: 560px;">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Rôle</label>
                <select name="role" class="form-select">
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Utilisateur</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent">Créer</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
