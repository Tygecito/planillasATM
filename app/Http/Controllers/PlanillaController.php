<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Exception;

class PlanillaController extends Controller
{
    /**
     * Muestra la vista con los filtros para generar planillas.
     */
    public function index()
    {
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        
        return view('planillas.index', compact('meses'));
    }

    /**
     * Obtiene los datos de nóminas para el mes y año dados.
     */
    private function getDatosNomina(string $mes, int $anio)
    {
        return Nomina::with('empleado')
            ->where('mes', $mes)
            ->where('anio', $anio)
            ->get();
    }

    /**
     * Genera y descarga la Planilla en el formato solicitado.
     */
    public function generar(Request $request, string $tipo, string $formato)
    {
        // 1. Validación Estricta
        try {
            $request->validate([
                'mes' => 'required|string|in:Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre',
                'anio' => 'required|integer|min:2000|max:2100',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
             return back()->with('error', 'Error inesperado en la validación: ' . $e->getMessage());
        }

        $mes = $request->mes;
        $anio = $request->anio;

        $datosPlanilla = $this->getDatosNomina($mes, $anio);

        if ($datosPlanilla->isEmpty()) {
            return back()->with('error', 'No hay datos de nóminas para el periodo seleccionado (' . $mes . ' ' . $anio . ').')->withInput();
        }

        $extension = $formato === 'xlsx' ? 'csv' : $formato;
        $nombreArchivo = "planilla-{$tipo}-{$mes}-{$anio}.{$extension}";

        // 2. Generación del reporte con RASTREO DE ERRORES (try-catch)
        try {
            switch ($tipo) {
                case 'mensual':
                    return $this->generarPlanillaMensual($datosPlanilla, $mes, $anio, $formato, $nombreArchivo);
                
                // CORRECCIÓN: Llamar al nuevo método de RC IVA
                case 'rc_iva':
                    return $this->generarPlanillaRCIva($datosPlanilla, $mes, $anio, $formato, $nombreArchivo);
                
                case 'gestora':
                    return back()->with('error', 'Funcionalidad Gestora aún no implementada.');
                    
                default:
                    return back()->with('error', 'Tipo de planilla no reconocido.');
            }
        } catch (Exception $e) {
            // Si falla la generación (ej. DomPDF no está instalado), devuelve un CSV con el error.
            $csv = "ERROR_MESSAGE,FILE,LINE\n";
            $csv .= '"' . str_replace('"', '""', $e->getMessage()) . '",' . $e->getFile() . ',' . $e->getLine() . "\n";
            
            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="ERROR_PLANILLAS.csv"',
            ]);
        }
    }

