<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class NominaController extends Controller
{
    /**
     * Mostrar listado de nóminas con filtros
     */
    public function index(Request $request)
    {
        $query = Nomina::with('empleado');
        
        // Aplicar filtros si existen
        if ($request->has('mes') && !empty($request->mes)) {
            $query->where('mes', $request->mes);
        }
        
        if ($request->has('anio') && !empty($request->anio)) {
            $query->where('anio', $request->anio);
        }
        
        $nominas = $query->orderBy('anio', 'desc')
                        ->orderByRaw("FIELD(mes, 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre')")
                        ->paginate(10);
        
        return view('nominas.index', compact('nominas'));
    }

    /**
     * Mostrar formulario para crear una nueva nómina
     */
    public function create()
    {
        $empleados = Empleado::where('estado', 1)->get();
        return view('nominas.create', compact('empleados'));
    }

    /**
     * Guardar múltiples nóminas en la BD
     */
    public function store(Request $request)
    {
        $request->validate([
            'mes' => 'required|string|max:20',
            'anio' => 'required|integer|min:2000|max:2100',
            'dias_pagados' => 'required|integer|min:1|max:31',
            'horas_pagadas' => 'required|integer|min:0',
            'empleados' => 'required|array',
            'empleados.*.empleado_id' => 'required|integer|exists:empleados,id',
            'empleados.*.smn' => 'nullable|numeric|min:0',
            'empleados.*.haber_basico' => 'required|numeric|min:0',
            'empleados.*.horas_extras' => 'nullable|integer|min:0',
            'empleados.*.bono_antiguedad' => 'nullable|numeric|min:0',
            'empleados.*.trabajo_extraordinario' => 'nullable|numeric|min:0',
            'empleados.*.pago_domingo' => 'nullable|numeric|min:0',
            'empleados.*.otros_bonos' => 'nullable|numeric|min:0',
            'empleados.*.aporte_laboral' => 'nullable|numeric|min:0',
            'empleados.*.aporte_nacional_solidario' => 'nullable|numeric|min:0',
            'empleados.*.rc_iva' => 'nullable|numeric|min:0',
            'empleados.*.anticipos' => 'nullable|numeric|min:0',
        ]);

        $nominasCreadas = 0;

        foreach ($request->empleados as $empleadoId => $datos) {
            // Calcular campos derivados usando haber_basico directamente
            $totalGanado = $datos['haber_basico'] + 
                          ($datos['bono_antiguedad'] ?? 0) + 
                          ($datos['trabajo_extraordinario'] ?? 0) + 
                          ($datos['pago_domingo'] ?? 0) + 
                          ($datos['otros_bonos'] ?? 0);
            
            $totalDescuentos = ($datos['aporte_laboral'] ?? 0) + 
                              ($datos['aporte_nacional_solidario'] ?? 0) + 
                              ($datos['rc_iva'] ?? 0) + 
                              ($datos['anticipos'] ?? 0);
            
            $liquido = $totalGanado - $totalDescuentos;

            // Crear la nómina SIN salario_ganado
            Nomina::create([
                'empleado_id' => $datos['empleado_id'],
                'mes' => $request->mes,
                'anio' => $request->anio,
                'smn' => $datos['smn'] ?? null,
                'haber_basico' => $datos['haber_basico'],
                'horas_pagadas' => $request->horas_pagadas,
                'horas_extras' => $datos['horas_extras'] ?? 0,
                'dias_pagados' => $request->dias_pagados,
                // 'salario_ganado' eliminado - usamos haber_basico directamente
                'bono_antiguedad' => $datos['bono_antiguedad'] ?? 0,
                'trabajo_extraordinario' => $datos['trabajo_extraordinario'] ?? 0,
                'pago_domingo' => $datos['pago_domingo'] ?? 0,
                'otros_bonos' => $datos['otros_bonos'] ?? 0,
                'total_ganado' => $totalGanado,
                'aporte_laboral' => $datos['aporte_laboral'] ?? 0,
                'aporte_nacional_solidario' => $datos['aporte_nacional_solidario'] ?? 0,
                'rc_iva' => $datos['rc_iva'] ?? 0,
                'anticipos' => $datos['anticipos'] ?? 0,
                'total_descuentos' => $totalDescuentos,
                'liquido' => $liquido
            ]);

            $nominasCreadas++;
        }

        return redirect()->route('nominas.index')
            ->with('success', "Se crearon {$nominasCreadas} nóminas correctamente.");
    }

    /**
     * Mostrar detalles de una nómina específica
     */
    public function show($id)
    {
        $nominaDetalle = Nomina::with('empleado')->findOrFail($id);
        
        $query = Nomina::with('empleado');
        
        if (request()->has('mes') && !empty(request()->mes)) {
            $query->where('mes', request()->mes);
        }
        
        if (request()->has('anio') && !empty(request()->anio)) {
            $query->where('anio', request()->anio);
        }
        
        $nominas = $query->orderBy('anio', 'desc')
                        ->orderByRaw("FIELD(mes, 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre')")
                        ->paginate(10);
        
        return view('nominas.index', compact('nominas', 'nominaDetalle'));
    }

    /**
     * Mostrar formulario de edición de nómina
     */
    public function edit($id)
    {
        $nomina = Nomina::findOrFail($id);
        $empleados = Empleado::where('estado', 1)->get();
        return view('nominas.edit', compact('nomina', 'empleados'));
    }

    /**
     * Actualizar nómina en la BD
     */
    public function update(Request $request, $id)
    {
        $nomina = Nomina::findOrFail($id);
        
        $request->validate([
            'empleado_id' => 'required|integer|exists:empleados,id',
            'mes' => 'required|string|max:20',
            'anio' => 'required|integer|min:2000|max:2100',
            'smn' => 'nullable|numeric|min:0',
            'haber_basico' => 'required|numeric|min:0',
            'horas_pagadas' => 'required|integer|min:0',
            'horas_extras' => 'nullable|integer|min:0',
            'dias_pagados' => 'required|integer|min:0|max:31',
            // 'salario_ganado' eliminado de las validaciones
            'bono_antiguedad' => 'nullable|numeric|min:0',
            'trabajo_extraordinario' => 'nullable|numeric|min:0',
            'pago_domingo' => 'nullable|numeric|min:0',
            'otros_bonos' => 'nullable|numeric|min:0',
            'aporte_laboral' => 'nullable|numeric|min:0',
            'aporte_nacional_solidario' => 'nullable|numeric|min:0',
            'rc_iva' => 'nullable|numeric|min:0',
            'anticipos' => 'nullable|numeric|min:0',
        ]);

        // Calcular campos derivados usando haber_basico directamente
        $total_ganado = $request->haber_basico + 
                       ($request->bono_antiguedad ?? 0) + 
                       ($request->trabajo_extraordinario ?? 0) + 
                       ($request->pago_domingo ?? 0) + 
                       ($request->otros_bonos ?? 0);
        
        $total_descuentos = ($request->aporte_laboral ?? 0) + 
                           ($request->aporte_nacional_solidario ?? 0) + 
                           ($request->rc_iva ?? 0) + 
                           ($request->anticipos ?? 0);
        
        $liquido = $total_ganado - $total_descuentos;

        // Actualizar la nómina SIN salario_ganado
        $nomina->update([
            'empleado_id' => $request->empleado_id,
            'mes' => $request->mes,
            'anio' => $request->anio,
            'smn' => $request->smn ?? null,
            'haber_basico' => $request->haber_basico,
            'horas_pagadas' => $request->horas_pagadas,
            'horas_extras' => $request->horas_extras ?? 0,
            'dias_pagados' => $request->dias_pagados,
            // 'salario_ganado' eliminado
            'bono_antiguedad' => $request->bono_antiguedad ?? 0,
            'trabajo_extraordinario' => $request->trabajo_extraordinario ?? 0,
            'pago_domingo' => $request->pago_domingo ?? 0,
            'otros_bonos' => $request->otros_bonos ?? 0,
            'total_ganado' => $total_ganado,
            'aporte_laboral' => $request->aporte_laboral ?? 0,
            'aporte_nacional_solidario' => $request->aporte_nacional_solidario ?? 0,
            'rc_iva' => $request->rc_iva ?? 0,
            'anticipos' => $request->anticipos ?? 0,
            'total_descuentos' => $total_descuentos,
            'liquido' => $liquido
        ]);

        return redirect()->route('nominas.index')
            ->with('success', 'Nómina actualizada correctamente.');
    }

    /**
     * Eliminar nómina de la BD
     */
    public function destroy($id)
    {
        $nomina = Nomina::findOrFail($id);
        $nomina->delete();

        return redirect()->route('nominas.index')
            ->with('success', 'Nómina eliminada correctamente.');
    }

    /**
     * Descargar nómina en formato PDF
     */
    public function download($id)
    {
        $nomina = Nomina::with('empleado')->findOrFail($id);
        
        $pdf = PDF::loadView('nominas.pdf', compact('nomina'));
        
        return $pdf->download('nomina-'.$nomina->empleado->primerapellido.'-'.$nomina->mes.'-'.$nomina->anio.'.pdf');
    }
}