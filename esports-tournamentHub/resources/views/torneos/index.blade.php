<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torneos</title>
</head>
<body>
    <h1>Torneos</h1>

    <p><a href="/dashboard">Volver al dashboard</a></p>
    <p><a href="/torneos/create">Crear torneo</a></p>

    @if (session('error'))
        <div>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @forelse($torneos as $torneo)
        <div>
            <h3>{{ $torneo->nombre }}</h3>
            <p>{{ $torneo->juego }}</p>
            <p>Tipo: {{ $torneo->tipo_torneo }}</p>
            <p>Fecha: {{ $torneo->fecha_inicio }}</p>
            <p>Estado: {{ $torneo->estado }}</p>

            <form method="POST" action="/torneos/{{ $torneo->id_torneo }}/inscribirse">
                @csrf
                <button type="submit">Inscribirse</button>
            </form>
        </div>
    @empty
        <p>No hay torneos creados todavía.</p>
    @endforelse

    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>
