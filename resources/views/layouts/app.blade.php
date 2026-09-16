<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Models\Setting::get('site_name', 'Générateur de documents'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E3A8A;
            --accent: #F97316;
            --bg: #F8FAFC;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); }
        .navbar-custom {
            background: #fff; border-bottom: 1px solid #E2E8F0; padding: 16px 40px;
        }
        .navbar-custom .brand { font-weight: 700; color: var(--primary); font-size: 1.15rem; }
        .navbar-custom .nav-link { color: #334155; font-weight: 500; }
        .navbar-custom .nav-link.active { color: var(--primary); font-weight: 600; }
        .btn-accent { background: var(--accent); border-color: var(--accent); color: #fff; font-weight: 600; }
        .btn-accent:hover { background: #ea6a0c; border-color: #ea6a0c; color: #fff; }
        .btn-primary-dark { background: var(--primary); border-color: var(--primary); font-weight: 600; }

        /* Stepper */
        .stepper { display: flex; align-items: center; justify-content: center; gap: 0; margin: 32px 0 48px; }
        .step-circle {
            width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; background: #fff; border: 2px solid #CBD5E1; color: #94A3B8;
        }
        .step-circle.done { background: var(--primary); border-color: var(--primary); color: #fff; }
        .step-circle.current { background: var(--primary); border-color: var(--primary); color: #fff; }
        .step-label { font-weight: 600; font-size: 0.875rem; color: #94A3B8; margin-top: 6px; text-align: center; }
        .step-label.active { color: var(--primary); }
        .step-line { height: 2px; background: #E2E8F0; flex: 1; margin: 0 8px; max-width: 160px; }
        .step-line.done { background: var(--primary); }
        .step-wrap { display: flex; flex-direction: column; align-items: center; }

        .country-card, .doc-card {
            border: 1px solid #E2E8F0; border-radius: 12px; padding: 24px; background: #fff;
            cursor: pointer; transition: all 0.15s;
        }
        .country-card:hover, .doc-card:hover { border-color: var(--primary); box-shadow: 0 4px 12px rgba(30,58,138,0.08); }
    </style>
</head>
<body>

@if (\App\Models\Setting::get('maintenance_mode') === '1')
    <div class="alert alert-warning mb-0 text-center rounded-0 py-2">
        🚧 Site en maintenance — certaines fonctionnalités peuvent être temporairement indisponibles.
    </div>
@endif

<nav class="navbar-custom d-flex justify-content-between align-items-center">
    <div class="brand">📜 {{ \App\Models\Setting::get('site_name', 'Juris-Expert') }}</div>
    <div class="d-flex gap-4">
        <a href="{{ route('generate.countries') }}" class="nav-link {{ request()->routeIs('generate.*') ? 'active' : '' }}">Nouveau document</a>
        @auth
            <a href="{{ route('my-documents.index') }}" class="nav-link {{ request()->routeIs('my-documents.*') ? 'active' : '' }}">Mes Dossiers</a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-danger">↩️ Déconnexion</button>
        </form>
        @endauth
    </div>
</nav>

<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>
<footer class="border-top mt-5 py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="text-muted small">
            {{ \App\Models\Setting::get('footer_text', '© ' . date('Y') . ' ' . \App\Models\Setting::get('site_name', 'Juris-Expert')) }}
        </div>
        <div class="d-flex gap-3 small">
            <a href="{{ route('cgu') }}" class="text-muted text-decoration-none">CGU</a>
        </div>
    </div>
</footer>
</body>
</html>
