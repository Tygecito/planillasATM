<?php

namespace App\Http\Controllers;

use App\Models\Feriado;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class FeriadoController extends Controller
{
    /**
     * Display a listing of the resource. (Listado de Feriados, ordenado por fecha)
     */
    public function index()
    {
        // El ordenamiento ASCENDENTE por la columna DATE 'fecha' asegura el orden cronológico
        $feriados = Feriado::orderBy('fecha', 'asc')->get();
        return view('feriados.index', compact('feriados'));
    }

    /**
     * Show the form for creating a new resource. (Formulario de Creación)
     */
    public function create()
    {
        // Filtra solo empleados ACTIVOS (estado = 1, según tu BD)
        $empleados = Empleado::select('id', 'nombres', 'primerapellido', 'segundoapellido')
                            ->where('estado', 1) 
                            ->get();
        
        $tipos = ['NACIONAL', 'DEPARTAMENTAL', 'COLECTIVA', 'CUMPLEANOS'];
        
        return view('feriados.create', compact('empleados', 'tipos'));
    }

    /**
     * Store a newly created resource in storage. (Guarda el Feriado)
     */
    public function store(Request $request)
    {
        // Rango de fechas: 1 año antes y 1 año después de hoy
        $minDate = Carbon::now()->subYear()->toDateString();
        $maxDate = Carbon::now()->addYear()->toDateString();

        $validated = $request->validate([
            'fecha' => [
                'required',
                'date',
                'unique:feriados,fecha',
                // Validación de rango de fechas
                'after_or_equal:' . $minDate,
                'before_or_equal:' . $maxDate,
            ],
            // Validación: Solo letras y espacios (Regex con /u para Ñ y acentos)
            'descripcion' => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            
            'tipo' => 'required|in:NACIONAL,DEPARTAMENTAL,COLECTIVA,CUMPLEANOS',
            'empleado_id' => 'nullable|required_if:tipo,CUMPLEANOS|integer|exists:empleados,id',
        ]);
        
        // CONVERSIÓN: Usar mb_strtoupper para convertir a MAYÚSCULAS correctamente (incluye Ñ)
        $validated['descripcion'] = mb_strtoupper($validated['descripcion'], 'UTF-8');

        // Si el tipo no es CUMPLEANOS, aseguramos que empleado_id sea null
        if ($validated['tipo'] !== 'CUMPLEANOS') {
            $validated['empleado_id'] = null;
        }

        Feriado::create($validated);

        return redirect()->route('feriados.index')
                         ->with('success', 'Feriado creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feriado $feriado)
    {
        // Filtra solo empleados ACTIVOS (estado = 1)
        $empleados = Empleado::select('id', 'nombres', 'primerapellido', 'segundoapellido')
                            ->where('estado', 1) 
                            ->get();
        $tipos = ['NACIONAL', 'DEPARTAMENTAL', 'COLECTIVA', 'CUMPLEANOS'];

        return view('feriados.edit', compact('feriado', 'empleados', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feriado $feriado)
    {
        // Rango de fechas: 1 año antes y 1 año después de hoy
        $minDate = Carbon::now()->subYear()->toDateString();
        $maxDate = Carbon::now()->addYear()->toDateString();

        $validated = $request->validate([
            'fecha' => [
                'required',
                'date',
                // Ignora el ID actual para la regla de unicidad
                Rule::unique('feriados', 'fecha')->ignore($feriado->id), 
                // Validación de rango de fechas
                'after_or_equal:' . $minDate,
                'before_or_equal:' . $maxDate,
            ],
            // Validación: Solo letras y espacios
            'descripcion' => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            
            'tipo' => 'required|in:NACIONAL,DEPARTAMENTAL,COLECTIVA,CUMPLEANOS',
            'empleado_id' => 'nullable|required_if:tipo,CUMPLEANOS|integer|exists:empleados,id',
        ]);
        
        // CONVERSIÓN: Usar mb_strtoupper para convertir a MAYÚSCULAS correctamente
        $validated['descripcion'] = mb_strtoupper($validated['descripcion'], 'UTF-8');

        if ($validated['tipo'] !== 'CUMPLEANOS') {
            $validated['empleado_id'] = null;
        }

        $feriado->update($validated);

        return redirect()->route('feriados.index')
                         ->with('success', 'Feriado actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feriado $feriado)
    {
        $feriado->delete();

        return redirect()->route('feriados.index')
                         ->with('success', 'Feriado eliminado correctamente.');
    }
}