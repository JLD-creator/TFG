<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos</title>
</head>
<body>
    <h1>Equipos</h1>

    <p><a href="/dashboard">Volver al dashboard</a></p>
    <p><a href="/equipos/create">Crear equipo</a></p>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @forelse($equipos as $equipo)
        <div>
            <h3>{{ $equipo->nombre_equipo }}</h3>

            <form method="POST" action="/equipos/{{ $equipo->id_equipo }}/unirse">
                @csrf
                <button type="submit">Unirse</button>
            </form>
        </div>
    @empty
        <p>No hay equipos creados todavía.</p>
    @endforelse

    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>
