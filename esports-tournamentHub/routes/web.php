<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TorneoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/estadisticas', [EstadisticasController::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'edit']);
    Route::post('/perfil', [ProfileController::class, 'update']);

    Route::get('/equipos', [EquipoController::class, 'index']);
    Route::get('/torneos', [TorneoController::class, 'index']);
    Route::get('/torneos/{id}/bracket', [PartidoController::class, 'verBracket']);

    Route::middleware('role:jugador')->group(function () {
        Route::get('/equipos/create', [EquipoController::class, 'create']);
        Route::post('/equipos', [EquipoController::class, 'store']);
        Route::post('/equipos/{id}/unirse', [EquipoController::class, 'unirse']);
        Route::post('/torneos/{id}/inscribirse', [TorneoController::class, 'inscribirse']);
    });

    Route::middleware('role:organizador,admin')->group(function () {
        Route::get('/torneos/create', [TorneoController::class, 'create']);
        Route::post('/torneos', [TorneoController::class, 'store']);
        Route::post('/torneos/{id}/bracket', [TorneoController::class, 'generarBracket']);

        Route::get('/partidos/{id}/resultado', [PartidoController::class, 'edit']);
        Route::post('/partidos/{id}/resultado', [PartidoController::class, 'guardarResultado']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/usuarios', [AdminUserController::class, 'index']);
        Route::post('/admin/usuarios/{user}/rol', [AdminUserController::class, 'updateRole']);
    });
});
