@extends('layouts.app')

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="glass-card p-4 p-lg-5">
                <h1 class="display-6 fw-bold mb-3">Mi perfil</h1>
                <p class="text-muted">Actualiza tus datos personales y cambia tu contrasena cuando lo necesites.</p>

                @if (session('success'))
                    <div class="alert alert-success">
                        <p class="mb-0">{{ session('success') }}</p>
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

                <form method="POST" action="/perfil" class="mt-4">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input
                            id="name"
                            class="form-control"
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            placeholder="Tu nombre"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            id="email"
                            class="form-control"
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            placeholder="tu@email.com"
                            required
                        >
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Contrasena actual</label>
                                <input
                                    id="current_password"
                                    class="form-control"
                                    type="password"
                                    name="current_password"
                                    placeholder="Solo si cambias la contrasena"
                                >
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="password" class="form-label">Nueva contrasena</label>
                                <input
                                    id="password"
                                    class="form-control"
                                    type="password"
                                    name="password"
                                    placeholder="Minimo 8 caracteres"
                                >
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar contrasena</label>
                                <input
                                    id="password_confirmation"
                                    class="form-control"
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="Repite la nueva contrasena"
                                >
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="/dashboard" class="btn btn-outline-light ms-2">Volver</a>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h2 class="h4 fw-bold mb-3">Resumen de cuenta</h2>
                <div class="mb-3">
                    <div class="small text-uppercase text-muted">Rol</div>
                    <div class="fs-5 fw-semibold text-capitalize">{{ $user->rol }}</div>
                </div>

                <div class="mb-3">
                    <div class="small text-uppercase text-muted">Equipos</div>
                    @if ($user->equipos->isEmpty())
                        <p class="mb-0 text-muted">Todavia no perteneces a ningun equipo.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($user->equipos as $equipo)
                                <li class="list-group-item">{{ $equipo->nombre_equipo }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div>
                    <div class="small text-uppercase text-muted">Cuenta creada</div>
                    <div class="fw-semibold">{{ $user->created_at?->format('d/m/Y') ?? 'Sin fecha' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
