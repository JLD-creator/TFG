@extends('layouts.app')

@section('content')
    <div class="glass-card p-4 p-lg-5">
        <h1 class="display-6 fw-bold mb-3">Crear torneo</h1>
        <p class="text-muted">Define la competicion y dejala lista para inscripciones y bracket.</p>

        <form method="POST" action="/torneos" class="mt-4">
            @csrf

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del torneo</label>
                <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Spring Invitational" value="{{ old('nombre') }}">
            </div>

            <div class="mb-3">
                <label for="juego" class="form-label">Juego</label>
                <input id="juego" class="form-control" type="text" name="juego" placeholder="Valorant, LoL, FIFA..." value="{{ old('juego') }}">
            </div>

            <div class="mb-3">
                <label for="tipo_torneo" class="form-label">Tipo de torneo</label>
                <select id="tipo_torneo" class="form-select" name="tipo_torneo">
                    <option value="eliminacion" @selected(old('tipo_torneo') === 'eliminacion')>Eliminacion directa</option>
                    <option value="liga" @selected(old('tipo_torneo') === 'liga')>Liga</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                <input id="fecha_inicio" class="form-control" type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
            </div>

            <div class="mb-3">
                <label for="normas" class="form-label">Normas del torneo</label>
                <textarea
                    id="normas"
                    class="form-control"
                    name="normas"
                    rows="5"
                    placeholder="Ejemplo: formato BO3, horario de check-in, prohibiciones, criterios de desempate..."
                >{{ old('normas') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Crear</button>
            <a href="/torneos" class="btn btn-outline-light ms-2">Volver</a>
        </form>
    </div>
@endsection
