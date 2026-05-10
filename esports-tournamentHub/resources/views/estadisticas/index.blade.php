@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="display-5 fw-bold mb-1">Centro de Estadisticas</h1>
            <p class="text-muted mb-0">Una vista visual del rendimiento de los equipos para seguir el estado competitivo del hub.</p>
        </div>
    </div>

    <div class="glass-card p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
            <div>
                <div class="small text-uppercase text-muted">Filtro activo</div>
                <h2 class="h4 fw-bold mb-1">
                    @if ($torneoSeleccionado)
                        {{ $torneoSeleccionado->nombre }}
                    @else
                        Todos los torneos
                    @endif
                </h2>
                <p class="text-muted mb-0">
                    @if ($torneoSeleccionado)
                        Estadisticas centradas en {{ $torneoSeleccionado->juego }} y el formato {{ $torneoSeleccionado->tipo_torneo }}.
                    @else
                        Vista global con todos los datos competitivos acumulados en la plataforma.
                    @endif
                </p>
            </div>

            <form method="GET" action="/estadisticas" class="d-flex flex-column flex-md-row gap-2">
                <select name="torneo" class="form-select">
                    <option value="">Todos los torneos</option>
                    @foreach ($torneos as $torneo)
                        <option value="{{ $torneo->id_torneo }}" @selected($torneoSeleccionadoId === $torneo->id_torneo)>
                            {{ $torneo->nombre }} - {{ $torneo->juego }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Aplicar filtro</button>
                @if ($torneoSeleccionado)
                    <a href="/estadisticas" class="btn btn-outline-light">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Equipos</div>
                <h3 class="fw-bold">{{ $resumen['equipos'] }}</h3>
                <p class="text-muted mb-0">Equipos que ya tienen datos competitivos registrados.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Partidos</div>
                <h3 class="fw-bold">{{ $resumen['partidos_resueltos'] }}</h3>
                <p class="text-muted mb-0">Partidos resueltos que alimentan el panel de estadisticas.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Victorias</div>
                <h3 class="fw-bold">{{ $resumen['victorias_totales'] }}</h3>
                <p class="text-muted mb-0">Victorias acumuladas entre todos los equipos registrados.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Empates / Top</div>
                <h3 class="fw-bold">{{ $resumen['empates_totales'] }}</h3>
                <p class="text-muted mb-0">Empates detectados en los datos filtrados. Lider actual: {{ $resumen['mejor_equipo'] }}.</p>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="glass-card p-4 p-lg-5 h-100">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h3 fw-bold mb-1">Comparativa de equipos</h2>
                        <p class="text-muted mb-0">Victorias y derrotas en una sola vista para comparar el rendimiento competitivo.</p>
                    </div>
                </div>

                @if (empty($stats))
                    <div class="alert alert-info mb-0">Todavia no hay datos suficientes para generar la grafica principal.</div>
                @else
                    <div style="height: 360px;">
                        <canvas id="teamPerformanceChart"></canvas>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-xl-4">
            <div class="glass-card p-4 p-lg-5 h-100">
                <div class="mb-4">
                    <h2 class="h3 fw-bold mb-1">Win Rate</h2>
                    <p class="text-muted mb-0">Distribucion del porcentaje de victorias por equipo.</p>
                </div>

                @if (empty($stats))
                    <div class="alert alert-info mb-0">Todavia no hay datos suficientes para generar la grafica circular.</div>
                @else
                    <div style="height: 360px;">
                        <canvas id="winRateChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="glass-card p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h3 fw-bold mb-1">Volumen competitivo</h2>
                        <p class="text-muted mb-0">Numero total de partidos disputados por equipo para detectar actividad y constancia competitiva.</p>
                    </div>
                </div>

                @if (empty($stats))
                    <div class="alert alert-info mb-0">Todavia no hay datos suficientes para generar la tercera grafica.</div>
                @else
                    <div style="height: 320px;">
                        <canvas id="activityChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="glass-card p-4 p-lg-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="h3 fw-bold mb-1">Ranking detallado</h2>
                <p class="text-muted mb-0">Tabla completa para consultar victorias, derrotas, empates, partidos resueltos y porcentaje de victorias.</p>
            </div>
        </div>

        @if (empty($stats))
            <div class="alert alert-info mb-0">Todavia no hay datos suficientes para mostrar el ranking.</div>
        @else
            <div class="table-responsive">
                <table class="table table-dark table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Equipo</th>
                            <th>Victorias</th>
                            <th>Derrotas</th>
                            <th>Empates</th>
                            <th>Resueltos</th>
                            <th>Total</th>
                            <th>% Victorias</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats as $index => $equipo)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $equipo['equipo'] }}</td>
                                <td>{{ $equipo['victorias'] }}</td>
                                <td>{{ $equipo['derrotas'] }}</td>
                                <td>{{ $equipo['empates'] }}</td>
                                <td>{{ $equipo['partidos_resueltos'] }}</td>
                                <td>{{ $equipo['partidos_totales'] }}</td>
                                <td>{{ number_format($equipo['porcentaje_victorias'], 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @if (! empty($stats))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const statsData = @json($chartData);
            const chartPalette = [
                '#00d084',
                '#16a8f0',
                '#ffb020',
                '#ff6384',
                '#7dd3fc',
                '#34d399',
                '#f59e0b',
                '#818cf8',
                '#f97316',
                '#22c55e',
            ];

            const chartBaseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1100,
                    easing: 'easeOutQuart',
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#e5eef7',
                            font: {
                                family: 'Segoe UI',
                                size: 12,
                            },
                        },
                    },
                    tooltip: {
                        backgroundColor: 'rgba(8, 25, 38, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#d7e5f2',
                        borderColor: 'rgba(0, 208, 132, 0.35)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                    },
                },
            };

            new Chart(document.getElementById('teamPerformanceChart'), {
                type: 'bar',
                data: {
                    labels: statsData.labels,
                    datasets: [
                        {
                            label: 'Victorias',
                            data: statsData.victorias,
                            backgroundColor: 'rgba(0, 208, 132, 0.75)',
                            borderColor: '#00d084',
                            borderWidth: 1.5,
                            borderRadius: 10,
                            hoverBackgroundColor: '#1ef0a0',
                        },
                        {
                            label: 'Derrotas',
                            data: statsData.derrotas,
                            backgroundColor: 'rgba(255, 99, 132, 0.68)',
                            borderColor: '#ff6384',
                            borderWidth: 1.5,
                            borderRadius: 10,
                            hoverBackgroundColor: '#ff88a4',
                        },
                    ],
                },
                options: {
                    ...chartBaseOptions,
                    scales: {
                        x: {
                            ticks: {
                                color: '#c6d3df',
                            },
                            grid: {
                                color: 'rgba(198, 211, 223, 0.08)',
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#c6d3df',
                                precision: 0,
                            },
                            grid: {
                                color: 'rgba(198, 211, 223, 0.08)',
                            },
                        },
                    },
                },
            });

            new Chart(document.getElementById('winRateChart'), {
                type: 'doughnut',
                data: {
                    labels: statsData.labels,
                    datasets: [{
                        label: '% Victorias',
                        data: statsData.porcentajes,
                        backgroundColor: chartPalette,
                        borderColor: 'rgba(8, 25, 38, 0.8)',
                        borderWidth: 2,
                        hoverOffset: 10,
                    }],
                },
                options: {
                    ...chartBaseOptions,
                    cutout: '62%',
                },
            });

            new Chart(document.getElementById('activityChart'), {
                type: 'line',
                data: {
                    labels: statsData.labels,
                    datasets: [{
                        label: 'Partidos totales',
                        data: statsData.partidosTotales,
                        fill: true,
                        tension: 0.35,
                        backgroundColor: 'rgba(22, 168, 240, 0.12)',
                        borderColor: '#16a8f0',
                        pointBackgroundColor: '#7dd3fc',
                        pointBorderColor: '#0b2233',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        borderWidth: 3,
                    }],
                },
                options: {
                    ...chartBaseOptions,
                    scales: {
                        x: {
                            ticks: {
                                color: '#c6d3df',
                            },
                            grid: {
                                color: 'rgba(198, 211, 223, 0.06)',
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#c6d3df',
                                precision: 0,
                            },
                            grid: {
                                color: 'rgba(198, 211, 223, 0.08)',
                            },
                        },
                    },
                },
            });
        </script>
    @endif
@endpush
