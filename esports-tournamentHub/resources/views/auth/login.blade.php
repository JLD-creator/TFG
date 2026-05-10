@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="glass-card p-4 p-lg-5">
                <h1 class="h2 fw-bold mb-3">Iniciar sesion</h1>
                <p class="text-muted">Accede para gestionar tus equipos, torneos y resultados.</p>

                <form method="POST" action="/login" class="mt-4">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="tu@email.com">
                    </div>

                    <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input id="password" class="form-control" type="password" name="password" placeholder="Introduce tu contraseña">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Login</button>
                        <a href="/register" class="btn btn-outline-light">Crear una cuenta</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
