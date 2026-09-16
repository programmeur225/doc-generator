<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DocAdmin') — Gestion Templates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E3A8A;
            --primary-light: #274690;
            --accent: #F97316;
            --bg: #F8FAFC;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
        }
        /* Sidebar */
        .sidebar {
            background: var(--primary);
            min-height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
        }
        .sidebar .brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 4px;
        }
        .sidebar .brand-sub {
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
            margin-bottom: 32px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 4px;
            font-weight: 500;
            font-size: 0.925rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar .nav-link:hover {
            background: var(--primary-light);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: var(--accent);
            color: #fff;
        }
        .sidebar .nav-bottom {
            margin-top: auto;
        }
        /* Content */
        .content-wrapper {
            margin-left: 260px;
            padding: 32px 40px;
        }
        .breadcrumb-custom {
            color: #64748B;
            font-size: 0.875rem;
            margin-bottom: 4px;
        }
        .page-title {
            font-weight: 700;
            font-size: 1.75rem;
            color: #0F172A;
        }
        .btn-accent {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            font-weight: 600;
        }
        .btn-accent:hover {
            background: #ea6a0c;
            border-color: #ea6a0c;
            color: #fff;
        }
        .btn-primary-dark {
            background: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
        }
        .card-item {
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 20px 24px;
            background: #fff;
            margin-bottom: 16px;
        }
        .card-item.is-published {
            border-left: 4px solid #16A34A;
        }
        .badge-status {
            font-weight: 500;
            font-size: 0.75rem;
            padding: 5px 10px;
            border-radius: 999px;
        }
        .badge-draft { background: #DBEAFE; color: #1D4ED8; }
        .badge-published { background: #DCFCE7; color: #15803D; }
        .badge-archived { background: #F1F5F9; color: #64748B; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">📄 DocAdmin</div>
    <div class="brand-sub">Gestion Templates</div>

    <nav class="nav flex-column">
        <a href="{{ route('admin.countries.index') }}"
           class="nav-link {{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
            🌍 Pays
        </a>
        <a href="{{ route('admin.document-types.index') }}"
           class="nav-link {{ request()->routeIs('admin.document-types.*') ? 'active' : '' }}">
            📄 Types de documents
        </a>
        <a href="{{ route('admin.versions.index') }}" class="nav-link {{ request()->routeIs('admin.document-versions.*') ? 'active' : '' }}">
            🕓 Versions
        </a>
        <a href="{{ route('admin.variables.index') }}" class="nav-link">🧩 Variables</a>
        <a href="{{ route('admin.generated-documents.index') }}"
           class="nav-link {{ request()->routeIs('admin.generated-documents.*') ? 'active' : '' }}">
            📁 Documents générés
        </a>
        <a href="{{ route('admin.users.index') }}" class="nav-link">👥 Utilisateurs</a>
    </nav>

    <div class="nav-bottom">
        <a href="{{ route('admin.settings.edit') }}" class="nav-link">⚙️ Paramètres</a>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">↩️ Déconnexion</button>
        </form>
    </div>
</div>

<div class="content-wrapper">
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

</body>
</html>
