<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Empresa; // Asegúrate de importar el modelo de Empresa
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Empleado::query();

        // Filtrar por empresa si se proporciona
        if ($request->has('empresa_id') && $request->empresa_id) {
            $query->where('empresa_id', $request->empresa_id);
        }

        // Filtrar por búsqueda si se proporciona
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'LIKE', "%{$search}%")
                  ->orWhere('primerapellido', 'LIKE', "%{$search}%")
                  ->orWhere('segundoapellido', 'LIKE', "%{$search}%")
                  ->orWhere('documento_identidad', 'LIKE', "%{$search}%")
                  ->orWhere('cargo_laboral', 'LIKE', "%{$search}%");
            });
        }

        $empresas = Empresa::all(); // Obtener todas las empresas
        $empleados = $query->get(); // Obtener los empleados filtrados

        return view('empleados.index', compact('empleados', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::all(); // Obtener todas las empresas
        return view('empleados.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|integer', // Validar que se envíe el ID de la empresa
            'nombres' => 'required|string|max:100',
            'primerapellido' => 'required|string|max:100',
            'segundoapellido' => 'nullable|string|max:100',
            'sucursal' => 'required|boolean',
            'fecha_ingreso' => 'required|date',
            'caja_de_salud' => 'nullable|in:Caja Nacional de Salud,Caja Bancaria Estatal de Salud,Caja de Salud de la Banca Privada,Caja Petrolera de Salud',
            'tipo_de_contrato' => 'nullable|in:Contrato escrito,Contrato verbal',
            'modalidad_contrato' => 'nullable|in:Contrato por tiempo indefinido,Contrato a plazo fijo,Contrato por temporada,Contrato por obra o servicio,Contrato de teletrabajo',
            'cargo_laboral' => 'required|string|max:100',
            'fecha_de_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F',
            'estado_civil' => 'nullable|in:Soltero,Casado,Divorciado,Viudo,Unión libre',
            'documento_identidad' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'foto' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
        ]);

        Empleado::create($request->all());
        return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente');
    }

    public function edit(Empleado $empleado)
    {
        $empresas = Empresa::all(); // Obtener todas las empresas para el formulario de edición
        return view('empleados.edit', compact('empleado', 'empresas'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'primerapellido' => 'required|string|max:100',
            'segundoapellido' => 'nullable|string|max:100',
            'sucursal' => 'required|boolean',
            'fecha_ingreso' => 'required|date',
            'caja_de_salud' => 'nullable|in:Caja Nacional de Salud,Caja Bancaria Estatal de Salud,Caja de Salud de la Banca Privada,Caja Petrolera de Salud',
            'tipo_de_contrato' => 'nullable|in:Contrato escrito,Contrato verbal',
            'modalidad_contrato' => 'nullable|in:Contrato por tiempo indefinido,Contrato a plazo fijo,Contrato por temporada,Contrato por obra o servicio,Contrato de teletrabajo',
            'cargo_laboral' => 'required|string|max:100',
            'fecha_de_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F',
            'estado_civil' => 'nullable|in:Soltero,Casado,Divorciado,Viudo,Unión libre',
            'documento_identidad' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'foto' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
        ]);

        $empleado->update($request->all());
        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado correctamente');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();
        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente');
    }
}