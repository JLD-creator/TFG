@extends('layouts.app')

@section('content')
    <div class="glass-card p-4 p-lg-5 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h1 class="display-6 fw-bold mb-2">Dashboard</h1>
                <p class="text-muted mb-0">Tu centro de control para seguir equipos, torneos y actividad reciente de la plataforma.</p>
            </div>
            <div class="glass-soft p-3">
                <div class="small text-uppercase text-muted">Usuario actual</div>
                <div class="fs-5 fw-semibold">{{ $user->name }}</div>
                <div class="text-uppercase small text-muted">{{ $user->rol }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Equipos</div>
                <h3 class="fw-bold">{{ $metricas['equipos_total'] }}</h3>
                <p class="text-muted mb-0">Equipos registrados actualmente en la plataforma.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Torneos</div>
                <h3 class="fw-bold">{{ $metricas['torneos_total'] }}</h3>
                <p class="text-muted mb-0">Torneos creados entre abiertos, en curso y finalizados.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Abiertos</div>
                <h3 class="fw-bold">{{ $metricas['torneos_abiertos'] }}</h3>
                <p class="text-muted mb-0">Torneos disponibles para seguir inscribiendo equipos.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Pendientes</div>
                <h3 class="fw-bold">{{ $metricas['partidos_pendientes'] }}</h3>
                <p class="text-muted mb-0">Partidos que todavía no tienen un resultado registrado.</p>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="glass-card p-4 h-100">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <div class="small text-uppercase text-muted">
                            @if ($user->esAdmin())
                                Administración
                            @elseif ($user->esOrganizador())
                                Organización
                            @else
                                Jugador
                            @endif
                        </div>
                        <h2 class="h3 fw-bold mb-1">Panel según tu rol</h2>
                        <p class="text-muted mb-0">
                            @if ($user->esAdmin())
                                Vista de supervisión global de usuarios, roles y estado general de la plataforma.
                            @elseif ($user->esOrganizador())
                                Vista operativa para preparar torneos, controlar jornadas y seguir resultados pendientes.
                            @else
                                Vista personal para seguir tus equipos, invitaciones y torneos en los que participas.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row g-4">
                    @if ($user->esAdmin())
                        <div class="col-md-6">
                            <div class="glass-soft p-4 h-100">
                                <div class="small text-uppercase text-muted">Usuarios</div>
                                <div class="fs-2 fw-bold">{{ $metricasAdmin['usuarios_total'] }}</div>
                                <p class="text-muted mb-3">Total de cuentas registradas en el sistema.</p>
                                <a href="/admin/usuarios" class="dashboard-link">Gestionar usuarios y roles</a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="glass-soft p-4 h-100">
                                <div class="small text-uppercase text-muted">Distribución</div>
                                <div class="fw-semibold mb-2">Admins: {{ $metricasAdmin['admins_total'] }}</div>
                                <div class="fw-semibold mb-2">Organizadores: {{ $metricasAdmin['organizadores_total'] }}</div>
                                <div class="fw-semibold">Jugadores: {{ $metricasAdmin['jugadores_total'] }}</div>
                            </div>
                        </div>
                    @elseif ($user->esOrganizador())
                        <div class="col-md-4">
                            <div class="glass-soft p-4 h-100">
                                <div class="small text-uppercase text-muted">Abiertos</div>
                                <div class="fs-2 fw-bold">{{ $metricasOrganizador['torneos_abiertos'] }}</div>
                                <p class="text-muted mb-0">Listos para seguir aceptando equipos.</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="glass-soft p-4 h-100">
                                <div class="small text-uppercase text-muted">En curso</div>
                                <div class="fs-2 fw-bold">{{ $metricasOrganizador['torneos_en_curso'] }}</div>
                                <p class="text-muted mb-0">Competiciones activas en este momento.</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="glass-soft p-4 h-100">
                                <div class="small text-uppercase text-muted">Por resolver</div>
                                <div class="fs-2 fw-bold">{{ $metricasOrganizador['partidos_pendientes'] }}</div>
                                <p class="text-muted mb-0">Partidos a la espera de resultado.</p>
                            </div>
                        </div>
                    @else
                        <div class="col-md-4">
                            <div class="glass-soft p-4 h-100">
                                <div class="small text-uppercase text-muted">Mis equipos</div>
                                <div class="fs-2 fw-bold">{{ $metricasJugador['mis_equipos'] }}</div>
                                <p class="text-muted mb-0">Equipos en los que participas ahora mismo.</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="glass-soft p-4 h-100">
                                <div class="small text-uppercase text-muted">Invitaciones</div>
                                <div class="fs-2 fw-bold">{{ $metricasJugador['invitaciones_pendientes'] }}</div>
                                <p class="text-muted mb-0">Invitaciones pendientes de aceptar o rechazar.</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="glass-soft p-4 h-100">
                                <div class="small text-uppercase text-muted">Mis torneos</div>
                                <div class="fs-2 fw-bold">{{ $metricasJugador['mis_torneos'] }}</div>
                                <p class="text-muted mb-0">Torneos donde participa alguno de tus equipos.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="glass-card p-4 h-100">
                <div class="small text-uppercase text-muted">Actividad reciente</div>
                <h2 class="h3 fw-bold mb-3">Últimos torneos</h2>

                @if ($torneosRecientes->isEmpty())
                    <div class="alert alert-info mb-0">Todavía no se han creado torneos en la plataforma.</div>
                @else
                    <div class="list-group">
                        @foreach ($torneosRecientes as $torneo)
                            <div class="list-group-item">
                                <div class="fw-semibold">{{ $torneo->nombre }}</div>
                                <div class="text-muted">
                                    {{ $torneo->juego }} | {{ $torneo->tipo_torneo }} | {{ $torneo->estado }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Módulo</div>
                <h3 class="fw-bold">Equipos</h3>
                <p class="text-muted">
                    @if ($user->esJugador())
                        Crea tu equipo, únete a uno existente y prepara la inscripción a torneos.
                    @else
                        Consulta los equipos registrados en la plataforma y su participación general.
                    @endif
                </p>
                <a href="/equipos" class="dashboard-link">{{ $user->esJugador() ? 'Ir a equipos' : 'Ver equipos' }}</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Módulo</div>
                <h3 class="fw-bold">Torneos</h3>
                <p class="text-muted">
                    @if ($user->tieneRol('organizador', 'admin'))
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
    </div>
@endsection
