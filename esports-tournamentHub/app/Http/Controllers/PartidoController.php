<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Torneo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PartidoController extends Controller
{
    public function edit(int $id): View
    {
        $partido = Partido::with(['equipo1', 'equipo2'])->findOrFail($id);

        return view('partidos.resultado', compact('partido'));
    }

    public function guardarResultado(Request $request, int $id): RedirectResponse
    {
        $partido = Partido::with('torneo')->findOrFail($id);

        if (! $partido->torneo->estaEnCurso()) {
            return back()->with('error', 'No puedes registrar resultados porque el torneo no esta en curso.');
        }

        if ($partido->resultado_equipo1 !== null || $partido->resultado_equipo2 !== null) {
            return back()->with('error', 'Este partido ya tiene un resultado registrado.');
        }

        $datos = $request->validate([
            'resultado_equipo1' => 'required|integer|min:0',
            'resultado_equipo2' => 'required|integer|min:0',
        ]);

        $resultado1 = (int) $datos['resultado_equipo1'];
        $resultado2 = (int) $datos['resultado_equipo2'];

        if ($resultado1 > $resultado2) {
            $ganador = $partido->id_equipo1;
        } elseif ($resultado2 > $resultado1) {
            $ganador = $partido->id_equipo2;
        } else {
            if ($partido->torneo->tipo_torneo !== 'liga') {
                return back()->with('error', 'En eliminacion directa no se permiten empates.');
            }

            $ganador = null;
        }

        $partido->update([
            'resultado_equipo1' => $resultado1,
            'resultado_equipo2' => $resultado2,
            'ganador' => $ganador,
        ]);

        $torneoId = $partido->id_torneo;
        $rondaActual = $partido->ronda;

        if ($partido->torneo->tipo_torneo === 'liga') {
            $this->finalizarLigaSiCorresponde($torneoId);

            return redirect('/torneos')->with('success', 'El resultado se ha guardado correctamente.');
        }

        $pendientes = Partido::where('id_torneo', $torneoId)
            ->where('ronda', $rondaActual)
            ->whereNull('ganador')
            ->count();

        if ($pendientes === 0) {
            $this->generarSiguienteRonda($torneoId, $rondaActual);
        }

        return redirect('/torneos')->with('success', 'El resultado se ha guardado correctamente.');
    }

    public function verBracket(int $torneoId): View
    {
        $torneo = Torneo::findOrFail($torneoId);

        $partidos = Partido::with(['equipo1', 'equipo2', 'equipoGanador'])
            ->where('id_torneo', $torneoId)
            ->orderBy('ronda')
            ->orderBy('id_partido')
            ->get()
            ->groupBy('ronda');

        $clasificacion = $torneo->tipo_torneo === 'liga'
            ? $this->calcularClasificacionLiga($torneoId)
            : collect();

        return view('torneos.bracket', compact('partidos', 'torneo', 'clasificacion'));
    }

    private function generarSiguienteRonda(int $torneoId, int $rondaActual): void
    {
        $ganadores = Partido::where('id_torneo', $torneoId)
            ->where('ronda', $rondaActual)
            ->pluck('ganador')
            ->filter()
            ->values();

        if ($ganadores->count() === 1) {
            Torneo::where('id_torneo', $torneoId)->update(['estado' => Torneo::ESTADO_FINALIZADO]);

            return;
        }

        if ($ganadores->count() <= 1) {
            return;
        }

        $nuevaRonda = $rondaActual + 1;

        if (Partido::where('id_torneo', $torneoId)->where('ronda', $nuevaRonda)->exists()) {
            return;
        }

        $ganadores = $this->resolverByes($ganadores, $torneoId, $nuevaRonda);

        if ($ganadores->count() <= 1) {
            if ($ganadores->count() === 1) {
                Torneo::where('id_torneo', $torneoId)->update(['estado' => Torneo::ESTADO_FINALIZADO]);
            }

            return;
        }

        $ganadores = $ganadores->shuffle()->values();

        for ($i = 0; $i < $ganadores->count(); $i += 2) {
            if (! isset($ganadores[$i + 1])) {
                break;
            }

            Partido::create([
                'id_torneo' => $torneoId,
                'id_equipo1' => $ganadores[$i],
                'id_equipo2' => $ganadores[$i + 1],
                'ronda' => $nuevaRonda,
            ]);
        }
    }

    private function resolverByes(Collection $ganadores, int $torneoId, int $nuevaRonda): Collection
    {
        if ($ganadores->count() % 2 === 0) {
            return $ganadores;
        }

        $ganadores = $ganadores->shuffle()->values();
        $equipoConBye = $ganadores->pop();

        if ($ganadores->count() === 0) {
            return collect([$equipoConBye]);
        }

        Partido::create([
            'id_torneo' => $torneoId,
            'id_equipo1' => $equipoConBye,
            'id_equipo2' => $equipoConBye,
            'ronda' => $nuevaRonda,
            'resultado_equipo1' => 1,
            'resultado_equipo2' => 0,
            'ganador' => $equipoConBye,
        ]);

        return $ganadores;
    }

    private function finalizarLigaSiCorresponde(int $torneoId): void
    {
        $pendientes = Partido::where('id_torneo', $torneoId)
            ->where(function ($query) {
                $query->whereNull('resultado_equipo1')
                    ->orWhereNull('resultado_equipo2');
            })
            ->count();

        if ($pendientes === 0) {
            Torneo::where('id_torneo', $torneoId)->update(['estado' => Torneo::ESTADO_FINALIZADO]);
        }
    }

    private function calcularClasificacionLiga(int $torneoId): Collection
    {
        $partidos = Partido::with(['equipo1', 'equipo2'])
            ->where('id_torneo', $torneoId)
            ->whereNotNull('resultado_equipo1')
            ->whereNotNull('resultado_equipo2')
            ->get();

        $tabla = collect();

        foreach ($partidos as $partido) {
            foreach ([
                ['equipo' => $partido->equipo1, 'favor' => $partido->resultado_equipo1, 'contra' => $partido->resultado_equipo2],
                ['equipo' => $partido->equipo2, 'favor' => $partido->resultado_equipo2, 'contra' => $partido->resultado_equipo1],
            ] as $fila) {
                if (! $fila['equipo']) {
                    continue;
                }

                $idEquipo = $fila['equipo']->id_equipo;

                if (! $tabla->has($idEquipo)) {
                    $tabla->put($idEquipo, [
                        'equipo' => $fila['equipo']->nombre_equipo,
                        'puntos' => 0,
                        'jugados' => 0,
                        'victorias' => 0,
                        'empates' => 0,
                        'derrotas' => 0,
                        'favor' => 0,
                        'contra' => 0,
                    ]);
                }

                $registro = $tabla->get($idEquipo);
                $registro['jugados']++;
                $registro['favor'] += $fila['favor'];
                $registro['contra'] += $fila['contra'];

                if ($fila['favor'] > $fila['contra']) {
                    $registro['victorias']++;
                    $registro['puntos'] += 3;
                } elseif ($fila['favor'] === $fila['contra']) {
                    $registro['empates']++;
                    $registro['puntos'] += 1;
                } else {
                    $registro['derrotas']++;
                }

                $tabla->put($idEquipo, $registro);
            }
        }

        return $tabla
            ->map(function (array $registro) {
                $registro['diferencia'] = $registro['favor'] - $registro['contra'];

                return $registro;
            })
            ->sort(function (array $a, array $b) {
                return [$b['puntos'], $b['diferencia'], $b['favor']]
                    <=> [$a['puntos'], $a['diferencia'], $a['favor']];
            })
            ->values();
    }
}
