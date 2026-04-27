@extends('layouts.app')

@section('content')
    <div class="glass-card p-4 p-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge text-bg-success mb-3 px-3 py-2">Proyecto TFG</span>
                <h1 class="display-4 fw-bold">Plataforma de Torneos eSports</h1>
                <p class="lead text-muted">
                    Organiza equipos, crea torneos, genera brackets y registra resultados en una sola aplicacion.
                    La idea es que cualquier profesor pueda entrar y entender el flujo en pocos clics.
                </p>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="/torneos" class="btn btn-primary btn-lg">Ver torneos</a>
                    <a href="/equipos" class="btn btn-outline-light btn-lg">Ver equipos</a>
                    @auth
                        <a href="/dashboard" class="btn btn-outline-info btn-lg">Abrir dashboard</a>
                    @else
                        <a href="/login" class="btn btn-outline-info btn-lg">Entrar</a>
                    @endauth
                </div>
            </div>

            <div class="col-lg-6 text-center">
                <img class="hero-image img-fluid" src="{{ asset('images/foto.png') }}" alt="Logo de eSports Tournament Hub">
            </div>
        </div>
    </div>
@endsection
