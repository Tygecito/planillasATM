<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia;
use App\Models\Marcacion; 
use App\Models\Retraso;   
use App\Models\Empleado; 
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MarcacionesImport; // La clase que hace la importación y el cálculo
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 

class AsistenciaController extends Controller
{
    /**
     * Muestra la vista principal (index.blade.php) y pasa los retrasos recientes.
     */
    public function index()
    {
        $retrasos = Retraso::with('empleado') 
                           ->orderBy('fecha', 'desc')
                           ->take(20)
                           ->get();
                           
        return view('asistencias.index', [
            'retrasos' => $retrasos 
        ]);
    }

    /**
     * Procesa la carga del archivo Excel y ejecuta el cálculo de retrasos.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'mes_a_procesar' => 'required|integer|min:1|max:12',
            'anio_a_procesar' => 'required|integer|min:2020|max:' . (date('Y') + 1),
        ]);

        $archivo = $request->file('excel_file');
        $mes = (int)$request->input('mes_a_procesar');
        $anio = (int)$request->input('anio_a_procesar');

        $conteo = 0;
        
        try {
            // Usamos una transacción para asegurar la integridad de los datos
            DB::transaction(function () use ($mes, $anio, $archivo, &$conteo) {
                
                // Limpiamos marcaciones antiguas para este periodo (SOLUCIÓN DUPLICACIÓN)
                Marcacion::whereMonth('fecha_hora', $mes)->whereYear('fecha_hora', $anio)->delete();
                
                // 1. Creamos la instancia de Import
                $import = new MarcacionesImport($mes, $anio);
                
                // 2. Ejecutamos la importación (El cálculo se dispara automáticamente por AfterImport)
                Excel::import($import, $archivo);
                
                // 3. Obtenemos el conteo que se calculó DENTRO de la clase
                $conteo = $import->conteoRetrasos;

            }); 
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $error = "Error en la fila " . $failures[0]->row() . ": " . $failures[0]->errors()[0];
             Log::error('Error de validación en Import: ' . $error, $failures);
             return redirect()->route('asistencias.index')->with('danger', 'Error en la importación: ' . $error);
        
        } catch (\Exception $e) {
            Log::error('Error general en Import: ' . $e->getMessage());
            return redirect()->route('asistencias.index')->with('danger', 'Error procesando el archivo: ' . $e->getMessage());
        }

        return redirect()->route('asistencias.index')->with('success', 
            "Archivo subido. Se procesaron las marcaciones del {$mes}/{$anio}. Se encontraron {$conteo} retrasos."
        );
    }
    
    // --- Módulo de Reportes ---

    /**
     * Muestra la vista con el formulario de filtro para el reporte.
     * Corrige el error "Undefined variable $mes" inicializando a null.
     */
    public function showReporte()
    {
        $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
        
        $resultados = collect(); 
        $mes = null; 
        $anio = null; 

        return view('asistencias.reporte', compact('resultados', 'mes', 'anio', 'meses'));
    }

    /**
     * Procesa el filtro y genera el reporte de retrasos.
     */
    // En AsistenciaController.php, reemplazar el método generarReporte

public function generarReporte(Request $request)
{
    $request->validate([
        'mes_reporte' => 'required|integer|min:1|max:12',
        'anio_reporte' => 'required|integer|min:2020|max:' . (date('Y') + 1),
    ]);

    $mes = $request->input('mes_reporte');
    $anio = $request->input('anio_reporte');

    // --- CONSULTA CORREGIDA Y ROBUSTA ---
    $resultados = Retraso::selectRaw('empleado_id, tipo, COUNT(*) as total_retrasos, SUM(minutos_retraso) as total_minutos')
                        ->with('empleado')
                        // Usamos whereRaw para forzar la comparación numérica de las partes de la fecha
                        ->whereRaw('MONTH(fecha) = ? AND YEAR(fecha) = ?', [$mes, $anio])
                        ->groupBy('empleado_id', 'tipo')
                        ->orderBy('empleado_id', 'asc')
                        ->get();
                        
    $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];

    return view('asistencias.reporte', compact('resultados', 'mes', 'anio', 'meses'));
}
    
    // --- Métodos vacíos del 'resource' ---
    public function create() { }
    public function store(Request $request) { }
    public function show(Asistencia $asistencia) { } 
    public function edit(Asistencia $asistencia) { }
    public function update(Request $request, Asistencia $asistencia) { }
    public function destroy(Asistencia $asistencia) { }
}