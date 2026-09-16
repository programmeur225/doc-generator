@extends('layouts.admin')

@section('title', 'Modifier utilisateur')

@section('content')
    <div class="breadcrumb-custom">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">Utilisateurs</a> > Modifier
    </div>
    <h1 class="page-title mb-4">Modifier {{ $user->name }}</h1>

    <div class="card-item" style="max-width: 560px;">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" class="form-control" placeholder="Laisser vide pour ne pas changer">
            </div>

            <div class="mb-4">
                <label class="form-label">Rôle</label>
                <select name="role" class="form-select">
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Utilisateur</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent">Enregistrer</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
