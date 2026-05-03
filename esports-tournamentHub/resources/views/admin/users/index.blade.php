@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Gestion de usuarios</h1>
            <p class="text-muted mb-0">Como administrador, desde aqui puedes asignar roles reales dentro de la plataforma.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            <p class="mb-0">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <p class="mb-0">{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol actual</th>
                        <th>Cambiar rol</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $listedUser)
                        <tr>
                            <td>{{ $listedUser->name }}</td>
                            <td>{{ $listedUser->email }}</td>
                            <td class="text-capitalize">{{ $listedUser->rol }}</td>
                            <td>
                                <form method="POST" action="/admin/usuarios/{{ $listedUser->id }}/rol" class="d-flex flex-column flex-md-row gap-2">
                                    @csrf
                                    <select name="rol" class="form-select" aria-label="Seleccionar rol de {{ $listedUser->name }}">
                                        <option value="jugador" @selected($listedUser->rol === 'jugador')>Jugador</option>
                                        <option value="organizador" @selected($listedUser->rol === 'organizador')>Organizador</option>
                                        <option value="admin" @selected($listedUser->rol === 'admin')>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
