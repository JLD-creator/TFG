@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Bracket</h1>
            <p class="text-muted mb-0">Visualiza rondas, partidos y ganadores del torneo.</p>
        </div>
        <a href="/torneos" class="btn btn-outline-light">Volver a torneos</a>
    </div>

    @if ($partidos->isEmpty())
        <div class="alert alert-info">Este torneo todavia no tiene partidos generados.</div>
    @else
        <div class="row g-4">
            @foreach($partidos as $ronda => $partidosRonda)
                <div class="col-md-6 col-xl-3 round-column">
                    <div class="glass-soft p-3 h-100">
                        <h2 class="h4 fw-bold mb-3">Ronda {{ $ronda }}</h2>

                        @foreach($partidosRonda as $partido)
                            <div class="card bracket-card bg-dark text-light border-secondary mb-3">
                                <div class="card-body">
                                    <p class="mb-2 fw-semibold">
                                        {{ $partido->equipo1->nombre_equipo ?? 'TBD' }}
                                        vs
                                        {{ $partido->equipo2->nombre_equipo ?? 'TBD' }}
                                    </p>

                                    <p class="mb-2 text-info">
                                        {{ $partido->resultado_equipo1 ?? '-' }} -
                                        {{ $partido->resultado_equipo2 ?? '-' }}
                                    </p>

                                    @if($partido->ganador)
                                        <strong class="text-success">
                                            Ganador: {{ $partido->equipoGanador->nombre_equipo ?? $partido->ganador }}
                                        </strong>
                                    @else
                                        <span class="text-muted">Pendiente de jugar</span>
                                    @endif

                                    @if (auth()->user()->tieneRol('organizador', 'admin'))
                                        <div class="mt-3">
                                            <a href="/partidos/{{ $partido->id_partido }}/resultado" class="btn btn-sm btn-outline-light">Registrar resultado</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
