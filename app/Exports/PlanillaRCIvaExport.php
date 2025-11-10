<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// --- Interfaces Avanzadas ---
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
// --- NUEVA INTERFAZ ---
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class PlanillaRCIvaExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents,
    WithDrawings,
    WithColumnFormatting,
    WithCustomStartCell // <-- AÑADIDA
{
    protected $datosPlanilla;
    protected $mes;
    protected $anio;
    protected $index = 0;

    protected $smn = 2750.00; 
    protected $smn_x_2;
    protected $credito_fijo;
    protected $tasa_rc_iva = 0.13;

    // Propiedades para las sumas
    protected $sumaBaseImponible = 0;
    protected $sumaMinimoImponible = 0;
    protected $sumaImpuestoSujeto = 0;
    protected $sumaRcIva = 0;
    protected $sumaCreditoFijo = 0;
    protected $sumaImpuestoNeto = 0;
    protected $sumaF110 = 0;
    protected $sumaSaldoFavorDep = 0;
    protected $sumaSaldoAnterior = 0;
    protected $sumaSaldoUtilizado = 0;
    protected $sumaImpuestoRetenido = 0;
    protected $sumaSaldoFinal = 0;

    public function __construct(Collection $datosPlanilla, string $mes, string $anio)
    {
        $this->datosPlanilla = $datosPlanilla;
        $this->mes = $mes;
        $this->anio = $anio;

        $this->smn_x_2 = $this->smn * 2;
        $this->credito_fijo = $this->smn_x_2 * 0.13;
    }

    public function collection()
    {
        return $this->datosPlanilla;
    }

    // --- NUEVA FUNCIÓN (Define dónde empieza la tabla) ---
    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return [
            'NRO', 'CODIGO_DEP_RCIVA', 'PATERNO', 'MATERNO', 'NOMBRES', 'CI', 'TIPO_DOC', 
            'NOV_EDAD', 'BASE_IMPONIBLE', 'MINIMO_NO_IMPONIBLE', 'IMPORTE_SUJETO_IMPUESTO', 
            'RC_IVA_13%', '13% 2SMN', 'IMPUESTO_NETO', 'F110', 'SALDO_FAVOR_DEP', 
            'SALDO_ANTERIOR', 'SALDO_DEL_FISCO', 'SALDO_UTILIZADO', 'IMPUESTO_RETENIDO', 'SALDO_FINAL'
        ];
    }

    public function map($nomina): array
    {
        $this->index++;
        
        $tg = $nomina->total_ganado;
        $remuneracion_neta = $tg - ($nomina->aporte_laboral + $nomina->aporte_nacional_solidario); 
        $importe_sujeto_impuesto = max(0, $remuneracion_neta - $this->smn_x_2);
        $impuesto_rc_iva = $importe_sujeto_impuesto * $this->tasa_rc_iva; 
        $impuesto_neto = max(0, $impuesto_rc_iva - $this->credito_fijo); 

        $f110 = 0;
        $saldo_favor_dep = 0;
        $saldo_anterior = 0;
        $saldo_fisco = $impuesto_neto;
        $saldo_utilizado = 0;
        $impuesto_retenido = $impuesto_neto;
        $saldo_final = $impuesto_neto;

        // Sumar para los totales
        $this->sumaBaseImponible += $remuneracion_neta;
        $this->sumaMinimoImponible += $this->smn_x_2;
        $this->sumaImpuestoSujeto += $importe_sujeto_impuesto;
        $this->sumaRcIva += $impuesto_rc_iva;
        $this->sumaCreditoFijo += $this->credito_fijo;
        $this->sumaImpuestoNeto += $impuesto_neto;
        $this->sumaF110 += $f110;
        $this->sumaSaldoFavorDep += $saldo_favor_dep;
        $this->sumaSaldoAnterior += $saldo_anterior;
        $this->sumaSaldoUtilizado += $saldo_utilizado;
        $this->sumaImpuestoRetenido += $impuesto_retenido;
        $this->sumaSaldoFinal += $saldo_final;

        return [
            $this->index,
            $nomina->empleado->documento_identidad,
            $nomina->empleado->primerapellido,
            $nomina->empleado->segundoapellido,
            $nomina->empleado->nombres,
            $nomina->empleado->documento_identidad,
            'CI',
            0,
            $remuneracion_neta,
            $this->smn_x_2,
            $importe_sujeto_impuesto,
            $impuesto_rc_iva,
            $this->credito_fijo,
            $impuesto_neto,
            $f110,
            $saldo_favor_dep,
            $saldo_anterior,
            $saldo_fisco,
            $saldo_utilizado,
            $impuesto_retenido,
            $saldo_final
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => '#,##0.00',
            'J' => '#,##0.00',
            'K' => '#,##0.00',
            'L' => '#,##0.00',
            'M' => '#,##0.00',
            'N' => '#,##0.00',
            'O' => '#,##0.00',
            'P' => '#,##0.00',
            'Q' => '#,##0.00',
            'R' => '#,##0.00',
            'S' => '#,##0.00',
            'T' => '#,##0.00',
            'U' => '#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // La cabecera está en la fila 5
        $sheet->getStyle('A5:U5')->getFont()->setBold(true);
        $sheet->getStyle('A5:U5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F0F0F0');
        
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I5:U'.$sheet->getHighestRow())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo de la empresa');
        $drawing->setPath(public_path('img/logo.png'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->mergeCells('B1:U2');
                $sheet->setCellValue('B1', 'IMPORTADORA Y LABORATORIO ATM S.R.L.');
                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('942044'));
                $sheet->getStyle('B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('B3:U3');
                $sheet->setCellValue('B3', 'PLANILLA DE RÉGIMEN COMPLEMENTARIO IMPUESTO AL VALOR AGREGADO (RC-IVA)');
                $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('B4:U4');
                $sheet->setCellValue('B4', 'CORRESPONDIENTE AL MES: ' . strtoupper($this->mes) . ' / ' . $this->anio);
                $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // --- LÍNEA DE ERROR ELIMINADA ---
            },

            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow() + 1;

                $sheet->setCellValue("A{$lastRow}", 'SUMAS TOTALES');
                $sheet->mergeCells("A{$lastRow}:H{$lastRow}");
                
                $sheet->setCellValue("I{$lastRow}", $this->sumaBaseImponible);
                $sheet->setCellValue("J{$lastRow}", $this->sumaMinimoImponible);
                $sheet->setCellValue("K{$lastRow}", $this->sumaImpuestoSujeto);
                $sheet->setCellValue("L{$lastRow}", $this->sumaRcIva);
                $sheet->setCellValue("M{$lastRow}", $this->sumaCreditoFijo);
                $sheet->setCellValue("N{$lastRow}", $this->sumaImpuestoNeto);
                $sheet->setCellValue("O{$lastRow}", $this->sumaF110);
                $sheet->setCellValue("P{$lastRow}", $this->sumaSaldoFavorDep);
                $sheet->setCellValue("Q{$lastRow}", $this->sumaSaldoAnterior);
                // Celda R (Saldo Fisco) es calculada
                $sheet->setCellValue("S{$lastRow}", $this->sumaSaldoUtilizado);
                $sheet->setCellValue("T{$lastRow}", $this->sumaImpuestoRetenido);
                $sheet->setCellValue("U{$lastRow}", $this->sumaSaldoFinal);

                $styleArray = [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0B2']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
                ];
                
                $sheet->getStyle("A{$lastRow}:U{$lastRow}")->applyFromArray($styleArray);
                $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}