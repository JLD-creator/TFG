@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">{{ $equipo->nombre_equipo }}</h1>
            <p class="text-muted mb-0">Ficha completa del equipo, sus miembros y sus torneos inscritos.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="/equipos/{{ $equipo->id_equipo }}/historial" class="btn btn-outline-info">Ver historial</a>
            <a href="/equipos" class="btn btn-outline-light">Volver a equipos</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-4 h-100">
                <div class="small text-uppercase text-muted">Capitan</div>
                <div class="fs-4 fw-bold">{{ $resumen['capitan'] ?? 'Sin asignar' }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4 h-100">
                <div class="small text-uppercase text-muted">Miembros</div>
                <div class="fs-4 fw-bold">{{ $resumen['miembros'] }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4 h-100">
                <div class="small text-uppercase text-muted">Torneos inscritos</div>
                <div class="fs-4 fw-bold">{{ $resumen['torneos'] }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h2 class="h4 fw-bold mb-3">Miembros del equipo</h2>

                @if ($equipo->usuarios->isEmpty())
                    <div class="alert alert-info mb-0">Este equipo todavia no tiene miembros registrados.</div>
                @else
                    <ul class="list-group">
                        @foreach ($equipo->usuarios as $miembro)
                            <li class="list-group-item d-flex flex-column flex-md-row justify-content-between gap-2 align-items-md-center">
                                <div>
                                    <span class="fw-semibold">{{ $miembro->name }}</span>
                                    <span class="text-muted">({{ $miembro->email }})</span>
                                </div>
                                @if ((int) $equipo->id_capitan === (int) $miembro->id)
                                    <span class="badge text-bg-success">Capitan</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h2 class="h4 fw-bold mb-3">Torneos inscritos</h2>

                @if ($equipo->inscripciones->isEmpty())
                    <div class="alert alert-info mb-0">Este equipo todavia no se ha inscrito en ningun torneo.</div>
                @else
                    <ul class="list-group">
                        @foreach ($equipo->inscripciones as $inscripcion)
                            <li class="list-group-item">
                                <div class="fw-semibold">{{ $inscripcion->torneo->nombre ?? 'Torneo no disponible' }}</div>
                                <div class="text-muted">
                                    {{ $inscripcion->torneo->juego ?? '-' }} |
                                    {{ $inscripcion->torneo->tipo_torneo ?? '-' }} |
                                    Estado: {{ $inscripcion->torneo->estado ?? '-' }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
