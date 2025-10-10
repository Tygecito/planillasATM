<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Empleado; // Necesitas este modelo para acceder a la lógica de saldo
use Illuminate\Http\Request;
use Carbon\Carbon; // Importamos Carbon

/**
 * Función auxiliar para calcular días hábiles (Lunes a Viernes).
 * NOTA: Colocada fuera de la clase para ser accesible directamente.
 * @param string $fechaInicio
 * @param string $fechaFin
 * @return float
 */
function calcularDiasHabiles(string $fechaInicio, string $fechaFin): float
{
    $inicio = Carbon::parse($fechaInicio);
    $fin = Carbon::parse($fechaFin);
    
    if ($inicio->greaterThan($fin)) {
        return 0.0;
    }

    $diasHabiles = 0.0;
    $currentDate = $inicio->copy();

    while ($currentDate->lessThanOrEqualTo($fin)) {
        // isWeekday() verifica si el día no es sábado (6) ni domingo (0)
        if ($currentDate->isWeekday()) {
            $diasHabiles += 1.0;
        }
        $currentDate->addDay();
    }

    return $diasHabiles;
}

class PermisoController extends Controller
{
    /**
     * Muestra una lista de todos los recursos.
     */
    public function index()
    {
        $permisos = Permiso::with('empleado')->get(); // Cargamos la relación del empleado
        return view('permisos.index', compact('permisos'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        // === CAMBIO CRÍTICO: FILTRAR SOLO EMPLEADOS ACTIVOS (estado = 1) ===
        $empleados = Empleado::select('id', 'nombres', 'primerapellido', 'segundoapellido', 'estado')
                             ->where('estado', 1) // Filtramos directamente en la base de datos
                             ->get();
        // ===================================================================
        return view('permisos.create', compact('empleados'));
    }

    /**
     * Almacena un recurso recién creado en la base de datos.
     * Esta función contiene la lógica de validación de saldo y cálculo de días.
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN
        $request->validate([
            'empleado_id' => 'required|integer|exists:empleados,id',
            'tipo_permiso' => 'required|in:VACACION,PERMISO_REMUNERADO,PERMISO_POR_HORAS,LICENCIA_MEDICA,OTRO',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'nullable|string|max:500',
            // Reglas condicionales para PERMISO_POR_HORAS
            'hora_inicio' => 'nullable|required_if:tipo_permiso,PERMISO_POR_HORAS|date_format:H:i',
            'hora_fin' => 'nullable|required_if:tipo_permiso,PERMISO_POR_HORAS|date_format:H:i|after:hora_inicio',
        ]);
        
        $empleado = Empleado::findOrFail($request->empleado_id);
        $dias_a_deducir = 0.0;
        $duracion_en_horas = null;

        // 2. CÁLCULO DE DÍAS Y HORAS
        if ($request->tipo_permiso === 'PERMISO_POR_HORAS') {
            $jornada_laboral_hrs = 8.0; 
            
            $horaInicio = Carbon::parse($request->hora_inicio);
            $horaFin = Carbon::parse($request->hora_fin);
            $duracion_en_horas = $horaFin->diffInMinutes($horaInicio) / 60;
            
            $dias_a_deducir = round($duracion_en_horas / $jornada_laboral_hrs, 3);

        } elseif (in_array($request->tipo_permiso, ['VACACION', 'PERMISO_REMUNERADO'])) {
            $dias_a_deducir = calcularDiasHabiles($request->fecha_inicio, $request->fecha_fin);
        }
        
        // 3. VALIDACIÓN DE SALDO (Solo si son VACACIONES)
        if ($request->tipo_permiso === 'VACACION') {
            $saldo_actual = $empleado->getSaldoVacaciones();

            if ($dias_a_deducir > $saldo_actual) {
                return redirect()->back()->withInput()->withErrors([
                    'saldo' => "El empleado solo tiene " . number_format($saldo_actual, 3) . " días de saldo disponibles."
                ]);
            }
        }

        // 4. CREAR SOLICITUD
        Permiso::create([
            'empleado_id' => $request->empleado_id,
            'tipo_permiso' => $request->tipo_permiso,
            'fecha_solicitud' => Carbon::now()->toDateString(),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'duracion_horas' => $duracion_en_horas,
            'dias_solicitados' => $dias_a_deducir,
            'motivo' => $request->motivo,
            'estado' => 'PENDIENTE',
        ]);

        return redirect()->route('permisos.index')
                         ->with('success', 'Permiso creado y enviado a aprobación correctamente. Días solicitados: ' . number_format($dias_a_deducir, 3));
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show(Permiso $permiso)
    {
        // Carga la información del empleado relacionado
        $permiso->load('empleado'); 
        return view('permisos.show', compact('permiso'));
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(Permiso $permiso)
    {
        // Aquí no filtramos por estado, ya que necesitamos que el empleado actual siempre aparezca.
        $empleados = Empleado::select('id', 'nombres', 'primerapellido', 'segundoapellido')->get(); 
        return view('permisos.edit', compact('permiso', 'empleados'));
    }

    /**
     * Actualiza el recurso especificado en la base de datos.
     * Esta función también debe recalcular los días solicitados y revalidar el saldo si es necesario.
     */
    public function update(Request $request, Permiso $permiso)
    {
        // 1. VALIDACIÓN
        $request->validate([
            'empleado_id' => 'required|integer|exists:empleados,id',
            'tipo_permiso' => 'required|in:VACACION,PERMISO_REMUNERADO,PERMISO_POR_HORAS,LICENCIA_MEDICA,OTRO',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'nullable|string|max:500',
            // Reglas condicionales para PERMISO_POR_HORAS
            'hora_inicio' => 'nullable|required_if:tipo_permiso,PERMISO_POR_HORAS|date_format:H:i',
            'hora_fin' => 'nullable|required_if:tipo_permiso,PERMISO_POR_HORAS|date_format:H:i|after:hora_inicio',
            'estado' => 'required|in:PENDIENTE,APROBADO,RECHAZADO,CANCELADO',
        ]);
        
        $empleado = Empleado::findOrFail($request->empleado_id);
        $dias_a_deducir = 0.0;
        $duracion_en_horas = null;
        
        // 2. RE-CÁLCULO DE DÍAS Y HORAS
        if ($request->tipo_permiso === 'PERMISO_POR_HORAS') {
            $jornada_laboral_hrs = 8.0; 
            
            $horaInicio = Carbon::parse($request->hora_inicio);
            $horaFin = Carbon::parse($request->hora_fin);
            $duracion_en_horas = $horaFin->diffInMinutes($horaInicio) / 60;
            
            $dias_a_deducir = round($duracion_en_horas / $jornada_laboral_hrs, 3);
            
        } elseif (in_array($request->tipo_permiso, ['VACACION', 'PERMISO_REMUNERADO'])) {
            $dias_a_deducir = calcularDiasHabiles($request->fecha_inicio, $request->fecha_fin);
        }
        
        // 3. VALIDACIÓN AVANZADA DE SALDO (Solo si es VACACION y está APROBADO o se está APROBANDO)
        if ($request->tipo_permiso === 'VACACION' && $request->estado === 'APROBADO') {
            
            // Usamos getSaldoVacaciones(permiso_id) para excluir el permiso actual del cálculo, 
            // asegurando que la validación se haga sobre el saldo real sin duplicidades.
            $saldo_total_sin_este_permiso = $empleado->getSaldoVacaciones($permiso->id);
            
            if ($dias_a_deducir > $saldo_total_sin_este_permiso) {
                return redirect()->back()->withInput()->withErrors([
                    'saldo' => "La actualización excede el saldo. Saldo disponible: " . number_format($saldo_total_sin_este_permiso, 3) . " días."
                ]);
            }
        }
        
        // 4. ACTUALIZAR SOLICITUD
        $permiso->update([
            'empleado_id' => $request->empleado_id,
            'tipo_permiso' => $request->tipo_permiso,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'duracion_horas' => $duracion_en_horas,
            'dias_solicitados' => $dias_a_deducir,
            'motivo' => $request->motivo,
            'estado' => $request->estado,
            // Aquí puedes añadir 'aprobado_por' si el estado es 'APROBADO'
        ]);

        return redirect()->route('permisos.index')
                         ->with('success', 'Permiso actualizado correctamente.');
    }

    /**
     * Elimina el recurso especificado de la base de datos.
     */
    public function destroy(Permiso $permiso)
    {
        $permiso->delete();

        return redirect()->route('permisos.index')
                         ->with('success', 'Permiso eliminado correctamente.');
    }
}
