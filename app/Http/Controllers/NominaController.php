<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use Illuminate\Http\Request;

class NominaController extends Controller
{
    /**
     * Mostrar listado de nóminas
     */
    public function index()
    {
        $nominas = Nomina::all();
        return view('nominas.index', compact('nominas'));
    }

    /**
     * Mostrar formulario para crear una nueva nómina
     */
    public function create()
    {
        return view('nominas.create');
    }

    /**
     * Guardar una nueva nómina en la BD
     */
    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|integer|exists:empleados,id',
            'mes' => 'required|string|max:20',
            'anio' => 'required|integer',
            'salario_base' => 'required|numeric',
            'descuentos' => 'nullable|numeric',
            'bonos' => 'nullable|numeric',
            'total' => 'required|numeric',
        ]);

        Nomina::create($request->all());

        return redirect()->route('nominas.index')
            ->with('success', 'Nómina creada correctamente.');
    }

    /**
     * Mostrar formulario de edición de nómina
     */
    public function edit(Nomina $nomina)
    {
        return view('nominas.edit', compact('nomina'));
    }

    /**
     * Actualizar nómina en la BD
     */
    public function update(Request $request, Nomina $nomina)
    {
        $request->validate([
            'empleado_id' => 'required|integer|exists:empleados,id',
            'mes' => 'required|string|max:20',
            'anio' => 'required|integer',
            'salario_base' => 'required|numeric',
            'descuentos' => 'nullable|numeric',
            'bonos' => 'nullable|numeric',
            'total' => 'required|numeric',
        ]);

        $nomina->update($request->all());

        return redirect()->route('nominas.index')
            ->with('success', 'Nómina actualizada correctamente.');
    }

    /**
     * Eliminar nómina de la BD
     */
    public function destroy(Nomina $nomina)
    {
        $nomina->delete();

        return redirect()->route('nominas.index')
            ->with('success', 'Nómina eliminada correctamente.');
    }
}
