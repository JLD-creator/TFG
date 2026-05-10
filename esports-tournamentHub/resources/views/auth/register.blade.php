@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="glass-card p-4 p-lg-5">
                <h1 class="h2 fw-bold mb-3">Registro de usuario</h1>
                <p class="text-muted">Crea una cuenta para empezar a usar la plataforma.</p>

                <form method="POST" action="/register" class="mt-4">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" placeholder="Tu nombre">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="tu@email.com">
                    </div>

                    <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input id="password" class="form-control" type="password" name="password" placeholder="Mínimo 8 caracteres">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Registrarse</button>
                        <a href="/login" class="btn btn-outline-light">Ya tienes cuenta? Inicia sesion</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
