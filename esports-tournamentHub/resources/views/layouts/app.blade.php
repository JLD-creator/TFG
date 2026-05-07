<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSports Torneos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --hub-bg: #081926;
            --hub-surface: rgba(14, 35, 49, 0.9);
            --hub-surface-soft: rgba(21, 48, 66, 0.82);
            --hub-border: rgba(0, 255, 170, 0.16);
            --hub-accent: #00d084;
            --hub-accent-soft: #74f0c7;
            --hub-text: #e5eef7;
            --hub-muted: #c6d3df;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(circle at top, rgba(0, 255, 170, 0.14), transparent 22%),
                linear-gradient(180deg, #081926 0%, #0f2231 100%);
            color: var(--hub-text);
            min-height: 100vh;
        }

        .navbar {
            background: rgba(5, 18, 28, 0.92);
            border-bottom: 1px solid var(--hub-border);
            backdrop-filter: blur(10px);
        }

        .navbar-brand,
        .nav-link,
        .navbar .btn-link {
            color: #ffffff !important;
        }

        .brand {
            font-size: 1.1rem;
            letter-spacing: 0.04em;
        }

        .nav-link.active,
        .nav-link:hover,
        .navbar-brand:hover {
            color: var(--hub-accent-soft) !important;
        }

        .btn-primary {
            background: var(--hub-accent);
            border-color: var(--hub-accent);
            color: #06201a;
            font-weight: 700;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: #00b775;
            border-color: #00b775;
            color: #03150f;
        }

        .btn-outline-light:hover {
            color: #081926;
        }

        .btn-danger {
            font-weight: 700;
        }

        main.container {
            max-width: 1140px;
        }

        .glass-card {
            background: var(--hub-surface);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 1.2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
        }

        .glass-soft {
            background: var(--hub-surface-soft);
            border: 1px solid var(--hub-border);
            border-radius: 1rem;
        }

        .text-muted {
            color: var(--hub-muted) !important;
        }

        .hero-image {
            width: 100%;
            max-width: 430px;
            border-radius: 1.5rem;
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.28);
        }

        .form-control,
        .form-select {
            border-radius: 0.8rem;
            padding: 0.8rem 1rem;
        }

        .round-column {
            min-width: 250px;
        }

        .bracket-card {
            min-width: 240px;
        }

        .metric-card h3 {
            font-size: 2rem;
            margin-bottom: 0.25rem;
        }

        .dashboard-link {
            color: var(--hub-accent-soft);
            font-weight: 700;
            text-decoration: none;
        }

        .dashboard-link:hover {
            color: #ffffff;
        }

        .list-group-item {
            background: transparent;
            color: var(--hub-text);
            border-color: rgba(148, 163, 184, 0.12);
        }

        .alert {
            border-radius: 1rem;
        }

        .alert-hub {
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.16);
        }

        @media (max-width: 768px) {
            main.container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a href="/" class="navbar-brand brand">eSports Tournament Hub</a>
            <button class="navbar-toggler border-0 bg-light-subtle" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <div class="navbar-nav me-auto mb-2 mb-lg-0">
                    <a href="/" class="nav-link">Inicio</a>
                    <a href="/equipos" class="nav-link">Equipos</a>
                    <a href="/torneos" class="nav-link">Torneos</a>
                    <a href="/estadisticas" class="nav-link">Estadisticas</a>
                    @auth
                        <a href="/dashboard" class="nav-link">Dashboard</a>
                        <a href="/perfil" class="nav-link">Mi perfil</a>
                        @if (auth()->user()->esAdmin())
                            <a href="/admin/usuarios" class="nav-link">Usuarios</a>
                        @endif
                    @endauth
                </div>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @auth
                        <div class="text-end">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <div class="small text-uppercase text-muted">{{ auth()->user()->rol }}</div>
                        </div>

                        <form method="POST" action="/logout" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                        </form>
                    @endauth

                    @guest
                        <a href="/login" class="btn btn-outline-light btn-sm">Login</a>
                        <a href="/register" class="btn btn-primary btn-sm">Register</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-lg-5">
        @include('components.flash-messages')
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
