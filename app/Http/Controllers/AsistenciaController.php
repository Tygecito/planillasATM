<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra la vista principal para la carga y gestión de asistencia.
     */
    public function index()
    {
        // **ESTO HACE QUE TU VISTA index.blade.php SE RENDERICE**
        return view('asistencias.index');
    }

    /**
     * Procesa la carga del archivo Excel con marcaciones del biométrico.
     * Esta función maneja la subida del archivo y la recepción del filtro de mes/año.
     */
    public function import(Request $request)
    {
        // 1. Validación de los campos del formulario
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Archivo requerido, tipos permitidos y tamaño máximo (10MB)
            'mes_a_procesar' => 'required|integer|min:1|max:12',
            'anio_a_procesar' => 'required|integer|min:2020|max:' . (date('Y') + 1), // Limita el año para evitar errores
        ]);

        // 2. Obtener los datos del filtro
        $archivo = $request->file('excel_file');
        $mes = $request->input('mes_a_procesar');
        $anio = $request->input('anio_a_procesar');

        // 3. Lógica de procesamiento de Excel (Añadirás la lógica de la librería aquí, como Laravel-Excel)
        // Por ejemplo, si usaras Maatwebsite/Laravel-Excel:
        // Excel::import(new TuClaseDeImportacion($mes, $anio), $archivo);

        // 4. Mensaje de éxito y redirección
        return redirect()->route('asistencias.index')->with('success', 
            "El archivo '{$archivo->getClientOriginalName()}' fue subido. 
             Se procederá a procesar las marcaciones del **Mes {$mes} / Año {$anio}**."
        );
    }
    
    /**
     * Show the form for creating a new resource. (No usado para esta funcionalidad)
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage. (Podría usarse para import, pero usamos 'import' como ruta personalizada)
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Asistencia $asistencia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asistencia $asistencia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asistencia $asistencia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asistencia $asistencia)
    {
        //
    }
}