@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Equipos</h1>
            <p class="text-muted mb-0">Gestiona equipos y unete a uno existente.</p>
        </div>
        @if (auth()->user()->esJugador())
            <a href="/equipos/create" class="btn btn-primary">Crear equipo</a>
        @endif
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @forelse($equipos as $equipo)
        <div class="glass-card p-4 mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                <div>
                    <h3 class="h4 mb-1">{{ $equipo->nombre_equipo }}</h3>
                    <p class="text-muted mb-0">
                        @if (auth()->user()->esJugador())
                            Equipo disponible para unirse.
                        @else
                            Solo los jugadores pueden crear equipos y unirse a ellos.
                        @endif
                    </p>
                </div>

                @if (auth()->user()->esJugador())
                    <form method="POST" action="/equipos/{{ $equipo->id_equipo }}/unirse" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-light">Unirse</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-info">No hay equipos creados todavia.</div>
    @endforelse
@endsection
