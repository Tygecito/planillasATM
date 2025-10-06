<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\PlanillaController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas por middleware 'auth'
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Módulo de Empresas (CRUD completo)
    Route::resource('empresas', EmpresaController::class);

    // Módulo de Empleados (CRUD completo)
    Route::resource('empleados', EmpleadoController::class);

    // Módulo de Nóminas
    Route::resource('nominas', NominaController::class);
    Route::get('nominas/{id}/download', [NominaController::class, 'download'])->name('nominas.download'); // ← AÑADE ESTA LÍNEA

    // Módulo de Planillas
    Route::resource('planillas', PlanillaController::class);

    // Módulo de Asistencia
    Route::resource('asistencias', AsistenciaController::class);

    // Módulo de Permisos (sustituye a vacaciones)
    Route::resource('permisos', PermisoController::class);

    // Módulo de Usuarios (CRUD completo)
    Route::resource('usuarios', UsuarioController::class);
});