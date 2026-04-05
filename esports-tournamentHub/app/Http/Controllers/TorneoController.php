<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Torneo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TorneoController extends Controller
{
    public function index(): View
    {
        $torneos = Torneo::all();

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
        ]);

        Torneo::create([
            'nombre' => $request->nombre,
            'juego' => $request->juego,
            'tipo_torneo' => $request->tipo_torneo,
            'fecha_inicio' => $request->fecha_inicio,
            'estado' => 'abierto',
        ]);

        return redirect('/torneos');
    }

    public function inscribirse(int $id): RedirectResponse
    {
        $equipo = Auth::user()->equipos()->first();
        $torneo = Torneo::findOrFail($id);

        if (! $equipo) {
            return back()->with('error', 'No tienes equipo');
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

        return redirect('/torneos');
    }
}
