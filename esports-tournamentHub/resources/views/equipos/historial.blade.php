@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Historial de {{ $equipo->nombre_equipo }}</h1>
            <p class="text-muted mb-0">Consulta todos los partidos jugados por el equipo y su rendimiento acumulado.</p>
        </div>
        <a href="/equipos" class="btn btn-outline-light">Volver a equipos</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4 h-100">
                <div class="small text-uppercase text-muted">Jugados</div>
                <div class="display-6 fw-bold">{{ $resumen['jugados'] }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 h-100">
                <div class="small text-uppercase text-muted">Victorias</div>
                <div class="display-6 fw-bold text-success">{{ $resumen['victorias'] }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 h-100">
                <div class="small text-uppercase text-muted">Empates</div>
                <div class="display-6 fw-bold text-warning">{{ $resumen['empates'] }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 h-100">
                <div class="small text-uppercase text-muted">Derrotas</div>
                <div class="display-6 fw-bold text-danger">{{ $resumen['derrotas'] }}</div>
            </div>
        </div>
    </div>

    <div class="glass-card p-4">
        <h2 class="h4 fw-bold mb-3">Torneos disputados</h2>

        @if ($torneosDisputados->isEmpty())
            <div class="alert alert-info">Este equipo todavia no ha participado en ningun torneo.</div>
        @else
            <div class="table-responsive mb-4">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Torneo</th>
                            <th>Juego</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>J</th>
                            <th>V</th>
                            <th>E</th>
                            <th>D</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($torneosDisputados as $torneo)
                            <tr>
                                <td>{{ $torneo['nombre'] }}</td>
                                <td>{{ $torneo['juego'] }}</td>
                                <td>{{ $torneo['tipo'] }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($torneo['fecha_inicio'])->format('d/m/Y') }}</td>
                                <td>{{ $torneo['estado'] }}</td>
                                <td>{{ $torneo['jugados'] }}</td>
                                <td>{{ $torneo['victorias'] }}</td>
                                <td>{{ $torneo['empates'] }}</td>
                                <td>{{ $torneo['derrotas'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <h2 class="h4 fw-bold mb-3">Partidos</h2>

        @if ($partidos->isEmpty())
            <div class="alert alert-info mb-0">Este equipo todavia no ha disputado ningun partido.</div>
        @else
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Torneo</th>
                            <th>Tipo</th>
                            <th>Ronda/Jornada</th>
                            <th>Rival</th>
                            <th>Marcador</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($partidos as $partido)
                            @php
                                $esEquipo1 = (int) $partido->id_equipo1 === (int) $equipo->id_equipo;
                                $rival = $esEquipo1 ? $partido->equipo2 : $partido->equipo1;
                                $marcadorPropio = $esEquipo1 ? $partido->resultado_equipo1 : $partido->resultado_equipo2;
                                $marcadorRival = $esEquipo1 ? $partido->resultado_equipo2 : $partido->resultado_equipo1;
                            @endphp
                            <tr>
                                <td>{{ $partido->torneo->nombre ?? 'Sin torneo' }}</td>
                                <td>{{ $partido->torneo->tipo_torneo ?? '-' }}</td>
                                <td>
                                    @if (($partido->torneo->tipo_torneo ?? null) === 'liga')
                                        Jornada {{ $partido->ronda }}
                                    @else
                                        Ronda {{ $partido->ronda }}
                                    @endif
                                </td>
                                <td>{{ $rival?->nombre_equipo ?? 'Pendiente' }}</td>
                                <td>
                                    @if ($marcadorPropio !== null && $marcadorRival !== null)
                                        {{ $equipo->nombre_equipo }} {{ $marcadorPropio }} - {{ $marcadorRival }} {{ $rival?->nombre_equipo ?? 'Rival' }}
                                    @else
                                        Pendiente
                                    @endif
                                </td>
                                <td>
                                    @if ($partido->ganador === $equipo->id_equipo)
                                        <span class="text-success fw-semibold">Victoria</span>
                                    @elseif ($partido->ganador !== null)
                                        <span class="text-danger fw-semibold">Derrota</span>
                                    @elseif ($marcadorPropio !== null && $marcadorRival !== null)
                                        <span class="text-warning fw-semibold">Empate</span>
                                    @else
                                        <span class="text-muted">Sin jugar</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
