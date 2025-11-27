<?php

namespace App\Http\Controllers; // <-- El namespace correcto

use Illuminate\Http\Request;
use App\Models\Empleado;  // Para Empleados Activos
use App\Models\Permiso;   // Para Empleados en Vacaciones
use App\Models\Feriado;   // Para Próximos Feriados
use Carbon\Carbon;        // Para manejo de fechas

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Configuración y Fechas
        // Configuramos Carbon para obtener el día de la semana en español
        Carbon::setLocale('es');
        $hoy = Carbon::today()->toDateString();
        
        // ===============================================
        // 2. Lógica Estadística
        // ===============================================

        // A. Total de Empleados Activos
        $totalEmpleadosActivos = Empleado::where('estado', 'activo')->count();
        
        // B. Personas en Vacaciones (Permisos Aprobados hoy)
        // Busca permisos de tipo 'vacaciones' que estén 'aprobados' y activos hoy
        $empleadosEnVacaciones = Permiso::where('tipo_permiso', 'vacaciones')
            ->where('estado', 'aprobado')
            ->where('fecha_inicio', '<=', $hoy)
            ->where('fecha_fin', '>=', $hoy)
            ->distinct('empleado_id') 
            ->count();
            
        // C. Próximos Feriados (Desde la Base de Datos)
        $proximosFeriados = Feriado::where('fecha', '>=', $hoy)
            ->orderBy('fecha', 'asc')
            ->limit(3)
            ->get()
            ->map(function ($feriado) {
                // Usamos Carbon para obtener el día de la semana (ej: Lunes)
                $carbonFecha = Carbon::parse($feriado->fecha);
                
                return [
                    'fecha' => $feriado->fecha,
                    'dia_semana' => $carbonFecha->isoFormat('dddd'), 
                    'nombre' => $feriado->nombre,
                ];
            });

        // ===============================================
        // 3. Devolver la Vista con las Variables
        // ===============================================
        return view('dashboard', [
            // Estas variables se usarán en dashboard.blade.php
            'totalEmpleadosActivos' => $totalEmpleadosActivos,
            'empleadosEnVacaciones' => $empleadosEnVacaciones,
            'proximosFeriados' => $proximosFeriados,
        ]);
    }
}