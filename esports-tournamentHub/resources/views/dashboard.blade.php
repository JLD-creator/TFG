@extends('layouts.app')

@section('content')
    <div class="glass-card p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h1 class="display-6 fw-bold mb-2">Dashboard</h1>
                <p class="text-muted mb-0">Aqui tienes un panel rapido para moverte por la app sin perderte.</p>
            </div>
            <div class="glass-soft p-3">
                <div class="small text-uppercase text-muted">Usuario actual</div>
                <div class="fs-5 fw-semibold">{{ auth()->user()->name }}</div>
                <div class="text-uppercase small text-muted">{{ auth()->user()->rol }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Modulo</div>
                <h3 class="fw-bold">Equipos</h3>
                <p class="text-muted">
                    @if (auth()->user()->esJugador())
                        Crea tu equipo, unete a uno existente y prepara la inscripcion a torneos.
                    @else
                        Consulta los equipos registrados en la plataforma y su participacion general.
                    @endif
                </p>
                <a href="/equipos" class="dashboard-link">
                    @if (auth()->user()->esJugador())
                        Ir a equipos
                    @else
                        Ver equipos
                    @endif
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Modulo</div>
                <h3 class="fw-bold">Torneos</h3>
                <p class="text-muted">
                    @if (auth()->user()->tieneRol('organizador', 'admin'))
                        Consulta torneos, crea competiciones, genera brackets y registra resultados.
                    @else
                        Consulta torneos disponibles e inscribe tu equipo en las competiciones abiertas.
                    @endif
                </p>
                <a href="/torneos" class="dashboard-link">Ir a torneos</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Cuenta</div>
                <h3 class="fw-bold">Perfil</h3>
                <p class="text-muted">Revisa tus datos personales, tu rol actual y los equipos a los que perteneces.</p>
                <a href="/perfil" class="dashboard-link">Abrir perfil</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4 h-100 metric-card">
                @if (auth()->user()->esAdmin())
                    <div class="small text-uppercase text-muted">Administracion</div>
                    <h3 class="fw-bold">Roles</h3>
                    <p class="text-muted">Gestiona permisos y asigna a cada usuario su funcion dentro de la plataforma.</p>
                    <a href="/admin/usuarios" class="dashboard-link">Gestionar usuarios</a>
                @elseif (auth()->user()->esOrganizador())
                    <div class="small text-uppercase text-muted">Flujo</div>
                    <h3 class="fw-bold">Organizacion</h3>
                    <p class="text-muted">Tu flujo recomendado es: crear torneo, abrir inscripciones, generar bracket y subir resultados.</p>
                    <a href="/torneos/create" class="dashboard-link">Crear torneo</a>
                @else
                    <div class="small text-uppercase text-muted">Flujo</div>
                    <h3 class="fw-bold">Siguiente paso</h3>
                    <p class="text-muted">Tu flujo recomendado es: crear equipo, inscribirte en un torneo y seguir el bracket.</p>
                    <a href="/equipos" class="dashboard-link">Empezar ahora</a>
                @endif
            </div>
        </div>
    </div>
@endsection
