@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="glass-card p-4 p-lg-5">
                <h1 class="display-6 fw-bold mb-3">Introducir resultado</h1>
                <p class="text-muted">
                    {{ $partido->equipo1?->nombre_equipo ?? 'Equipo 1' }} vs {{ $partido->equipo2?->nombre_equipo ?? 'Equipo 2' }}
                </p>

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

                <form method="POST" action="/partidos/{{ $partido->id_partido }}/resultado" class="mt-4">
                    @csrf

                    <div class="mb-3">
                        <label for="resultado_equipo1" class="form-label">{{ $partido->equipo1?->nombre_equipo ?? 'Equipo 1' }}</label>
                        <input
                            id="resultado_equipo1"
                            class="form-control"
                            type="number"
                            name="resultado_equipo1"
                            min="0"
                            value="{{ old('resultado_equipo1', $partido->resultado_equipo1) }}"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="resultado_equipo2" class="form-label">{{ $partido->equipo2?->nombre_equipo ?? 'Equipo 2' }}</label>
                        <input
                            id="resultado_equipo2"
                            class="form-control"
                            type="number"
                            name="resultado_equipo2"
                            min="0"
                            value="{{ old('resultado_equipo2', $partido->resultado_equipo2) }}"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="/torneos" class="btn btn-outline-light ms-2">Volver</a>
                </form>
            </div>
        </div>
    </div>
@endsection
