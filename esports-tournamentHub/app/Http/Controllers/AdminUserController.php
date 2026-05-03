<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'rol' => ['required', Rule::in(['admin', 'organizador', 'jugador'])],
        ]);

        if ($request->user()->is($user) && $validated['rol'] !== 'admin') {
            return back()->with('error', 'No puedes quitarte a ti mismo el rol de administrador.');
        }

        $user->update([
            'rol' => $validated['rol'],
        ]);

        return back()->with('success', 'Rol actualizado correctamente.');
    }
}
