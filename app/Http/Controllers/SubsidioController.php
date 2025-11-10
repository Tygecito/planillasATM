<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Subsidio;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class SubsidioController extends Controller
{
    /**
     * Muestra el reporte de subsidios para un empleado específico.
     */
    public function reportePorEmpleado(Empleado $empleado)
    {
        $empleado->load(['subsidios' => function ($query) {
            $query->orderBy('anio', 'desc')->orderByRaw("FIELD(mes, 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre')");
        }]);

        return view('subsidios.reporte', compact('empleado'));
    }

    /**
     * Muestra el formulario para crear un nuevo subsidio para un empleado.
     */
    public function create(Empleado $empleado)
    {
        return view('subsidios.create', compact('empleado'));
    }

    /**
     * Guarda el nuevo subsidio en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|integer|exists:empleados,id',
            'tipo_subsidio' => ['required', Rule::in(['PRENATAL', 'NATALIDAD', 'LACTANCIA'])],
            'monto' => 'required|numeric|min:2500', 
            'mes' => 'required|string|max:20',
            'anio' => 'required|integer|min:2020|max:' . date('Y'),
            'estado' => ['required', Rule::in(['POR PAGAR', 'PAGADO'])], 
        ]);

        Subsidio::create($request->all());

        return redirect()->route('subsidios.reporte', $request->empleado_id)
                         ->with('success', 'Subsidio registrado correctamente.');
    }

    /**
     * Muestra el formulario para editar un subsidio.
     */
    public function edit(Subsidio $subsidio)
    {
        $empleado = $subsidio->empleado; 
        return view('subsidios.edit', compact('subsidio', 'empleado'));
    }

    /**
     * Actualiza un subsidio en la base de datos.
     */
    public function update(Request $request, Subsidio $subsidio)
    {
        $validatedData = $request->validate([
            'tipo_subsidio' => ['required', Rule::in(['PRENATAL', 'NATALIDAD', 'LACTANCIA'])],
            'monto' => 'required|numeric|min:2500', 
            'mes' => 'required|string|max:20',
            'anio' => 'required|integer|min:2020|max:' . date('Y'),
            'estado' => ['required', Rule::in(['POR PAGAR', 'PAGADO'])], 
        ]);

        $subsidio->update($validatedData);

        return redirect()->route('subsidios.reporte', $subsidio->empleado_id)
                         ->with('success', 'Subsidio actualizado correctamente.');
    }

    /**
     * Elimina un subsidio de la base de datos.
     */
    public function destroy(Subsidio $subsidio)
    {
        $empleado_id = $subsidio->empleado_id;
        $subsidio->delete();

        return redirect()->route('subsidios.reporte', $empleado_id)
                         ->with('success', 'Subsidio eliminado correctamente.');
    }
}