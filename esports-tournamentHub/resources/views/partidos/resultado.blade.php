<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del partido</title>
</head>
<body>
    <h1>Introducir resultado</h1>

    <p>{{ $partido->equipo1?->nombre_equipo ?? 'Equipo 1' }} vs {{ $partido->equipo2?->nombre_equipo ?? 'Equipo 2' }}</p>

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

    <form method="POST" action="/partidos/{{ $partido->id_partido }}/resultado">
        @csrf

        <label for="resultado_equipo1">{{ $partido->equipo1?->nombre_equipo ?? 'Equipo 1' }}</label>
        <input
            id="resultado_equipo1"
            type="number"
            name="resultado_equipo1"
            min="0"
            value="{{ old('resultado_equipo1', $partido->resultado_equipo1) }}"
            required
        >

        <label for="resultado_equipo2">{{ $partido->equipo2?->nombre_equipo ?? 'Equipo 2' }}</label>
        <input
            id="resultado_equipo2"
            type="number"
            name="resultado_equipo2"
            min="0"
            value="{{ old('resultado_equipo2', $partido->resultado_equipo2) }}"
            required
        >

        <button type="submit">Guardar</button>
    </form>

    <p><a href="/torneos">Volver a torneos</a></p>
</body>
</html>
