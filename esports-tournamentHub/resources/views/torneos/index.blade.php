@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Torneos</h1>
            <p class="text-muted mb-0">Crea competiciones, inscribe equipos y gestiona el bracket.</p>
        </div>
        @if (auth()->user()->tieneRol('organizador', 'admin'))
            <a href="/torneos/create" class="btn btn-primary">Crear torneo</a>
        @endif
    </div>

    @forelse($torneos as $torneo)
        <div class="glass-card p-4 mb-3">
            <div class="row g-3 align-items-start">
                <div class="col-lg-6">
                    <h3 class="h4 mb-1">{{ $torneo->nombre }}</h3>
                    <p class="mb-1 text-muted">{{ $torneo->juego }}</p>
                    <p class="mb-1"><strong>Tipo:</strong> {{ $torneo->tipo_torneo }}</p>
                    <p class="mb-1"><strong>Estado:</strong> {{ $torneo->estado }}</p>
                    <p class="mb-0">
                        <strong>Normas:</strong><br>
                        <span class="text-muted">{{ $torneo->normas }}</span>
                    </p>
                </div>

                <div class="col-lg-6">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        @if (auth()->user()->esJugador() && $torneo->estaAbierto())
                            <form method="POST" action="/torneos/{{ $torneo->id_torneo }}/inscribirse" class="mb-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-light">Inscribirse</button>
                            </form>
                        @endif

                        @if (auth()->user()->tieneRol('organizador', 'admin') && $torneo->estaAbierto())
                            <form method="POST" action="/torneos/{{ $torneo->id_torneo }}/bracket" class="mb-0">
                                @csrf
                                <button type="submit" class="btn btn-primary">Generar Bracket</button>
                            </form>
                        @endif

                        <a href="/torneos/{{ $torneo->id_torneo }}/bracket" class="btn btn-outline-info">Ver bracket</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No hay torneos creados todavía.</div>
    @endforelse
@endsection
