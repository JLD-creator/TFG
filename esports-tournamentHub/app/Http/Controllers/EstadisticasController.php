<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;
use Illuminate\View\View;

class EstadisticasController extends Controller
{
    public function index(): View
    {
        $equipos = Equipo::all();

        $stats = [];

        foreach ($equipos as $equipo) {
            $victorias = Partido::where('ganador', $equipo->id_equipo)->count();

            $derrotas = Partido::where(function ($query) use ($equipo) {
                $query->where('id_equipo1', $equipo->id_equipo)
                    ->orWhere('id_equipo2', $equipo->id_equipo);
            })
                ->where('ganador', '!=', $equipo->id_equipo)
                ->whereNotNull('ganador')
                ->count();

            $partidosResueltos = $victorias + $derrotas;
            $porcentajeVictorias = $partidosResueltos > 0
                ? round(($victorias / $partidosResueltos) * 100, 2)
                : 0;

            $stats[] = [
                'equipo' => $equipo->nombre_equipo,
                'victorias' => $victorias,
                'derrotas' => $derrotas,
                'porcentaje_victorias' => $porcentajeVictorias,
            ];
        }

        usort($stats, function ($a, $b) {
            return $b['victorias'] <=> $a['victorias'];
        });

        return view('estadisticas.index', compact('stats'));
    }
}
