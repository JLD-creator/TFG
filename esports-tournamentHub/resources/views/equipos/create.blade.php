@extends('layouts.app')

@section('content')
    <div class="glass-card p-4 p-lg-5">
        <h1 class="display-6 fw-bold mb-3">Crear equipo</h1>
        <p class="text-muted">Pon un nombre al equipo y quedará asociado a tu usuario como capitán.</p>

        <form method="POST" action="/equipos" class="mt-4">
            @csrf

            <div class="mb-3">
                <label for="nombre_equipo" class="form-label">Nombre del equipo</label>
                <input id="nombre_equipo" class="form-control" type="text" name="nombre_equipo" placeholder="Ejemplo: Nexus Wolves" value="{{ old('nombre_equipo') }}">
            </div>

            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="/equipos" class="btn btn-outline-light ms-2">Volver</a>
        </form>
    </div>
@endsection
