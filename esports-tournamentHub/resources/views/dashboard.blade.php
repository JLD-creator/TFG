@extends('layouts.app')

@section('content')
    <div class="glass-card p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h1 class="display-6 fw-bold mb-2">Dashboard</h1>
                <p class="text-muted mb-0">Aqui tienes un panel rapido para moverte por la app sin perderte.</p>
            </div>
            <div class="glass-soft p-3">
                <div class="small text-uppercase text-muted">Usuario actual</div>
                <div class="fs-5 fw-semibold">{{ auth()->user()->name }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Modulo</div>
                <h3 class="fw-bold">Equipos</h3>
                <p class="text-muted">Crea tu equipo, unete a uno existente y prepara la inscripcion a torneos.</p>
                <a href="/equipos" class="dashboard-link">Ir a equipos</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Modulo</div>
                <h3 class="fw-bold">Torneos</h3>
                <p class="text-muted">Consulta torneos, inscribe equipos y genera brackets automaticamente.</p>
                <a href="/torneos" class="dashboard-link">Ir a torneos</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4 h-100 metric-card">
                <div class="small text-uppercase text-muted">Flujo</div>
                <h3 class="fw-bold">Siguiente paso</h3>
                <p class="text-muted">El orden recomendado es: crear equipo, crear o elegir torneo, generar bracket y subir resultados.</p>
                <a href="/torneos" class="dashboard-link">Empezar ahora</a>
            </div>
        </div>
    </div>
@endsection
