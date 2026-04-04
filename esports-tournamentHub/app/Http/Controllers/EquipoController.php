<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EquipoController extends Controller
{
    public function index(): View
    {
        $equipos = Equipo::all();

        return view('equipos.index', compact('equipos'));
    }

    public function create(): View
    {
        return view('equipos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre_equipo' => ['required', 'string', 'max:255'],
        ]);

        $equipo = Equipo::create([
            'nombre_equipo' => $request->nombre_equipo,
            'id_capitan' => Auth::id(),
        ]);

        $equipo->usuarios()->attach(Auth::id());

        return redirect('/equipos');
    }

    public function unirse(int $id): RedirectResponse
    {
        $equipo = Equipo::findOrFail($id);

        if (! $equipo->usuarios->contains(Auth::id())) {
            $equipo->usuarios()->attach(Auth::id());
        }

        return redirect('/equipos');
    }
}
