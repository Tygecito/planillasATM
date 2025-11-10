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
use App\Exports\PlanillaGestoraExport; // <-- Asegúrate que esté

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
        return Nomina::with('empleado')
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

    private function generarCsvRCIva($datosPlanilla) {
        $columnas = [
            'Nro.', 'CUR Dependiente (NIT)', 'Nombres y Apellidos', 'Monto Ingreso Neto',
            'Dos (2) SMN Imponibles', 'Importe Sujeto a Impuesto', 'Impuesto RC-IVA (13%)',
            '13% de Un (1) SMN', 'Impuesto Neto RC-IVA', 'F-110 Casilla 693',
            'Saldo a Favor del Fisco', 'Saldo a Favor del Dependiente',
            'Saldo Dependiente Período Anterior', 'Mntto de Valor', 'Saldo Anterior Actualizado',
            'Saldo Utilizado', 'Impuesto RC-IVA Retenido', 'Saldo CF-IVA para el Mes Siguiente'
        ];
        $csv = implode(',', $columnas) . "\n";

        $smn = (float)($datosPlanilla->first()?->smn ?? 2750.00);
        $smn_x_2 = $smn * 2;
        $credito_fijo_1smn = $smn * 0.13;

        foreach ($datosPlanilla as $index => $nomina) {
            $empleado = $nomina->empleado;
            $monto_ingreso_neto = $nomina->total_ganado - ($nomina->aporte_laboral + $nomina->aporte_nacional_solidario);
            $dos_smn = $smn_x_2;
            $base_imponible = max(0, $monto_ingreso_neto - $dos_smn);
            $impuesto_rc_iva = $base_imponible * 0.13;
            $credito_fijo_1smn_valor = $credito_fijo_1smn;
            $impuesto_neto = max(0, $impuesto_rc_iva - $credito_fijo_1smn_valor);
            $f110 = $nomina->f110_monto_facturas ?? 0;
            $saldo_fisco = max(0, $impuesto_neto - $f110);
            $saldo_dependiente_actual = max(0, $f110 - $impuesto_neto);
            $saldo_anterior = $nomina->saldo_anterior_dependiente ?? 0;
            $mantenimiento_valor = $nomina->mantenimiento_valor ?? 0;
            $saldo_anterior_actualizado = $saldo_anterior + $mantenimiento_valor;
            $saldo_utilizado = min($saldo_fisco, $saldo_anterior_actualizado);
            $impuesto_retenido = max(0, $saldo_fisco - $saldo_utilizado);
            $saldo_siguiente_mes = $saldo_dependiente_actual + ($saldo_anterior_actualizado - $saldo_utilizado);
            
            $fila = [
                $index + 1, $empleado->nit_dependiente ?? $empleado->documento_identidad,
                $empleado->primerapellido . ' ' . $empleado->segundoapellido . ' ' . $empleado->nombres,
                number_format($monto_ingreso_neto, 2, '.', ''), number_format($dos_smn, 2, '.', ''),
                number_format($base_imponible, 2, '.', ''), number_format($impuesto_rc_iva, 2, '.', ''),
                number_format($credito_fijo_1smn_valor, 2, '.', ''), number_format($impuesto_neto, 2, '.', ''),
                number_format($f110, 2, '.', ''), number_format($saldo_fisco, 2, '.', ''),
                number_format($saldo_dependiente_actual, 2, '.', ''), number_format($saldo_anterior, 2, '.', ''),
                number_format($mantenimiento_valor, 2, '.', ''), number_format($saldo_anterior_actualizado, 2, '.', ''),
                number_format($saldo_utilizado, 2, '.', ''), number_format($impuesto_retenido, 2, '.', ''),
                number_format($saldo_siguiente_mes, 2, '.', ''),
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

        // --- CORRECCIÓN AQUÍ ---
        $mesesMap = [
            'Enero' => '01', 'Febrero' => '02', 'Marzo' => '03', 'Abril' => '04',
            'Mayo' => '05', 'Junio' => '06', 'Julio' => '07', 'Agosto' => '08',
            'Septiembre' => '09', 'Octubre' => '10', 'Noviembre' => '11', 'Diciembre' => '12'
        ];
        $mesNumero = $mesesMap[$mes] ?? '01';
        // --- FIN DE LA CORRECCIÓN ---

        foreach ($datosPlanilla as $index => $nomina) {
            $empleado = $nomina->empleado;

            list($primer_nombre, $segundo_nombre) = array_pad(explode(' ', $empleado->nombres, 2), 2, null);
            
            $tipo_novedad = '';
            $fecha_novedad = null;
            $fecha_ingreso = \Carbon\Carbon::parse($empleado->fecha_ingreso);
            
            // Usamos $mesNumero corregido
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