    /**
     * Lógica para generar la Planilla Mensual (PDF o Excel/CSV).
     */
    private function generarPlanillaMensual($datosPlanilla, $mes, $anio, $formato, $nombreArchivo)
    {
        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('planillas.pdf.mensual', compact('datosPlanilla', 'mes', 'anio'));
            $pdf->setPaper('letter', 'landscape'); 
            return $pdf->download($nombreArchivo);
            
        } elseif ($formato === 'xlsx') {
            // Lógica de CSV (Excel) para Planilla Mensual... (código omitido por brevedad, asumo que ya funciona)
            $csv = $this->generarCsvMensual($datosPlanilla);
            
            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"', 
            ]);
        }

        throw new Exception("Formato de descarga '{$formato}' no soportado.");
    }
    
    /**
     * Lógica para generar la Planilla RC IVA (PDF o Excel/CSV).
     * NUEVO MÉTODO
     */
    private function generarPlanillaRCIva($datosPlanilla, $mes, $anio, $formato, $nombreArchivo)
    {
        if ($formato === 'pdf') {
            // Cargar la vista específica del RC IVA
            $pdf = Pdf::loadView('planillas.pdf.rc_iva', compact('datosPlanilla', 'mes', 'anio'));
            
            // Configuración Carta Horizontal para RC IVA
            $pdf->setPaper('letter', 'landscape'); 
            
            return $pdf->download($nombreArchivo);
            
        } elseif ($formato === 'xlsx') {
            // Generación simplificada de Excel (CSV) para RC IVA
            $csv = $this->generarCsvRCIva($datosPlanilla);
            
            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"', 
            ]);
        }

        throw new Exception("Formato de descarga '{$formato}' no soportado para RC IVA.");
    }
    
    /**
     * Función auxiliar para generar el contenido CSV de la Planilla Mensual
     */
    private function generarCsvMensual($datosPlanilla) {
        $columnas = ['DOC IDENTIDAD', 'PATERNO', 'MATERNO', 'NOMBRE', 'FECHA INGRESO', 'DIAS PAGADOS', 'HORAS PAGADAS', 'SUELDO BÁSICO', 'BONO ANTIGÜEDAD', 'TRABAJO EXTRA', 'PAGO DOMINGO', 'OTROS BONOS', 'TOTAL GANADO', 'RC IVA 13%', 'AFP 12.71%', 'OTROS DESCUENTOS', 'TOTAL DESCUENTO', 'LÍQUIDO PAGABLE'];
        $csv = implode(',', $columnas) . "\n";
        
        foreach ($datosPlanilla as $nomina) {
            $empleado = $nomina->empleado;
            $otrosDescuentos = ($nomina->anticipos ?? 0) + ($nomina->aporte_nacional_solidario ?? 0);
            
            $fila = [
                $empleado->documento_identidad,
                $empleado->primerapellido,
                $empleado->segundoapellido,
                $empleado->nombres,
                $empleado->fecha_ingreso,
                $nomina->dias_pagados,
                $nomina->horas_pagadas,
                $nomina->haber_basico,
                $nomina->bono_antiguedad,
                $nomina->trabajo_extraordinario,
                $nomina->pago_domingo,
                $nomina->otros_bonos,
                $nomina->total_ganado,
                $nomina->rc_iva,
                $nomina->aporte_laboral,
                number_format($otrosDescuentos, 2, '.', ''),
                $nomina->total_descuentos,
                $nomina->liquido
            ];
            $csv .= implode(',', $fila) . "\n";
        }
        return $csv;
    }
    
    /**
     * Función auxiliar para generar el contenido CSV de la Planilla RC IVA
     */
    private function generarCsvRCIva($datosPlanilla) {
        $columnas = ['NRO', 'CODIGO_DEP_RCIVA', 'PATERNO', 'MATERNO', 'NOMBRES', 'CI', 'TIPO_DOC', 'NOV_EDAD', 'BASE_IMPONIBLE', 'MINIMO_NO_IMPONIBLE', 'IMPORTE_SUJETO_IMPUESTO', 'RC_IVA_13', '2SMN_13', 'IMPUESTO_NETO', 'F110', 'SALDO_FAVOR_DEP', 'SALDO_ANTERIOR', 'SALDO_DEL_FISCO', 'SALDO_UTILIZADO', 'IMPUESTO_RETENIDO', 'SALDO_FINAL'];
        $csv = implode(',', $columnas) . "\n";
        
        // Valores Fijos para el cálculo
        $smn = 2750.00; 
        $smn_x_2 = $smn * 2;
        $credito_fijo = $smn_x_2 * 0.13;
        $tasa_rc_iva = 0.13;
        
        foreach ($datosPlanilla as $index => $nomina) {
            $tg = $nomina->total_ganado;
            $remuneracion_neta = $tg - ($nomina->aporte_laboral + $nomina->aporte_nacional_solidario); 
            $importe_sujeto_impuesto = max(0, $remuneracion_neta - $smn_x_2);
            $impuesto_rc_iva = $importe_sujeto_impuesto * $tasa_rc_iva; 
            $impuesto_neto = max(0, $impuesto_rc_iva - $credito_fijo); 
            
            $fila = [
                $index + 1,
                $nomina->empleado->documento_identidad, // Usando CI como código dependiente (simplificado)
                $nomina->empleado->primerapellido,
                $nomina->empleado->segundoapellido,
                $nomina->empleado->nombres,
                $nomina->empleado->documento_identidad,
                'CI', // Tipo Doc
                0, // Nov Edad
                number_format($remuneracion_neta, 2, '.', ''), // Base Imponible
                number_format($smn_x_2, 2, '.', ''), // Minimo No Imponible
                number_format($importe_sujeto_impuesto, 2, '.', ''), // Importe Sujeto Impuesto
                number_format($impuesto_rc_iva, 2, '.', ''), // RC IVA 13%
                number_format($credito_fijo, 2, '.', ''), // 2SMN 13%
                number_format($impuesto_neto, 2, '.', ''), // Impuesto Neto
                0, // F-110
                0, // Saldo Favor Dep
                0, // Saldo Anterior
                number_format($impuesto_neto, 2, '.', ''), // Saldo del Fisco (simplificado)
                0, // Saldo Utilizado
                //
                //number_format($monto_retenido, 2, '.', ''), // Impuesto Retenido (usando RC IVA DB, simplificado)
                number_format($impuesto_neto, 2, '.', ''), // Saldo Final (simplificado)
            ];
            $csv .= implode(',', $fila) . "\n";
        }
        return $csv;
    }

}
