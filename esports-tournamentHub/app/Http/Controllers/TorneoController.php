<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Partido;
use App\Models\Torneo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TorneoController extends Controller
{
    public function index(): View
    {
        $torneos = Torneo::with(['partidos.equipo1', 'partidos.equipo2'])->get();

        return view('torneos.index', compact('torneos'));
    }

    public function create(): View
    {
        return view('torneos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'juego' => ['required', 'string', 'max:255'],
            'tipo_torneo' => ['required', 'string', 'max:255'],
            'fecha_inicio' => ['required', 'date'],
            'normas' => ['required', 'string', 'max:3000'],
        ]);

        Torneo::create([
            'nombre' => $request->nombre,
            'juego' => $request->juego,
            'tipo_torneo' => $request->tipo_torneo,
            'fecha_inicio' => $request->fecha_inicio,
            'normas' => $request->normas,
            'estado' => Torneo::ESTADO_ABIERTO,
        ]);

        return redirect('/torneos');
    }

    public function inscribirse(int $id): RedirectResponse
    {
        $equipo = Auth::user()->equipos()->first();
        $torneo = Torneo::findOrFail($id);

        if (! $torneo->estaAbierto()) {
            return back()->with('error', 'No puedes inscribirte porque este torneo ya no esta abierto.');
        }

        if (! $equipo) {
            return back()->with('error', 'Necesitas pertenecer a un equipo antes de poder inscribirte en un torneo.');
        }

        $existe = Inscripcion::where('id_torneo', $torneo->id_torneo)
            ->where('id_equipo', $equipo->id_equipo)
            ->exists();

        if (! $existe) {
            Inscripcion::create([
                'id_torneo' => $torneo->id_torneo,
                'id_equipo' => $equipo->id_equipo,
            ]);
        }

        return redirect('/torneos')->with('success', 'Tu equipo se ha inscrito correctamente en el torneo.');
    }

    public function generarBracket(int $torneo_id): RedirectResponse
    {
        $torneo = Torneo::findOrFail($torneo_id);

        if (! $torneo->estaAbierto()) {
            return back()->with('error', 'No puedes generar el bracket o calendario porque el torneo ya no esta abierto.');
        }

        if (Partido::where('id_torneo', $torneo_id)->exists()) {
            return back()->with('error', 'Este torneo ya tiene un bracket o calendario generado.');
        }

        $equipos = Inscripcion::where('id_torneo', $torneo_id)
            ->pluck('id_equipo')
            ->toArray();

        if (count($equipos) < 2) {
            return back()->with('error', 'Debes tener al menos dos equipos inscritos para generar el torneo.');
        }

        if ($torneo->tipo_torneo === 'liga') {
            $this->generarLiga($torneo_id, $equipos);
            $torneo->update(['estado' => Torneo::ESTADO_EN_CURSO]);

            return redirect('/torneos')->with('success', 'El calendario de liga se ha generado correctamente.');
        }

        $this->generarEliminacionDirecta($torneo_id, $equipos);
        $torneo->update(['estado' => Torneo::ESTADO_EN_CURSO]);

        return redirect('/torneos')->with('success', 'El bracket se ha generado correctamente.');
    }

    private function generarEliminacionDirecta(int $torneoId, array $equipos): void
    {
        shuffle($equipos);
        $totalEquipos = count($equipos);

        for ($i = 0; $i < $totalEquipos; $i += 2) {
            if (! isset($equipos[$i + 1])) {
                Partido::create([
                    'id_torneo' => $torneoId,
                    'id_equipo1' => $equipos[$i],
                    'id_equipo2' => $equipos[$i],
                    'ronda' => 1,
                    'resultado_equipo1' => 1,
                    'resultado_equipo2' => 0,
                    'ganador' => $equipos[$i],
                ]);

                continue;
            }

            Partido::create([
                'id_torneo' => $torneoId,
                'id_equipo1' => $equipos[$i],
                'id_equipo2' => $equipos[$i + 1],
                'ronda' => 1,
            ]);
        }
    }

    private function generarLiga(int $torneoId, array $equipos): void
    {
        shuffle($equipos);

        if (count($equipos) % 2 !== 0) {
            $equipos[] = null;
        }

        $totalEquipos = count($equipos);
        $rondas = $totalEquipos - 1;
        $mitad = (int) ($totalEquipos / 2);

        for ($ronda = 0; $ronda < $rondas; $ronda++) {
            for ($i = 0; $i < $mitad; $i++) {
                $equipo1 = $equipos[$i];
                $equipo2 = $equipos[$totalEquipos - 1 - $i];

                if ($equipo1 === null || $equipo2 === null) {
                    continue;
                }

                Partido::create([
                    'id_torneo' => $torneoId,
                    'id_equipo1' => $equipo1,
                    'id_equipo2' => $equipo2,
                    'ronda' => $ronda + 1,
                ]);
            }

            $fijo = array_shift($equipos);
            $ultimo = array_pop($equipos);
            array_unshift($equipos, $fijo);
            array_splice($equipos, 1, 0, [$ultimo]);
        }
    }
}
