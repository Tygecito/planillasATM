<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PlanillaGestoraExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents,
    WithDrawings,
    WithColumnFormatting,
    WithCustomStartCell
{
    protected $datosPlanilla;
    protected $mes;
    protected $anio;
    protected $mesNumero;
    protected $index = 0;

    protected $sumaTotalGanado = 0;
    protected $sumaCotizacionAdicional = 0;

    public function __construct(Collection $datosPlanilla, string $mes, string $anio)
    {
        $this->datosPlanilla = $datosPlanilla;
        $this->mes = $mes;
        $this->anio = $anio;

        // --- CORRECCIÓN AQUÍ ---
        $mesesMap = [
            'Enero' => '01', 'Febrero' => '02', 'Marzo' => '03', 'Abril' => '04',
            'Mayo' => '05', 'Junio' => '06', 'Julio' => '07', 'Agosto' => '08',
            'Septiembre' => '09', 'Octubre' => '10', 'Noviembre' => '11', 'Diciembre' => '12'
        ];
        $this->mesNumero = $mesesMap[$mes] ?? '01'; // Usamos el traductor
        // --- FIN DE LA CORRECCIÓN ---
    }

    public function collection()
    {
        return $this->datosPlanilla;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return [
            'NRO',
            'TIPO DE DOCUMENTO DE IDENTIDAD (I / E)',
            'NÚMERO DE DOCUMENTO DE IDENTIDAD',
            'COMPLEMENTO C.I.',
            'CUA',
            '(A) PRIMER APELLIDO',
            '(B) SEGUNDO APELLIDO',
            '(C) APELLIDO CASADA',
            '(D) PRIMER NOMBRE',
            '(E) SEGUNDO NOMBRE',
            'TIPO DE NOVEDAD (I/R/L/S)',
            'FECHA NOVEDAD (AAAA-MM-DD)',
            'DÍAS COTIZADOS',
            'TIPO DE ASEGURADO',
            'TOTAL GANADO',
            'COTIZACIÓN ADICIONAL'
        ];
    }

    public function map($nomina): array
    {
        $this->index++;
        $empleado = $nomina->empleado;

        list($primer_nombre, $segundo_nombre) = array_pad(explode(' ', $empleado->nombres, 2), 2, null);

        $tipo_novedad = '';
        $fecha_novedad = null;
        $fecha_ingreso = \Carbon\Carbon::parse($empleado->fecha_ingreso);
        
        // Usamos $this->mesNumero (corregido)
        if ($fecha_ingreso->month == $this->mesNumero && $fecha_ingreso->year == $this->anio) {
            $tipo_novedad = 'I';
            $fecha_novedad = $fecha_ingreso->format('Y-m-d');
        }

        $cotizacion_adicional = 0;

        $this->sumaTotalGanado += $nomina->total_ganado;
        $this->sumaCotizacionAdicional += $cotizacion_adicional;

        return [
            $this->index,
            'I',
            $empleado->documento_identidad,
            $empleado->complemento,
            $empleado->cua,
            $empleado->primerapellido,
            $empleado->segundoapellido,
            '', // (Apellido Casada)
            $primer_nombre,
            $segundo_nombre,
            $tipo_novedad,
            $fecha_novedad,
            $nomina->dias_pagados,
            'D',
            $nomina->total_ganado,
            $cotizacion_adicional
        ];
    }

    public function columnFormats(): array
    {
        return [
            'L' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'O' => '#,##0.00',
            'P' => '#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A5:P5')->getFont()->setBold(true);
        $sheet->getStyle('A5:P5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F0F0F0');
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K:K')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('M:N')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('O5:P'.$sheet->getHighestRow())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
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
                
                $sheet->mergeCells('B1:P2');
                $sheet->setCellValue('B1', 'IMPORTADORA Y LABORATORIO ATM S.R.L.');
                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('942044'));
                $sheet->getStyle('B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('B3:P3');
                $sheet->setCellValue('B3', 'PLANILLA DE PAGO DE APORTES - GESTORA');
                $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('B4:P4');
                $sheet->setCellValue('B4', 'PERIODO: ' . strtoupper($this->mes) . ' / ' . $this->anio);
                $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },

            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow() + 1;

                $sheet->setCellValue("A{$lastRow}", 'Total');
                $sheet->mergeCells("A{$lastRow}:N{$lastRow}");
                
                $sheet->setCellValue("O{$lastRow}", $this->sumaTotalGanado);
                $sheet->setCellValue("P{$lastRow}", $this->sumaCotizacionAdicional);

                $styleArray = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
                ];
                
                $sheet->getStyle("A{$lastRow}:P{$lastRow}")->applyFromArray($styleArray);
                $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}