<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bracket del torneo</title>
</head>
<body>
    <h1>Bracket de {{ $torneo->nombre }}</h1>

    <p><a href="/torneos">Volver a torneos</a></p>

    @if ($partidos->isEmpty())
        <p>Este torneo todavía no tiene partidos generados.</p>
    @else
        <div style="display:flex; gap:40px; align-items:flex-start; flex-wrap:wrap;">
            @foreach($partidos as $ronda => $partidosRonda)
                <div>
                    <h2>Ronda {{ $ronda }}</h2>

                    @foreach($partidosRonda as $partido)
                        <div style="border:1px solid black; padding:10px; margin-bottom:10px; min-width:220px;">
                            <p>
                                {{ $partido->equipo1->nombre_equipo ?? 'TBD' }}
                                vs
                                {{ $partido->equipo2->nombre_equipo ?? 'TBD' }}
                            </p>

                            <p>
                                {{ $partido->resultado_equipo1 ?? '-' }}
                                -
                                {{ $partido->resultado_equipo2 ?? '-' }}
                            </p>

                            @if($partido->equipoGanador)
                                <strong>Ganador: {{ $partido->equipoGanador->nombre_equipo }}</strong>
                            @endif

                            <p><a href="/partidos/{{ $partido->id_partido }}/resultado">Introducir resultado</a></p>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
</body>
</html>
