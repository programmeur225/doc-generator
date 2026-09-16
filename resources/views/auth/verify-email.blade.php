@extends('layouts.app')

@section('title', 'Vérifiez votre email')

@section('content')
<div class="text-center py-5">
    <div class="fs-1 mb-3">✉️</div>
    <h1 class="fw-bold mb-2">Vérifiez votre adresse email</h1>
    <p class="text-muted mb-4">
        Un lien de confirmation a été envoyé à {{ auth()->user()->email }}.
        Cliquez dessus pour activer votre compte.
    </p>
    <form action="{{ route('verification.resend') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-primary">Renvoyer l'email</button>
    </form>
</div>
@endsection