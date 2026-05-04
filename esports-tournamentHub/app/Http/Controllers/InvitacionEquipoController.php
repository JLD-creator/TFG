<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\InvitacionEquipo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitacionEquipoController extends Controller
{
    public function store(Request $request, Equipo $equipo): RedirectResponse
    {
        $user = Auth::user();

        if ((int) $equipo->id_capitan !== (int) $user->id) {
            abort(403, 'Solo el capitan puede invitar jugadores a este equipo.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $invitado = User::where('email', $validated['email'])->first();

        if (! $invitado) {
            return back()->with('error', 'No existe ningun usuario registrado con ese email.');
        }

        if ((int) $invitado->id === (int) $user->id) {
            return back()->with('error', 'No puedes invitarte a ti mismo.');
        }

        if ($equipo->usuarios()->where('users.id', $invitado->id)->exists()) {
            return back()->with('error', 'Ese usuario ya pertenece al equipo.');
        }

        if ($invitado->equipos()->exists()) {
            return back()->with('error', 'Ese usuario ya pertenece a otro equipo.');
        }

        $invitacionPendiente = InvitacionEquipo::where('id_equipo', $equipo->id_equipo)
            ->where('id_usuario_invitado', $invitado->id)
            ->where('estado', 'pendiente')
            ->exists();

        if ($invitacionPendiente) {
            return back()->with('error', 'Ya existe una invitacion pendiente para ese usuario.');
        }

        InvitacionEquipo::create([
            'id_equipo' => $equipo->id_equipo,
            'id_usuario_invitado' => $invitado->id,
            'id_usuario_invitador' => $user->id,
            'estado' => 'pendiente',
        ]);

        return back()->with('success', 'Invitacion enviada correctamente.');
    }

    public function accept(InvitacionEquipo $invitacion): RedirectResponse
    {
        $user = Auth::user();

        if ((int) $invitacion->id_usuario_invitado !== (int) $user->id) {
            abort(403, 'No puedes aceptar una invitacion que no te pertenece.');
        }

        if ($invitacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta invitacion ya fue procesada.');
        }

        if ($user->equipos()->exists()) {
            return back()->with('error', 'Ya perteneces a un equipo.');
        }

        $invitacion->equipo->usuarios()->attach($user->id);
        $invitacion->update(['estado' => 'aceptada']);

        InvitacionEquipo::where('id_usuario_invitado', $user->id)
            ->where('estado', 'pendiente')
            ->where('id_invitacion', '!=', $invitacion->id_invitacion)
            ->update(['estado' => 'rechazada']);

        return back()->with('success', 'Te has unido al equipo correctamente.');
    }

    public function reject(InvitacionEquipo $invitacion): RedirectResponse
    {
        $user = Auth::user();

        if ((int) $invitacion->id_usuario_invitado !== (int) $user->id) {
            abort(403, 'No puedes rechazar una invitacion que no te pertenece.');
        }

        if ($invitacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta invitacion ya fue procesada.');
        }

        $invitacion->update(['estado' => 'rechazada']);

        return back()->with('success', 'Invitacion rechazada.');
    }
}
