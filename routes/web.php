<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\PlanillaController;
use App\Http\Controllers\AsistenciaController; 
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\SubsidioController; 
use App\Http\Controllers\FeriadoController; // <<< 1. NUEVA INCLUSIÓN
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
    Route::get('nominas/{id}/download', [NominaController::class, 'download'])->name('nominas.download'); 

    // Módulo de Planillas
    Route::resource('planillas', PlanillaController::class)->only(['index']); 
    Route::get('planillas/generar/{tipo}/{formato}', [PlanillaController::class, 'generar'])->name('planillas.generar');

    // =================================================================
    // MÓDULO DE ASISTENCIA: RUTA ESPECÍFICA DE REPORTE DEBE IR PRIMERO
    // =================================================================
    
    // 1. Rutas ESPECÍFICAS (Reporte e Importación)
    
    // GET para mostrar el formulario de filtro del reporte
    Route::get('asistencias/reporte', [AsistenciaController::class, 'showReporte'])->name('asistencias.reporte');
    
    // POST para procesar el filtro y mostrar los datos del reporte
    Route::post('asistencias/reporte', [AsistenciaController::class, 'generarReporte'])->name('asistencias.generar');
    
    // POST para la importación del archivo (de tu formulario)
    Route::post('asistencias/importar', [AsistenciaController::class, 'import'])->name('asistencias.import');

    // 2. Ruta RESOURCE GENÉRICA (Debe ir al final para evitar el conflicto)
    Route::resource('asistencias', AsistenciaController::class)->only(['index']);


    // Módulo de Permisos (sustituye a vacaciones)
    Route::resource('permisos', PermisoController::class);
    
    Route::get('permisos/reporte/vacaciones', [PermisoController::class, 'reporteVacaciones'])
    ->name('permisos.reporte.vacaciones');
    
    // =================================================================
    // >>> 2. NUEVA RUTA: MÓDULO DE FERIADOS (CRUD) <<<
    // =================================================================
    Route::resource('feriados', FeriadoController::class);
    
    // Módulo de Usuarios (CRUD completo)
    Route::resource('usuarios', UsuarioController::class);
    
    // --- GRUPO DE SUBSIDIOS ACTUALIZADO ---
    
    // GET (Reporte)
    Route::get('subsidios/reporte/{empleado}', [SubsidioController::class, 'reportePorEmpleado'])
                 ->name('subsidios.reporte');
    
    // GET (Crear)
    Route::get('subsidios/crear/{empleado}', [SubsidioController::class, 'create'])
                 ->name('subsidios.create');

    // POST (Guardar)
    Route::post('subsidios', [SubsidioController::class, 'store'])
                 ->name('subsidios.store');
                 
    // GET (Editar)
    Route::get('subsidios/{subsidio}/edit', [SubsidioController::class, 'edit'])
                 ->name('subsidios.edit');
    
    // PUT/PATCH (Actualizar)
    Route::put('subsidios/{subsidio}', [SubsidioController::class, 'update'])
                 ->name('subsidios.update');

    // DELETE (Eliminar)
    Route::delete('subsidios/{subsidio}', [SubsidioController::class, 'destroy'])
                 ->name('subsidios.destroy');
    // --- FIN ---
});