<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlanillaMensualExport;
use App\Exports\PlanillaRCIvaExport;
use App\Exports\PlanillaGestoraExport; 
use Carbon\Carbon; // Importar Carbon

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
     * Obtiene los datos de nóminas para el mes y año dados,
     * ORDENADOS alfabéticamente por empleado.
     */
    private function getDatosNomina(string $mes, int $anio)
    {
        // Añadimos la carga de 'empleado.subsidios' para cualquier reporte futuro
        return Nomina::with('empleado.subsidios') 
            ->join('empleados', 'nominas.empleado_id', '=', 'empleados.id')
            ->where('nominas.mes', $mes)
            ->where('nominas.anio', $anio)
            ->orderBy('empleados.primerapellido', 'asc')
            ->orderBy('empleados.segundoapellido', 'asc')
            ->orderBy('empleados.nombres', 'asc')
            ->select('nominas.*')
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

        $nombreArchivo = "planilla-{$tipo}-{$mes}-{$anio}.{$formato}";

        // 2. Generación del reporte
        try {
            switch ($tipo) {
                case 'mensual':
                    return $this->generarPlanillaMensual($datosPlanilla, $mes, $anio, $formato, $nombreArchivo);

                case 'rc_iva':
                    return $this->generarPlanillaRCIva($datosPlanilla, $mes, $anio, $formato, $nombreArchivo);

                case 'gestora':
                    return $this->generarPlanillaGestora($datosPlanilla, $mes, $anio, $formato, $nombreArchivo);

                default:
                    return back()->with('error', 'Tipo de planilla no reconocido.');
            }
        } catch (Exception $e) {
            $csv = "ERROR_MESSAGE,FILE,LINE\n";
            $csv .= '"' . str_replace('"', '""', $e->getMessage()) . '",' . $e->getFile() . ',' . $e->getLine() . "\n";

            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="ERROR_PLANILLAS.csv"',
            ]);
        }
    }

    /**
     * Lógica para generar la Planilla Mensual (PDF, XLSX o CSV).
     */
    private function generarPlanillaMensual($datosPlanilla, $mes, $anio, $formato, $nombreArchivo)
    {
        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('planillas.pdf.mensual', compact('datosPlanilla', 'mes', 'anio'));
            $pdf->setPaper('letter', 'landscape');
            return $pdf->download($nombreArchivo);
        }
        elseif ($formato === 'xlsx') {
            return Excel::download(new PlanillaMensualExport($datosPlanilla, $mes, $anio), $nombreArchivo);
        }
        elseif ($formato === 'csv') {
            $csv = $this->generarCsvMensual($datosPlanilla);
            return Response::make($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"']);
        }
        throw new Exception("Formato de descarga '{$formato}' no soportado.");
    }

    /**
     * Lógica para generar la Planilla RC IVA (PDF, XLSX o CSV).
     */
    private function generarPlanillaRCIva($datosPlanilla, $mes, $anio, $formato, $nombreArchivo)
    {
        if ($formato === 'pdf') {
            // Asegúrate de que tu vista PDF esté usando los datos correctos
            $pdf = Pdf::loadView('planillas.pdf.rc_iva', compact('datosPlanilla', 'mes', 'anio'));
            $pdf->setPaper('letter', 'landscape');
            return $pdf->download($nombreArchivo);
        }
        elseif ($formato === 'xlsx') {
            return Excel::download(new PlanillaRCIvaExport($datosPlanilla, $mes, $anio), $nombreArchivo);
        }
        elseif ($formato === 'csv') {
            $csv = $this->generarCsvRCIva($datosPlanilla);
            return Response::make($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"']);
        }
        throw new Exception("Formato de descarga '{$formato}' no soportado para RC IVA.");
    }

    /**
     * Lógica para generar la Planilla Gestora (PDF, XLSX o CSV).
     */
    private function generarPlanillaGestora($datosPlanilla, $mes, $anio, $formato, $nombreArchivo)
    {
        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('planillas.pdf.gestora', compact('datosPlanilla', 'mes', 'anio'));
            $pdf->setPaper('letter', 'landscape'); // Horizontal
            return $pdf->download($nombreArchivo);
        }
        elseif ($formato === 'xlsx') {
            return Excel::download(new PlanillaGestoraExport($datosPlanilla, $mes, $anio), $nombreArchivo);
        }
        elseif ($formato === 'csv') {
            $csv = $this->generarCsvGestora($datosPlanilla, $mes, $anio);
            return Response::make($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"']);
        }
        throw new Exception("Formato de descarga '{$formato}' no soportado para Gestora.");
    }


    /*
    |--------------------------------------------------------------------------
    | FUNCIONES AUXILIARES DE CSV
    |--------------------------------------------------------------------------
    */

    private function generarCsvMensual($datosPlanilla) {
        $columnas = ['NRO', 'DOC IDENTIDAD', 'PATERNO', 'MATERNO', 'NOMBRE', 'FECHA INGRESO', 'DIAS PAGADOS', 'HORAS PAGADAS', 'SUELDO BÁSICO', 'BONO ANTIGÜEDAD', 'TRABAJO EXTRA', 'PAGO DOMINGO', 'OTROS BONOS', 'TOTAL GANADO', 'RC IVA 13%', 'AFP 12.71%', 'OTROS DESCUENTOS', 'TOTAL DESCUENTO', 'LÍQUIDO PAGABLE'];
        $csv = implode(',', $columnas) . "\n";
        foreach ($datosPlanilla as $index => $nomina) {
            $empleado = $nomina->empleado;
            $otrosDescuentos = ($nomina->anticipos ?? 0) + ($nomina->aporte_nacional_solidario ?? 0);
            $fila = [
                $index + 1, $empleado->documento_identidad, $empleado->primerapellido, $empleado->segundoapellido, $empleado->nombres,
                $empleado->fecha_ingreso, $nomina->dias_pagados, $nomina->horas_pagadas, $nomina->haber_basico,
                $nomina->bono_antiguedad, $nomina->trabajo_extraordinario, $nomina->pago_domingo, $nomina->otros_bonos,
                $nomina->total_ganado, $nomina->rc_iva, $nomina->aporte_laboral, number_format($otrosDescuentos, 2, '.', ''),
                $nomina->total_descuentos, $nomina->liquido
            ];
            $csv .= implode(',', $fila) . "\n";
        }
        return $csv;
    }

    /**
     * --- FUNCIÓN RC-IVA CORREGIDA (Usa las nuevas columnas) ---
     */
    private function generarCsvRCIva($datosPlanilla) {
        $columnas = [
            'Nro.', 'CUR Dependiente (NIT)', 'Nombres y Apellidos', 
            'Monto Ingreso Neto (Base Imponible)', 'Dos (2) SMN Imponibles', 'Importe Sujeto a Impuesto', 
            'Impuesto RC-IVA (13% Bruto)', '13% de Un (1) SMN', 'Total Crédito F.110', 
            'Impuesto Neto (Saldo Fisco)', 'Saldo Dependiente Período Anterior', 'Saldo Utilizado', 
            'Impuesto RC-IVA Retenido', 'Saldo CF-IVA para el Mes Siguiente'
        ];
        $csv = implode(',', $columnas) . "\n";

        // Usamos el SMN del primer registro como base para el cálculo de compensación.
        $smn = (float)($datosPlanilla->first()?->smn ?? 2750.00);
        $smn_x_2 = $smn * 2;
        $tasa_rc_iva = 0.13;
        $credito_fijo_1smn = $smn * $tasa_rc_iva; // El crédito fijo D.S. 5383

        foreach ($datosPlanilla as $index => $nomina) {
            $empleado = $nomina->empleado;
            
            // Reconstruir valores intermedios (la lógica exacta del JS)
            $monto_ingreso_neto = $nomina->total_ganado - ($nomina->aporte_laboral + $nomina->aporte_nacional_solidario);
            $base_imponible = max(0, $monto_ingreso_neto - $smn_x_2);
            $impuesto_rc_iva_bruto = $base_imponible * $tasa_rc_iva;

            // Datos de las nuevas columnas
            $f110_monto_bruto = $nomina->rc_iva_f110_monto ?? 0;
            $saldo_anterior = $nomina->rc_iva_saldo_anterior ?? 0;
            $saldo_siguiente_final = $nomina->rc_iva_saldo_siguiente ?? 0;

            // Calcular el crédito F110 usado
            $credito_f110_valor = $f110_monto_bruto * $tasa_rc_iva;
            
            // Impuesto Neto (Impuesto Bruto - Crédito Fijo - Crédito F110)
            $impuesto_neto_compensar = max(0, $impuesto_rc_iva_bruto - $credito_fijo_1smn - $credito_f110_valor); 

            // Saldo Fisco (El impuesto neto antes de usar el saldo anterior)
            $saldo_fisco = $impuesto_neto_compensar;

            // Saldo Utilizado (Cuánto del saldo anterior se usó)
            // Calculamos cuánto se necesitaba VS cuánto se tenía.
            $saldo_utilizado = 0;
            if ($impuesto_neto_compensar > 0) {
                // Solo se usa si hay impuesto neto y hay saldo anterior
                $saldo_utilizado = min($saldo_anterior, $impuesto_neto_compensar);
            }

            $fila = [
                $index + 1, 
                $empleado->nit_dependiente ?? $empleado->documento_identidad,
                $empleado->primerapellido . ' ' . $empleado->segundoapellido . ' ' . $empleado->nombres,
                
                number_format($monto_ingreso_neto, 2, '.', ''), 
                number_format($smn_x_2, 2, '.', ''),
                number_format($base_imponible, 2, '.', ''), 
                number_format($impuesto_rc_iva_bruto, 2, '.', ''),
                
                number_format($credito_fijo_1smn, 2, '.', ''),
                number_format($credito_f110_valor, 2, '.', ''), 
                
                number_format($saldo_fisco, 2, '.', ''),
                number_format($saldo_anterior, 2, '.', ''),
                
                number_format($saldo_utilizado, 2, '.', ''),
                number_format($nomina->rc_iva, 2, '.', ''), // RC-IVA Retenido (Final)
                number_format($saldo_siguiente_final, 2, '.', ''),
            ];
            $csv .= implode(',', $fila) . "\n";
        }
        return $csv;
    }

    /**
     * --- CSV GESTORA CORREGIDO ---
     */
    private function generarCsvGestora($datosPlanilla, $mes, $anio) {
        $columnas = [
            'NRO', 'TIPO_DOC', 'NUMERO_DOC', 'COMPLEMENTO', 'CUA', 'PRIMER_APELLIDO',
            'SEGUNDO_APELLIDO', 'APELLIDO_CASADA', 'PRIMER_NOMBRE', 'SEGUNDO_NOMBRE',
            'TIPO_NOVEDAD', 'FECHA_NOVEDAD', 'DIAS_COTIZADOS', 'TIPO_ASEGURADO',
            'TOTAL_GANADO', 'COTIZACION_ADICIONAL'
        ];
        $csv = implode(',', $columnas) . "\n";

        $mesesMap = [
            'Enero' => '01', 'Febrero' => '02', 'Marzo' => '03', 'Abril' => '04',
            'Mayo' => '05', 'Junio' => '06', 'Julio' => '07', 'Agosto' => '08',
            'Septiembre' => '09', 'Octubre' => '10', 'Noviembre' => '11', 'Diciembre' => '12'
        ];
        $mesNumero = $mesesMap[$mes] ?? '01';

        foreach ($datosPlanilla as $index => $nomina) {
            $empleado = $nomina->empleado;

            // Aseguramos que solo usamos el primer nombre como primer_nombre
            $nombres_array = explode(' ', $empleado->nombres);
            $primer_nombre = $nombres_array[0] ?? '';
            $segundo_nombre = (isset($nombres_array[1]) && $nombres_array[1] !== '') ? $nombres_array[1] : null;

            $tipo_novedad = '';
            $fecha_novedad = null;
            $fecha_ingreso = \Carbon\Carbon::parse($empleado->fecha_ingreso);
            
            if ($fecha_ingreso->month == $mesNumero && $fecha_ingreso->year == $anio) {
                $tipo_novedad = 'I';
                $fecha_novedad = $fecha_ingreso->format('Y-m-d');
            }
            
            $cotizacion_adicional = 0;

            $fila = [
                $index + 1, 'I', $empleado->documento_identidad, $empleado->complemento,
                $empleado->cua, $empleado->primerapellido, $empleado->segundoapellido,
                '', $primer_nombre, $segundo_nombre, $tipo_novedad, $fecha_novedad,
                $nomina->dias_pagados, 'D', $nomina->total_ganado, $cotizacion_adicional
            ];
            $csv .= implode(',', $fila) . "\n";
        }
        return $csv;
    }
}