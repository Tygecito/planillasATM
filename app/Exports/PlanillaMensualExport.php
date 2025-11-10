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

class PlanillaMensualExport implements 
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

    // Propiedades para las sumas
    protected $sumaHaberBasico = 0;
    protected $sumaBonoAntiguedad = 0;
    protected $sumaTrabExtra = 0;
    protected $sumaPagoDomingo = 0;
    protected $sumaOtrosBonos = 0;
    protected $sumaTotalGanado = 0;
    protected $sumaRcIva = 0;
    protected $sumaAporteLaboral = 0;
    protected $sumaOtrosDescuentos = 0;
    protected $sumaTotalDescuento = 0;
    protected $sumaLiquidoPagable = 0;

    public function __construct(Collection $datosPlanilla, string $mes, string $anio)
    {
        $this->datosPlanilla = $datosPlanilla;
        $this->mes = $mes;
        $this->anio = $anio;
    }

    public function collection()
    {
        return $this->datosPlanilla;
    }

    // --- NUEVA FUNCIÓN (Define dónde empieza la tabla) ---
    public function startCell(): string
    {
        return 'A5'; // Los títulos irán en A1-A4, la tabla empieza en A5
    }

    public function headings(): array
    {
        return [
            'NRO', 'DOC IDENTIDAD', 'PATERNO', 'MATERNO', 'NOMBRE', 'FECHA INGRESO', 'DIAS PAGADOS', 'HORAS PAGADAS',
            'SUELDO BÁSICO', 'BONO ANTIGÜEDAD', 'TRABAJO EXTRA', 'PAGO DOMINGO', 'OTROS BONOS', 'TOTAL GANADO',
            'RC IVA 13%', 'AFP 12.71%', 'OTROS DESCUENTOS', 'TOTAL DESCUENTO', 'LÍQUIDO PAGABLE'
        ];
    }

    public function map($nomina): array
    {
        $this->index++;
        $empleado = $nomina->empleado;
        $otrosDescuentos = ($nomina->anticipos ?? 0) + ($nomina->aporte_nacional_solidario ?? 0);

        // Sumar para los totales
        $this->sumaHaberBasico += $nomina->haber_basico;
        $this->sumaBonoAntiguedad += $nomina->bono_antiguedad;
        $this->sumaTrabExtra += $nomina->trabajo_extraordinario;
        $this->sumaPagoDomingo += $nomina->pago_domingo;
        $this->sumaOtrosBonos += $nomina->otros_bonos;
        $this->sumaTotalGanado += $nomina->total_ganado;
        $this->sumaRcIva += $nomina->rc_iva;
        $this->sumaAporteLaboral += $nomina->aporte_laboral;
        $this->sumaOtrosDescuentos += $otrosDescuentos;
        $this->sumaTotalDescuento += $nomina->total_descuentos;
        $this->sumaLiquidoPagable += $nomina->liquido;

        return [
            $this->index,
            $empleado->documento_identidad,
            $empleado->primerapellido,
            $empleado->segundoapellido,
            $empleado->nombres,
            \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d-m-Y'),
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
            $otrosDescuentos,
            $nomina->total_descuentos,
            $nomina->liquido
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
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
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // La fila de cabecera ahora es la 5 (porque definimos startCell)
        $sheet->getStyle('A5:S5')->getFont()->setBold(true);
        $sheet->getStyle('A5:S5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F0F0F0');
        
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I5:S'.$sheet->getHighestRow())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo de la empresa');
        $drawing->setPath(public_path('img/logo.png')); 
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1'); // Títulos empezarán debajo o al lado

        return $drawing;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // NO insertamos filas, solo escribimos en las filas 1-4
                
                $sheet->mergeCells('B1:S2');
                $sheet->setCellValue('B1', 'IMPORTADORA Y LABORATORIO ATM S.R.L.');
                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('942044'));
                $sheet->getStyle('B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('B3:S3');
                $sheet->setCellValue('B3', 'PLANILLA DE SUELDOS Y SALARIOS');
                $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('B4:S4');
                $sheet->setCellValue('B4', 'CORRESPONDIENTE AL MES: ' . strtoupper($this->mes) . ' / ' . $this->anio);
                $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // --- LÍNEA DE ERROR ELIMINADA ---
                // $event->sheet->setStartingCell('A5'); // <- ESTO FUE BORRADO
            },

            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow() + 1; 

                $sheet->setCellValue("A{$lastRow}", 'SUMAS TOTALES');
                $sheet->mergeCells("A{$lastRow}:H{$lastRow}"); 
                
                $sheet->setCellValue("I{$lastRow}", $this->sumaHaberBasico);
                $sheet->setCellValue("J{$lastRow}", $this->sumaBonoAntiguedad);
                $sheet->setCellValue("K{$lastRow}", $this->sumaTrabExtra);
                $sheet->setCellValue("L{$lastRow}", $this->sumaPagoDomingo);
                $sheet->setCellValue("M{$lastRow}", $this->sumaOtrosBonos);
                $sheet->setCellValue("N{$lastRow}", $this->sumaTotalGanado);
                $sheet->setCellValue("O{$lastRow}", $this->sumaRcIva);
                $sheet->setCellValue("P{$lastRow}", $this->sumaAporteLaboral);
                $sheet->setCellValue("Q{$lastRow}", $this->sumaOtrosDescuentos);
                $sheet->setCellValue("R{$lastRow}", $this->sumaTotalDescuento);
                $sheet->setCellValue("S{$lastRow}", $this->sumaLiquidoPagable);

                $styleArray = [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0B2']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
                ];
                
                $sheet->getStyle("A{$lastRow}:S{$lastRow}")->applyFromArray($styleArray);
                $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}