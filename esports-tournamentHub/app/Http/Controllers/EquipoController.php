<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\InvitacionEquipo;
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

        return view('equipos.index', compact('equipos', 'invitacionesPendientes'));
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

        if (Auth::user()->equipos()->exists()) {
            return back()->with('error', 'Ya perteneces a un equipo y no puedes crear otro.');
        }

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
        $user = Auth::user();

        if ($user->equipos()->exists()) {
            return back()->with('error', 'Ya perteneces a un equipo.');
        }

        if (! $equipo->usuarios->contains($user->id)) {
            $equipo->usuarios()->attach($user->id);
        }

        return redirect('/equipos');
    }

    public function expulsarMiembro(Equipo $equipo, int $usuarioId): RedirectResponse
    {
        $capitan = Auth::user();

        if ((int) $equipo->id_capitan !== (int) $capitan->id) {
            abort(403, 'Solo el capitan puede gestionar los miembros del equipo.');
        }

        if ((int) $usuarioId === (int) $capitan->id) {
            return back()->with('error', 'No puedes expulsarte a ti mismo. Usa la opcion de salir del equipo.');
        }

        if (! $equipo->usuarios()->where('users.id', $usuarioId)->exists()) {
            return back()->with('error', 'Ese usuario no pertenece al equipo.');
        }

        $equipo->usuarios()->detach($usuarioId);

        return back()->with('success', 'Miembro expulsado del equipo.');
    }

    public function salir(Equipo $equipo): RedirectResponse
    {
        $user = Auth::user();

        if (! $equipo->usuarios()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'No perteneces a este equipo.');
        }

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
