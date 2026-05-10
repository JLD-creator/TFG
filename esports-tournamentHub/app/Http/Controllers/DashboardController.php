<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\InvitacionEquipo;
use App\Models\Partido;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user()->load('equipos');

        $metricas = [
            'equipos_total' => Equipo::count(),
            'torneos_total' => Torneo::count(),
            'torneos_abiertos' => Torneo::where('estado', Torneo::ESTADO_ABIERTO)->count(),
            'torneos_en_curso' => Torneo::where('estado', Torneo::ESTADO_EN_CURSO)->count(),
            'torneos_finalizados' => Torneo::where('estado', Torneo::ESTADO_FINALIZADO)->count(),
            'partidos_pendientes' => Partido::where(function ($query) {
                $query->whereNull('resultado_equipo1')
                    ->orWhereNull('resultado_equipo2');
            })->count(),
        ];

        $metricasJugador = [
            'mis_equipos' => $user->equipos->count(),
            'invitaciones_pendientes' => InvitacionEquipo::where('id_usuario_invitado', $user->id)
                ->where('estado', 'pendiente')
                ->count(),
            'mis_torneos' => Torneo::whereHas('inscripciones', function ($query) use ($user) {
                $query->whereIn('id_equipo', $user->equipos->pluck('id_equipo'));
            })->count(),
        ];

        $metricasOrganizador = [
            'torneos_abiertos' => $metricas['torneos_abiertos'],
            'torneos_en_curso' => $metricas['torneos_en_curso'],
            'partidos_pendientes' => $metricas['partidos_pendientes'],
        ];

        $metricasAdmin = [
            'usuarios_total' => User::count(),
            'admins_total' => User::where('rol', 'admin')->count(),
            'organizadores_total' => User::where('rol', 'organizador')->count(),
            'jugadores_total' => User::where('rol', 'jugador')->count(),
        ];

        $torneosRecientes = Torneo::orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'metricas',
            'metricasJugador',
            'metricasOrganizador',
            'metricasAdmin',
            'torneosRecientes'
        ));
    }
}
