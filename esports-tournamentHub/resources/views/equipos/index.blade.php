@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Equipos</h1>
            <p class="text-muted mb-0">Gestiona equipos, envia invitaciones y unete a uno existente.</p>
        </div>
        @if (auth()->user()->esJugador())
            <a href="/equipos/create" class="btn btn-primary">Crear equipo</a>
        @endif
    </div>

    @if (auth()->user()->esJugador() && $invitacionesPendientes->isNotEmpty())
        <div class="glass-card p-4 mb-4">
            <h2 class="h4 fw-bold mb-3">Invitaciones pendientes</h2>

            @foreach ($invitacionesPendientes as $invitacion)
                <div class="glass-soft p-3 mb-3">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <div class="fw-semibold">{{ $invitacion->equipo->nombre_equipo }}</div>
                            <div class="text-muted">
                                Invitacion enviada por {{ $invitacion->invitador->name }} ({{ $invitacion->invitador->email }})
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <form method="POST" action="/invitaciones-equipo/{{ $invitacion->id_invitacion }}/aceptar" class="mb-0">
                                @csrf
                                <button type="submit" class="btn btn-primary">Aceptar</button>
                            </form>

                            <form method="POST" action="/invitaciones-equipo/{{ $invitacion->id_invitacion }}/rechazar" class="mb-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-light">Rechazar</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @forelse($equipos as $equipo)
        <div class="glass-card p-4 mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-start">
                <div>
                    <h3 class="h4 mb-1">{{ $equipo->nombre_equipo }}</h3>
                    <p class="mb-1">
                        <strong>Capitan:</strong> {{ $equipo->capitan->name ?? 'Sin asignar' }}
                    </p>
                    <p class="mb-1">
                        <strong>Miembros:</strong> {{ $equipo->usuarios->count() }}
                    </p>
                    <p class="text-muted mb-0">
                        @if (auth()->user()->esJugador())
                            Equipo disponible para unirse o gestionar si eres su capitan.
                        @else
                            Solo los jugadores pueden crear equipos y unirse a ellos.
                        @endif
                    </p>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <a href="/equipos/{{ $equipo->id_equipo }}" class="btn btn-sm btn-outline-light">Ver detalle</a>
                        <a href="/equipos/{{ $equipo->id_equipo }}/historial" class="btn btn-sm btn-outline-info">Ver historial de partidos</a>
                    </div>
                </div>

                <div class="w-100 w-md-auto">
                    <div class="glass-soft p-3 mb-3">
                        <div class="fw-semibold mb-2">Miembros del equipo</div>
                        <ul class="list-group mb-0">
                            @foreach ($equipo->usuarios as $miembro)
                                <li class="list-group-item d-flex flex-column flex-lg-row justify-content-between gap-2 align-items-lg-center">
                                    <div>
                                        <span class="fw-semibold">{{ $miembro->name }}</span>
                                        <span class="text-muted">({{ $miembro->email }})</span>
                                        @if ((int) $equipo->id_capitan === (int) $miembro->id)
                                            <span class="badge text-bg-success ms-2">Capitan</span>
                                        @endif
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        @if (auth()->user()->esJugador() && (int) auth()->id() === (int) $miembro->id)
                                            <form method="POST" action="/equipos/{{ $equipo->id_equipo }}/salir" class="mb-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning">Salir del equipo</button>
                                            </form>
                                        @endif

                                        @if (auth()->user()->esJugador() && (int) $equipo->id_capitan === (int) auth()->id() && (int) $miembro->id !== (int) auth()->id())
                                            <form method="POST" action="/equipos/{{ $equipo->id_equipo }}/miembros/{{ $miembro->id }}/expulsar" class="mb-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Expulsar</button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if (auth()->user()->esJugador() && (int) $equipo->id_capitan === (int) auth()->id())
                        <div class="glass-soft p-3 mb-3">
                            <div class="fw-semibold mb-2">Invitar jugador</div>
                            <form method="POST" action="/equipos/{{ $equipo->id_equipo }}/invitar" class="d-flex flex-column flex-lg-row gap-2">
                                @csrf
                                <input
                                    class="form-control"
                                    type="email"
                                    name="email"
                                    placeholder="email@jugador.com"
                                    required
                                >
                                <button type="submit" class="btn btn-primary">Enviar invitacion</button>
                            </form>
                        </div>
                    @endif

                    @if (auth()->user()->esJugador() && ! $equipo->usuarios->contains(auth()->id()))
                        <form method="POST" action="/equipos/{{ $equipo->id_equipo }}/unirse" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-light">Unirse</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No hay equipos creados todavia.</div>
    @endforelse
@endsection
