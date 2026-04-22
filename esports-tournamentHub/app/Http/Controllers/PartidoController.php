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
        $partido = Partido::findOrFail($id);

        if ($partido->ganador !== null) {
            return back()->with('error', 'Este partido ya tiene resultado');
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
            $ganador = null;
        }

        $partido->update([
            'resultado_equipo1' => $resultado1,
            'resultado_equipo2' => $resultado2,
            'ganador' => $ganador,
        ]);

        $torneoId = $partido->id_torneo;
        $rondaActual = $partido->ronda;

        $pendientes = Partido::where('id_torneo', $torneoId)
            ->where('ronda', $rondaActual)
            ->whereNull('ganador')
            ->count();

        if ($pendientes === 0) {
            $this->generarSiguienteRonda($torneoId, $rondaActual);
        }

        return redirect('/torneos')->with('success', 'Resultado guardado');
    }

    public function verBracket(int $torneoId): View
    {
        $partidos = Partido::with(['equipo1', 'equipo2', 'equipoGanador'])
            ->where('id_torneo', $torneoId)
            ->orderBy('ronda')
            ->orderBy('id_partido')
            ->get()
            ->groupBy('ronda');

        $torneo = Torneo::findOrFail($torneoId);

        return view('torneos.bracket', compact('partidos', 'torneo'));
    }

    private function generarSiguienteRonda(int $torneoId, int $rondaActual): void
    {
        $ganadores = Partido::where('id_torneo', $torneoId)
            ->where('ronda', $rondaActual)
            ->pluck('ganador')
            ->filter()
            ->values();

        if ($ganadores->count() === 1) {
            Torneo::where('id_torneo', $torneoId)->update(['estado' => 'finalizado']);

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
                Torneo::where('id_torneo', $torneoId)->update(['estado' => 'finalizado']);
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
}
