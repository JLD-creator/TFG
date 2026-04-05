<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear torneo</title>
</head>
<body>
    <h1>Crear torneo</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/torneos">
        @csrf

        <input type="text" name="nombre" placeholder="Nombre del torneo" value="{{ old('nombre') }}">
        <input type="text" name="juego" placeholder="Juego" value="{{ old('juego') }}">

        <select name="tipo_torneo">
            <option value="eliminacion" @selected(old('tipo_torneo') === 'eliminacion')>Eliminación directa</option>
            <option value="liga" @selected(old('tipo_torneo') === 'liga')>Liga</option>
        </select>

        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}">

        <button type="submit">Crear</button>
    </form>

    <p><a href="/torneos">Volver a torneos</a></p>
</body>
</html>
