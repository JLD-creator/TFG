<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\TorneoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/dashboard', function () {
    return 'Bienvenido ' . auth()->user()->name;
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/equipos', [EquipoController::class, 'index']);
    Route::get('/equipos/create', [EquipoController::class, 'create']);
    Route::post('/equipos', [EquipoController::class, 'store']);

    Route::post('/equipos/{id}/unirse', [EquipoController::class, 'unirse']);

    Route::get('/torneos', [TorneoController::class, 'index']);
    Route::get('/torneos/create', [TorneoController::class, 'create']);
    Route::post('/torneos', [TorneoController::class, 'store']);

    Route::post('/torneos/{id}/inscribirse', [TorneoController::class, 'inscribirse']);
    Route::post('/torneos/{id}/bracket', [TorneoController::class, 'generarBracket']);
});
