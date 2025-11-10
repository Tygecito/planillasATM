<!DOCTYPE html>
<html>
<head>
    <title>Planilla Gestora - {{ $mes }} {{ $anio }}</title>
    
    @php
        $logoPath = public_path('img/logo.png');
        $logoSrc = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        }
        \Carbon\Carbon::setLocale('es');

        // --- CORRECCIÓN AQUÍ ---
        // Creamos un "traductor" para los meses
        $mesesMap = [
            'Enero' => '01', 'Febrero' => '02', 'Marzo' => '03', 'Abril' => '04',
            'Mayo' => '05', 'Junio' => '06', 'Julio' => '07', 'Agosto' => '08',
            'Septiembre' => '09', 'Octubre' => '10', 'Noviembre' => '11', 'Diciembre' => '12'
        ];
        $mesNumero = $mesesMap[$mes] ?? '01'; // Usamos el traductor
        // --- FIN DE LA CORRECCIÓN ---
        
        $sumaTotalGanado = 0;
        $sumaCotizacionAdicional = 0;
    @endphp

    <style>
        body { font-family: Arial, sans-serif; font-size: 5.5pt; margin: 3mm; }
        
        .header-empresa { display: flex; align-items: center; margin-bottom: 5px; border-bottom: 1px solid #333; padding-bottom: 5px; }
        .header-empresa .logo { width: 50px; height: auto; margin-right: 10px; vertical-align: middle; }
        .header-text { text-align: left; flex-grow: 1; }
        .razon-social { font-size: 9pt; font-weight: bold; color: #942044; margin: 0; line-height: 1; }
        h1 { margin: 0; font-size: 11pt; color: #333; line-height: 1.1; text-align: center; }
        .header-info { text-align: center; margin-bottom: 5px; font-size: 7pt; }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 5px; }
        th, td { border: 1px solid #333; padding: 1px 2px; text-align: left; vertical-align: middle; word-wrap: break-word; font-size: 5pt; height: 10px; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        
        .sumas-totales td {
            font-weight: bold;
            background-color: #f0f0f0;
            text-align: right;
        }
        .sumas-totales .label {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header-empresa">
    @if($logoSrc)
        <img src="{{ $logoSrc }}" alt="Logo" class="logo">
    @endif
    <div class="header-text">
        <p class="razon-social">IMPORTADORA Y LABORATORIO ATM S.R.L.</p>
        <h1>PLANILLA DE PAGO DE APORTES - GESTORA</h1>
    </div>
</div>
<div class="header-info">
    PERIODO: {{ strtoupper($mes) }} / {{ $anio }}
</div>

<table>
    <thead>
        <tr>
            <th style="width: 2.5%">NRO</th>
            <th style="width: 4%">TIPO DOC.</th>
            <th style="width: 6%">NÚMERO DOC.</th>
            <th style="width: 3%">COMP.</th>
            <th style="width: 5%">CUA</th>
            <th style="width: 7%">(A) PRIMER APELLIDO</th>
            <th style="width: 7%">(B) SEGUNDO APELLIDO</th>
            <th style="width: 7%">(C) APELLIDO CASADA</th>
            <th style="width: 7%">(D) PRIMER NOMBRE</th>
            <th style="width: 7%">(E) SEGUNDO NOMBRE</th>
            <th style="width: 4%">TIPO NOV.</th>
            <th style="width: 6%">FECHA NOV.</th>
            <th style="width: 4%">DÍAS COT.</th>
            <th style="width: 4%">TIPO ASEG.</th>
            <th style="width: 7%">TOTAL GANADO</th>
            <th style="width: 7%">COTIZ. ADICIONAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datosPlanilla as $nomina)
            @php
                $empleado = $nomina->empleado;
                
                list($primer_nombre, $segundo_nombre) = array_pad(explode(' ', $empleado->nombres, 2), 2, null);

                $tipo_novedad = '';
                $fecha_novedad = null;
                $fecha_ingreso = \Carbon\Carbon::parse($empleado->fecha_ingreso);
                
                // Usamos el $mesNumero corregido
                if ($fecha_ingreso->month == $mesNumero && $fecha_ingreso->year == $anio) {
                    $tipo_novedad = 'I';
                    $fecha_novedad = $fecha_ingreso->format('Y-m-d');
                }

                $cotizacion_adicional = 0;

                $sumaTotalGanado += $nomina->total_ganado;
                $sumaCotizacionAdicional += $cotizacion_adicional;
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">I</td>
                <td class="text-center">{{ $empleado->documento_identidad }}</td>
                <td class="text-center">{{ $empleado->complemento }}</td>
                <td class="text-center">{{ $empleado->cua }}</td>
                <td>{{ $empleado->primerapellido }}</td>
                <td>{{ $empleado->segundoapellido }}</td>
                <td></td> {{-- Apellido Casada --}}
                <td>{{ $primer_nombre }}</td>
                <td>{{ $segundo_nombre }}</td>
                <td class="text-center">{{ $tipo_novedad }}</td>
                <td class="text-center">{{ $fecha_novedad }}</td>
                <td class="text-center">{{ $nomina->dias_pagados }}</td>
                <td class="text-center">D</td> {{-- Tipo Asegurado --}}
                <td class="text-right">{{ number_format($nomina->total_ganado, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($cotizacion_adicional, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        
        <tr class="sumas-totales">
            <td colspan="14" class="label">TOTAL</td>
            <td class="text-right">{{ number_format($sumaTotalGanado, 2, ',', '.') }}</td>
            <td class="text-right">{{ number_format($sumaCotizacionAdicional, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

</body>
</html>