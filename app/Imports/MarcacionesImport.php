<?php

namespace App\Imports;

use App\Models\Empleado;
use App\Models\Marcacion;
use App\Models\Retraso; 
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;

class MarcacionesImport implements ToModel, WithHeadingRow, WithChunkReading, WithEvents
{
    protected $mes;
    protected $anio;
    protected $empleadosMap;
    public $conteoRetrasos = 0; 

    public function __construct(int $mes, int $anio)
    {
        $this->mes = $mes;
        $this->anio = $anio;

        $this->empleadosMap = Empleado::all()->keyBy(fn($empleado) => trim((string)$empleado->documento_identidad))
                                    ->map(fn($empleado) => $empleado->id)
                                    ->toArray();
    }

    public function model(array $row)
    {
        $acNo = isset($row['ac_no']) ? trim((string)$row['ac_no']) : null;
        $dateTimeValor = isset($row['time']) ? $row['time'] : null;
        if (empty($acNo) || empty($dateTimeValor)) { return null; }

        try {
            if (is_numeric($dateTimeValor)) {
                $dateTimeObject = Date::excelToDateTimeObject($dateTimeValor);
                $fechaHora = Carbon::instance($dateTimeObject);
            } else {
                try { $fechaHora = Carbon::parse($dateTimeValor); }
                catch (\Exception $e1) { $fechaHora = Carbon::createFromFormat('j/n/Y H:i:s', $dateTimeValor); }
            }
        } catch (\Exception $e) {
            Log::warning('Import Asistencia: Fecha inválida. Fila ignorada.', ['valor' => $dateTimeValor]);
            return null;
        }

        if ($fechaHora->month != $this->mes || $fechaHora->year != $this->anio) {
            return null;
        }

        $empleadoId = $this->empleadosMap[$acNo] ?? null;
        if (!$empleadoId) {
            Log::warning('Import Asistencia: Empleado no encontrado.', ['ac_no_buscado' => $acNo]);
            return null;
        }

        return new Marcacion([
            'empleado_id' => $empleadoId,
            'ac_no'       => $acNo,
            'fecha_hora'  => $fechaHora,
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                // Ejecutamos la lógica de cálculo AHORA
                $this->conteoRetrasos = $this->procesarRetrasos($this->mes, $this->anio);
            },
        ];
    }

    /**
    * Lógica de cálculo CORREGIDA y ROBUSTA.
    */
    public function procesarRetrasos(int $mes, int $anio)
    {
        // 1. Definimos los límites como objetos de tiempo (sin fecha)
        $horaLimiteManana = Carbon::createFromTimeString('08:00:00');
        $horaLimiteTarde = Carbon::createFromTimeString('14:00:00');
        $horaSeparacionAlmuerzo = Carbon::createFromTimeString('12:00:00'); 

        Retraso::whereMonth('fecha', $mes)->whereYear('fecha', $anio)->delete();

        $marcaciones = Marcacion::whereMonth('fecha_hora', $mes)
                                ->whereYear('fecha_hora', $anio)
                                ->orderBy('fecha_hora', 'asc')
                                ->get()
                                ->groupBy(['empleado_id', fn($item) => $item->fecha_hora->format('Y-m-d')]);
        
        $totalRetrasos = 0;
        $nuevosRetrasos = []; 

        foreach ($marcaciones as $empleadoId => $dias) {
            foreach ($dias as $fechaStr => $marcacionesDelDia) {
                $fecha = Carbon::parse($fechaStr);

                // --- 5.1. CHEQUEO DE RETRASO EN LA MAÑANA (08:00) ---
                $primeraMarcacion = $marcacionesDelDia->first();
                if (!$primeraMarcacion) continue;
                $horaEfectivaManana = $primeraMarcacion->fecha_hora;
                
                // Creamos el límite con la FECHA de la marcación para una comparación segura
                $limiteMananaConFecha = $fecha->copy()->setTime(8, 0, 0); 
                
                // Comparamos: Si la marca es MAYOR (después) de las 08:00:00
                if ($horaEfectivaManana->greaterThan($limiteMananaConFecha)) {
                    
                    // Calculamos los minutos (08:20:00 - 08:00:00 = 20 min)
                    $minutosRetraso = $horaEfectivaManana->diffInMinutes($limiteMananaConFecha); 

                    $nuevosRetrasos[] = [
                        'empleado_id' => $empleadoId, 'fecha' => $fecha,
                        'tipo' => 'INGRESO_MANANA',
                        'minutos_retraso' => $minutosRetraso, 
                        'hora_limite' => $horaLimiteManana->format('H:i:s'), // 08:00:00
                        'hora_marcacion' => $horaEfectivaManana->format('H:i:s'), // 08:20:00
                        'fecha_creacion' => now(), 'fecha_modificacion' => now(),
                    ];
                    $totalRetrasos++;
                }

                // --- 5.2. CHEQUEO DE RETRASO EN LA TARDE (14:00) ---
                // Buscamos la PRIMERA marcación DESPUÉS de las 12:00 (hora de reingreso)
                $ingresoTarde = $marcacionesDelDia->firstWhere(fn($marcacion) => $marcacion->fecha_hora->greaterThan($horaSeparacionAlmuerzo));

                if ($ingresoTarde) {
                    $horaEfectivaTarde = $ingresoTarde->fecha_hora;
                    $limiteTardeConFecha = $fecha->copy()->setTime(14, 0, 0); 
                    
                    // Comparamos: Si marcó DESPUÉS de las 14:00:00
                    if ($horaEfectivaTarde->greaterThan($limiteTardeConFecha)) {
                         
                        $minutosRetraso = $horaEfectivaTarde->diffInMinutes($limiteTardeConFecha);

                         $nuevosRetrasos[] = [
                            'empleado_id' => $empleadoId, 'fecha' => $fecha,
                            'tipo' => 'INGRESO_TARDE',
                            'minutos_retraso' => $minutosRetraso,
                            'hora_limite' => $horaLimiteTarde->format('H:i:s'),
                            'hora_marcacion' => $horaEfectivaTarde->format('H:i:s'),
                            'fecha_creacion' => now(), 'fecha_modificacion' => now(),
                        ];
                        $totalRetrasos++;
                    }
                }
            }
        }

        if (!empty($nuevosRetrasos)) {
            Retraso::insert($nuevosRetrasos);
        }
        
        return $totalRetrasos; 
    }
}