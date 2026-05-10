<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\EquipoBaja;
use App\Models\InvitacionEquipo;
use App\Models\Inscripcion;
use App\Models\Partido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EquipoController extends Controller
{
    public function index(): View
    {
        $equipos = Equipo::with(['capitan', 'usuarios'])->get();
        $user = Auth::user();
        $invitacionesPendientes = InvitacionEquipo::with(['equipo', 'invitador'])
            ->where('id_usuario_invitado', $user->id)
            ->where('estado', 'pendiente')
            ->get();
        $equiposBloqueadosParaReingreso = EquipoBaja::where('id_usuario', $user->id)
            ->pluck('id_equipo')
            ->all();

        return view('equipos.index', compact('equipos', 'invitacionesPendientes', 'equiposBloqueadosParaReingreso'));
    }

    public function create(): View
    {
        return view('equipos.create');
    }

    public function show(Equipo $equipo): View
    {
        $equipo->load(['capitan', 'usuarios', 'inscripciones.torneo']);

        $resumen = [
            'miembros' => $equipo->usuarios->count(),
            'torneos' => $equipo->inscripciones->count(),
            'capitan' => $equipo->capitan?->name,
        ];

        return view('equipos.show', compact('equipo', 'resumen'));
    }

    public function historial(Equipo $equipo): View
    {
        $partidos = Partido::with(['torneo', 'equipo1', 'equipo2', 'equipoGanador'])
            ->where(function ($query) use ($equipo) {
                $query->where('id_equipo1', $equipo->id_equipo)
                    ->orWhere('id_equipo2', $equipo->id_equipo);
            })
            ->orderByDesc('ronda')
            ->orderByDesc('id_partido')
            ->get();

        $resumen = [
            'jugados' => $partidos->whereNotNull('resultado_equipo1')->whereNotNull('resultado_equipo2')->count(),
            'victorias' => $partidos->where('ganador', $equipo->id_equipo)->count(),
            'derrotas' => $partidos->whereNotNull('ganador')->where('ganador', '!=', $equipo->id_equipo)->count(),
            'empates' => $partidos
                ->whereNull('ganador')
                ->whereNotNull('resultado_equipo1')
                ->whereNotNull('resultado_equipo2')
                ->count(),
        ];

        $torneosDisputados = Inscripcion::with('torneo')
            ->where('id_equipo', $equipo->id_equipo)
            ->get()
            ->map(function (Inscripcion $inscripcion) use ($equipo, $partidos) {
                $torneo = $inscripcion->torneo;
                $partidosTorneo = $partidos->where('id_torneo', $torneo->id_torneo);
                $jugados = $partidosTorneo->whereNotNull('resultado_equipo1')->whereNotNull('resultado_equipo2')->count();
                $victorias = $partidosTorneo->where('ganador', $equipo->id_equipo)->count();
                $derrotas = $partidosTorneo->whereNotNull('ganador')->where('ganador', '!=', $equipo->id_equipo)->count();
                $empates = $partidosTorneo
                    ->whereNull('ganador')
                    ->whereNotNull('resultado_equipo1')
                    ->whereNotNull('resultado_equipo2')
                    ->count();

                return [
                    'nombre' => $torneo->nombre,
                    'juego' => $torneo->juego,
                    'tipo' => $torneo->tipo_torneo,
                    'estado' => $torneo->estado,
                    'fecha_inicio' => $torneo->fecha_inicio,
                    'jugados' => $jugados,
                    'victorias' => $victorias,
                    'empates' => $empates,
                    'derrotas' => $derrotas,
                ];
            })
            ->sortByDesc('fecha_inicio')
            ->values();

        return view('equipos.historial', compact('equipo', 'partidos', 'resumen', 'torneosDisputados'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre_equipo' => ['required', 'string', 'max:255'],
        ]);

        if (Auth::user()->equipos()->exists()) {
            return back()->with('error', 'Ya formas parte de un equipo. Sal de tu equipo actual antes de crear uno nuevo.');
        }

        $equipo = Equipo::create([
            'nombre_equipo' => $request->nombre_equipo,
            'id_capitan' => Auth::id(),
        ]);

        $equipo->usuarios()->attach(Auth::id());

        return redirect('/equipos')->with('success', 'El equipo se ha creado correctamente y ya eres su capitan.');
    }

    public function unirse(int $id): RedirectResponse
    {
        $equipo = Equipo::findOrFail($id);
        $user = Auth::user();

        if ($user->equipos()->exists()) {
            return back()->with('error', 'Ya formas parte de un equipo y no puedes unirte a otro.');
        }

        $necesitaNuevaInvitacion = EquipoBaja::where('id_equipo', $equipo->id_equipo)
            ->where('id_usuario', $user->id)
            ->exists();

        if ($necesitaNuevaInvitacion) {
            return back()->with('error', 'Ya habias salido de este equipo. Solo puedes volver si el capitan te envia una nueva invitacion.');
        }

        if (! $equipo->usuarios->contains($user->id)) {
            $equipo->usuarios()->attach($user->id);
        }

        return redirect('/equipos')->with('success', 'Te has unido al equipo correctamente.');
    }

    public function expulsarMiembro(Equipo $equipo, int $usuarioId): RedirectResponse
    {
        $capitan = Auth::user();

        if ((int) $equipo->id_capitan !== (int) $capitan->id) {
            abort(403, 'Solo el capitan puede gestionar los miembros del equipo.');
        }

        if ((int) $usuarioId === (int) $capitan->id) {
            return back()->with('error', 'No puedes expulsarte a ti mismo desde aqui. Usa la opcion de salir del equipo.');
        }

        if (! $equipo->usuarios()->where('users.id', $usuarioId)->exists()) {
            return back()->with('error', 'El usuario seleccionado ya no pertenece a este equipo.');
        }

        $equipo->usuarios()->detach($usuarioId);

        return back()->with('success', 'El miembro ha sido expulsado del equipo correctamente.');
    }

    public function salir(Equipo $equipo): RedirectResponse
    {
        $user = Auth::user();

        if (! $equipo->usuarios()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'No puedes salir de este equipo porque no formas parte de el.');
        }

        EquipoBaja::updateOrCreate([
            'id_equipo' => $equipo->id_equipo,
            'id_usuario' => $user->id,
        ]);

        $equipo->usuarios()->detach($user->id);

        if ((int) $equipo->id_capitan === (int) $user->id) {
            $nuevoCapitan = $equipo->usuarios()->orderBy('users.id')->first();

            if (! $nuevoCapitan) {
                $equipo->delete();

                return back()->with('success', 'Has salido del equipo y el equipo se ha eliminado porque no quedaban miembros.');
            }

            $equipo->update([
                'id_capitan' => $nuevoCapitan->id,
            ]);

            return back()->with('success', 'Has salido del equipo y el capitan ahora es '.$nuevoCapitan->name.'.');
        }

        return back()->with('success', 'Has salido del equipo correctamente.');
    }
}
