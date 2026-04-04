<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear equipo</title>
</head>
<body>
    <h1>Crear equipo</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/equipos">
        @csrf

        <input type="text" name="nombre_equipo" placeholder="Nombre del equipo" value="{{ old('nombre_equipo') }}">

        <button type="submit">Crear</button>
    </form>

    <p><a href="/equipos">Volver a equipos</a></p>
</body>
</html>
