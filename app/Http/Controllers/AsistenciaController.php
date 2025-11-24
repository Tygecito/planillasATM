<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia;
use App\Models\Marcacion; 
use App\Models\Retraso;
use App\Models\Empleado;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MarcacionesImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // <-- Necesario para la transacción
use Illuminate\Support\Facades\Log; 
use Exception;

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
     * --- APLICACIÓN DE TRANSACCIONES ---
     */
    public function import(Request $request)
    {
        // 1. Validación de Request (fuera de la transacción de DB)
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'mes_a_procesar' => 'required|integer|min:1|max:12',
            'anio_a_procesar' => 'required|integer|min:2020|max:' . (date('Y') + 1),
        ]);

        $archivo = $request->file('excel_file');
        $mes = (int)$request->input('mes_a_procesar');
        $anio = (int)$request->input('anio_a_procesar');

        $conteoRetrasos = 0;
        
        try {
            // --- INICIO DE LA TRANSACCIÓN CRÍTICA ---
            DB::transaction(function () use ($mes, $anio, $archivo, &$conteoRetrasos) {
                
                // 1. Limpiamos marcaciones antiguas (Parte de la operación atómica)
                Marcacion::whereMonth('fecha_hora', $mes)->whereYear('fecha_hora', $anio)->delete();
                
                // 2. Ejecutamos la importación. 
                $import = new MarcacionesImport($mes, $anio);
                // Si la importación falla (ej. error de datos en la fila 50), 
                // se lanza una excepción, y el DB::transaction ejecuta ROLLBACK.
                Excel::import($import, $archivo);
                
                // 3. Obtenemos el conteo final de retrasos
                $conteoRetrasos = $import->conteoRetrasos; 
            }); 
            // Si el código llega aquí, DB::commit() fue exitoso.

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Manejamos errores específicos de Maatwebsite (Ej: formato de número, fila vacía).
            // La transacción ya hizo ROLLBACK por la excepción lanzada.
            $failures = $e->failures();
            $error = "Error de formato en la fila " . $failures[0]->row() . ": " . $failures[0]->errors()[0];
            Log::error('Error de validación en Import: ' . $error);
            return redirect()->route('asistencias.index')->with('danger', 'Error en la importación: ' . $error);
        
        } catch (\Throwable $e) {
            // Manejamos cualquier otro error de DB o lógico.
            // La transacción ya hizo ROLLBACK.
            Log::error('Error crítico procesando el archivo: ' . $e->getMessage() . ' en línea: ' . $e->getLine());
            return redirect()->route('asistencias.index')->with('danger', 'Fallo crítico durante la importación. Ningún dato fue guardado.');
        }

        // Respuesta de éxito
        return redirect()->route('asistencias.index')->with('success', 
            "Archivo subido. Se procesaron las marcaciones del {$mes}/{$anio}. Se encontraron {$conteoRetrasos} retrasos."
        );
    }
    
    // --- Módulo de Reportes ---

    /**
     * Muestra la vista con el formulario de filtro para el reporte.
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
    public function generarReporte(Request $request)
    {
        $request->validate([
            'mes_reporte' => 'required|integer|min:1|max:12',
            'anio_reporte' => 'required|integer|min:2020|max:' . (date('Y') + 1),
        ]);

        $mes = $request->input('mes_reporte');
        $anio = $request->input('anio_reporte');

        $resultados = Retraso::selectRaw('empleado_id, tipo, COUNT(*) as total_retrasos, SUM(minutos_retraso) as total_minutos')
                             ->with('empleado')
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