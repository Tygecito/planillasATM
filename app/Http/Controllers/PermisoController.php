<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Empleado; 
use App\Models\Feriado; 
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // <-- NECESARIO para Transacciones
use Illuminate\Support\Facades\Log; 
use Throwable; // Para capturar cualquier tipo de error de transacción

class PermisoController extends Controller
{
    /**
     * Función interna para calcular días hábiles (Lunes a Viernes) excluyendo feriados.
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return float
     */
    private function calcularDiasHabiles(string $fechaInicio, string $fechaFin): float
    {
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);
        
        if ($inicio->greaterThan($fin)) {
            return 0.0;
        }

        $diasHabiles = 0.0;
        $currentDate = $inicio->copy();

        $fechasExcluidas = Feriado::whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
                                 ->pluck('fecha')
                                 ->map(fn($date) => Carbon::parse($date)->toDateString())
                                 ->toArray();

        while ($currentDate->lessThanOrEqualTo($fin)) {
            $fechaString = $currentDate->toDateString();
            
            if ($currentDate->isWeekday()) {
                if (!in_array($fechaString, $fechasExcluidas)) {
                    $diasHabiles += 1.0;
                }
            }
            $currentDate->addDay();
        }

        return $diasHabiles;
    }


    /**
     * Muestra una lista de todos los recursos (Solicitudes de Permiso).
     */
    public function index()
    {
        $permisos = Permiso::with('empleado')->get();
        return view('permisos.index', compact('permisos'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $empleados = Empleado::select('id', 'nombres', 'primerapellido', 'segundoapellido', 'estado')
                             ->where('estado', 1)
                             ->get();
        return view('permisos.create', compact('empleados'));
    }

    /**
     * Almacena un recurso recién creado en la base de datos.
     * PROTEGIDO POR TRANSACCIÓN (Crea el permiso).
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
            $dias_a_deducir = $this->calcularDiasHabiles($request->fecha_inicio, $request->fecha_fin);
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

        // 4. CREAR SOLICITUD - ENVUELTA EN TRANSACCIÓN
        DB::beginTransaction();

        try {
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

            DB::commit(); 

            return redirect()->route('permisos.index')
                             ->with('success', 'Permiso creado y enviado a aprobación correctamente. Días solicitados: ' . number_format($dias_a_deducir, 3));
        
        } catch (Throwable $e) {
            DB::rollBack(); 
            Log::error("Fallo al crear permiso: " . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Error al crear el permiso. Por favor, intente de nuevo.');
        }
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show(Permiso $permiso)
    {
        $permiso->load('empleado'); 
        return view('permisos.show', compact('permiso'));
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(Permiso $permiso)
    {
        $empleados = Empleado::select('id', 'nombres', 'primerapellido', 'segundoapellido')->get(); 
        return view('permisos.edit', compact('permiso', 'empleados'));
    }

    /**
     * Actualiza el recurso especificado en la base de datos.
     * PROTEGIDO POR TRANSACCIÓN (Actualiza estado y valida saldo).
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
            'hora_inicio' => 'nullable|required_if:tipo_permiso,PERMISO_POR_HORAS|date_format:H:i',
            'hora_fin' => 'nullable|required_if:tipo_permiso,PERMISO_POR_HORAS|date_format:H:i|after:hora_inicio',
            'estado' => 'required|in:PENDIENTE,APROBADO,RECHAZADO,CANCELADO',
        ]);
        
        $empleado = Empleado::findOrFail($request->empleado_id);
        $dias_a_deducir = 0.0;
        $duracion_en_horas = null;
        $nuevo_estado = $request->estado;
        $estado_anterior = $permiso->estado;
        
        // 2. RE-CÁLCULO DE DÍAS Y HORAS
        if ($request->tipo_permiso === 'PERMISO_POR_HORAS') {
            $jornada_laboral_hrs = 8.0; 
            $horaInicio = Carbon::parse($request->hora_inicio);
            $horaFin = Carbon::parse($request->hora_fin);
            $duracion_en_horas = $horaFin->diffInMinutes($horaInicio) / 60;
            $dias_a_deducir = round($duracion_en_horas / $jornada_laboral_hrs, 3);
        } elseif (in_array($request->tipo_permiso, ['VACACION', 'PERMISO_REMUNERADO'])) {
            $dias_a_deducir = $this->calcularDiasHabiles($request->fecha_inicio, $request->fecha_fin);
        }
        
        // 3. VALIDACIÓN AVANZADA DE SALDO (Solo si es VACACION y se está APROBANDO)
        if ($request->tipo_permiso === 'VACACION' && $nuevo_estado === 'APROBADO' && $estado_anterior !== 'APROBADO') {
            
            // Excluimos este permiso del cálculo del saldo para evitar doble conteo
            $saldo_total_sin_este_permiso = $empleado->getSaldoVacaciones($permiso->id); 
            
            if ($dias_a_deducir > $saldo_total_sin_este_permiso) {
                return redirect()->back()->withInput()->withErrors([
                    'saldo' => "La actualización excede el saldo. Saldo disponible: " . number_format($saldo_total_sin_este_permiso, 3) . " días."
                ]);
            }
        }
        
        // 4. ACTUALIZAR SOLICITUD - ENVUELTA EN TRANSACCIÓN
        DB::beginTransaction();

        try {
            // Actualizar el registro del permiso
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
                'estado' => $nuevo_estado,
            ]);
            
            // Aquí iría la lógica para MODIFICAR EL SALDO DEL EMPLEADO (restar/devolver días)
            // Ya que no tenemos el método, esto es un placeholder de la lógica:
            /* if ($nuevo_estado === 'APROBADO' && $estado_anterior !== 'APROBADO') {
                 $empleado->decrement('saldo_vacaciones', $dias_a_deducir);
            } elseif ($nuevo_estado === 'RECHAZADO' && $estado_anterior === 'APROBADO') {
                 $empleado->increment('saldo_vacaciones', $permiso->dias_solicitados); // Devolver días
            }
            */
            
            DB::commit(); 

            return redirect()->route('permisos.index')
                             ->with('success', 'Permiso actualizado correctamente.');
        
        } catch (Throwable $e) {
            DB::rollBack(); 
            Log::error("Fallo al actualizar permiso: " . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Error al actualizar el permiso. Por favor, intente de nuevo.');
        }
    }

    /**
     * Elimina el recurso especificado de la base de datos.
     * PROTEGIDO POR TRANSACCIÓN (Elimina el permiso).
     */
    public function destroy(Permiso $permiso)
    {
        DB::beginTransaction();

        try {
            // Lógica para devolver días si el estado era APROBADO antes de eliminar
            /*
            if ($permiso->estado === 'APROBADO' && $permiso->tipo_permiso === 'VACACION') {
                 $permiso->empleado->increment('saldo_vacaciones', $permiso->dias_solicitados);
            }
            */
            
            $permiso->delete();

            DB::commit();
            
            return redirect()->route('permisos.index')
                             ->with('success', 'Permiso eliminado correctamente.');
        
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Fallo al eliminar permiso: " . $e->getMessage());
            return redirect()->route('permisos.index')
                             ->with('error', 'Error al eliminar el permiso. Por favor, intente de nuevo.');
        }
    }
    
    /**
     * Muestra el reporte del saldo de vacaciones de todos los empleados.
     */
    public function reporteVacaciones()
    {
        $empleados = Empleado::where('estado', 1)
                             ->orderBy('primerapellido')
                             ->get();

        return view('permisos.reporte_vacaciones', compact('empleados'));
    }
}