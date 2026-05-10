<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EstadisticasController extends Controller
{
    public function index(Request $request): View
    {
        $torneos = Torneo::orderByDesc('fecha_inicio')->get();
        $torneoSeleccionadoId = $request->integer('torneo');
        $torneoSeleccionado = $torneoSeleccionadoId > 0
            ? $torneos->firstWhere('id_torneo', $torneoSeleccionadoId)
            : null;

        $equipos = $torneoSeleccionado
            ? Equipo::whereHas('inscripciones', function ($query) use ($torneoSeleccionadoId) {
                $query->where('id_torneo', $torneoSeleccionadoId);
            })->get()
            : Equipo::all();

        $stats = [];

        foreach ($equipos as $equipo) {
            $partidosEquipo = Partido::query()
                ->when($torneoSeleccionadoId, function ($query) use ($torneoSeleccionadoId) {
                    $query->where('id_torneo', $torneoSeleccionadoId);
                })
                ->where(function ($query) use ($equipo) {
                    $query->where('id_equipo1', $equipo->id_equipo)
                        ->orWhere('id_equipo2', $equipo->id_equipo);
                });

            $victorias = (clone $partidosEquipo)
                ->where('ganador', $equipo->id_equipo)
                ->count();

            $derrotas = (clone $partidosEquipo)
                ->where('ganador', '!=', $equipo->id_equipo)
                ->whereNotNull('ganador')
                ->count();

            $empates = (clone $partidosEquipo)
                ->whereNull('ganador')
                ->whereNotNull('resultado_equipo1')
                ->whereNotNull('resultado_equipo2')
                ->count();

            $partidosResueltos = $victorias + $derrotas;
            $porcentajeVictorias = $partidosResueltos > 0
                ? round(($victorias / $partidosResueltos) * 100, 2)
                : 0;

            $stats[] = [
                'equipo' => $equipo->nombre_equipo,
                'victorias' => $victorias,
                'derrotas' => $derrotas,
                'empates' => $empates,
                'porcentaje_victorias' => $porcentajeVictorias,
                'partidos_resueltos' => $partidosResueltos,
                'partidos_totales' => $partidosResueltos + $empates,
            ];
        }

        usort($stats, function ($a, $b) {
            return [$b['victorias'], $b['porcentaje_victorias']]
                <=> [$a['victorias'], $a['porcentaje_victorias']];
        });

        $chartData = [
            'labels' => array_column($stats, 'equipo'),
            'victorias' => array_column($stats, 'victorias'),
            'derrotas' => array_column($stats, 'derrotas'),
            'empates' => array_column($stats, 'empates'),
            'porcentajes' => array_column($stats, 'porcentaje_victorias'),
            'partidosTotales' => array_column($stats, 'partidos_totales'),
        ];

        $resumen = [
            'equipos' => count($stats),
            'partidos_resueltos' => array_sum(array_column($stats, 'partidos_resueltos')),
            'victorias_totales' => array_sum(array_column($stats, 'victorias')),
            'empates_totales' => array_sum(array_column($stats, 'empates')),
            'mejor_equipo' => $stats[0]['equipo'] ?? 'Sin datos',
        ];

        return view('estadisticas.index', compact(
            'stats',
            'chartData',
            'resumen',
            'torneos',
            'torneoSeleccionado',
            'torneoSeleccionadoId'
        ));
    }
}
