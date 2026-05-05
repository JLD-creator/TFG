@extends('layouts.app')

@section('content')
    <div class="glass-card p-4 p-lg-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="display-6 fw-bold mb-1">Ranking</h1>
                <p class="text-muted mb-0">Resumen de victorias y derrotas de todos los equipos registrados.</p>
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
                                <td>{{ number_format($equipo['porcentaje_victorias'], 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
