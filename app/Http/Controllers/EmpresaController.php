<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Mostrar listado de empresas.
     */
    public function index()
    {
        $empresas = Empresa::all();
        return view('empresas.index', compact('empresas'));
    }

    /**
     * Formulario para crear una nueva empresa.
     */
    public function create()
    {
        return view('empresas.create');
    }

    /**
     * Guardar nueva empresa en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => 'required|string|max:20|unique:empresas,nit',
            'ciudad' => 'required|string|max:100',
            'numero_patronal' => 'required|string|max:20',
            'gestion' => 'required|integer',
            'nro_empleador_min_trab' => 'required|string|max:20',
            'tipo_empresa' => 'required|string|max:50',
            'representante_legal' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'ci_representante_legal' => 'required|string|max:20',
            'mes' => 'required|string|max:20',
            'salario_minimo_nacional' => 'required|decimal:0,2',
            'email' => 'required|string|email|max:100',
            'telefono' => 'required|string|max:20',
        ]);

        // Crear la empresa y asignar las fechas manualmente
        Empresa::create(array_merge($request->all(), [
            'fecha_creacion' => now(),
            'fecha_modificacion' => now(),
        ]));

        return redirect()->route('empresas.index')
                         ->with('success', 'Empresa creada correctamente');
    }

    /**
     * Formulario para editar una empresa.
     */
    public function edit(Empresa $empresa)
    {
        return view('empresas.edit', compact('empresa'));
    }

    /**
     * Actualizar empresa en la base de datos.
     */
    public function update(Request $request, $id)
{
    $empresa = Empresa::findOrFail($id);

    $request->validate([
        'nombre' => 'required|string|max:255',
        'nit' => 'required|string|max:20|unique:empresas,nit,' . $empresa->id,
        'ciudad' => 'required|string|max:100',
        'numero_patronal' => 'required|string|max:20',
        'nro_empleador_min_trab' => 'required|string|max:20',
        'gestion' => 'required|integer',
        'tipo_empresa' => 'required|string|max:50',
        'representante_legal' => 'required|string|max:255',
        'direccion' => 'required|string|max:255',
        'ci_representante_legal' => 'required|string|max:20',
        'mes' => 'required|string|max:20',
        'salario_minimo_nacional' => 'required|numeric',
        'email' => 'required|string|email|max:100',
        'telefono' => 'required|string|max:20',
    ]);

    // Actualiza los datos de la empresa
    $empresa->update($request->all());

    return redirect()->route('empresas.index')
                     ->with('success', 'Empresa actualizada correctamente');
}

    /**
     * Eliminar empresa.
     */
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();

        return redirect()->route('empresas.index')
                         ->with('success', 'Empresa eliminada correctamente');
    }
}