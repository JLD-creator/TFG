@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">
                @if ($torneo->tipo_torneo === 'liga')
                    Liga
                @else
                    Bracket
                @endif
            </h1>
            <p class="text-muted mb-0">
                @if ($torneo->tipo_torneo === 'liga')
                    Visualiza jornadas, resultados y clasificación del torneo.
                @else
                    Visualiza rondas, partidos y ganadores del torneo.
                @endif
            </p>
        </div>
        <a href="/torneos" class="btn btn-outline-light">Volver a torneos</a>
    </div>

    @if ($partidos->isEmpty())
        <div class="alert alert-info">Este torneo todavía no tiene partidos generados.</div>
    @else
        @if ($torneo->tipo_torneo === 'liga')
            <div class="glass-card p-4 mb-4">
                <h2 class="h4 fw-bold mb-3">Clasificación</h2>

                @if ($clasificacion->isEmpty())
                    <p class="text-muted mb-0">Todavía no hay resultados suficientes para calcular la clasificación.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-dark table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Equipo</th>
                                    <th>P</th>
                                    <th>J</th>
                                    <th>V</th>
                                    <th>E</th>
                                    <th>D</th>
                                    <th>GF</th>
                                    <th>GC</th>
                                    <th>Dif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clasificacion as $indice => $fila)
                                    <tr>
                                        <td>{{ $indice + 1 }}</td>
                                        <td>{{ $fila['equipo'] }}</td>
                                        <td>{{ $fila['puntos'] }}</td>
                                        <td>{{ $fila['jugados'] }}</td>
                                        <td>{{ $fila['victorias'] }}</td>
                                        <td>{{ $fila['empates'] }}</td>
                                        <td>{{ $fila['derrotas'] }}</td>
                                        <td>{{ $fila['favor'] }}</td>
                                        <td>{{ $fila['contra'] }}</td>
                                        <td>{{ $fila['diferencia'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        <div class="row g-4">
            @foreach($partidos as $ronda => $partidosRonda)
                <div class="col-md-6 col-xl-3 round-column">
                    <div class="glass-soft p-3 h-100">
                        <h2 class="h4 fw-bold mb-3">
                            @if ($torneo->tipo_torneo === 'liga')
                                Jornada {{ $ronda }}
                            @else
                                Ronda {{ $ronda }}
                            @endif
                        </h2>

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
                                    @elseif ($torneo->tipo_torneo === 'liga' && $partido->resultado_equipo1 !== null && $partido->resultado_equipo2 !== null)
                                        <strong class="text-warning">Empate</strong>
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
