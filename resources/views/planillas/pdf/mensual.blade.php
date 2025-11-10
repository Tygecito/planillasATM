<!DOCTYPE html>
<html>
<head>
<title>Planilla Mensual - {{ $mes }} {{ $anio }}</title>

@php
    // Lógica para codificar el logo en Base64 para que DomPDF pueda mostrarlo
    $logoPath = public_path('img/logo.png');
    $logoSrc = '';
    if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoSrc = 'data:image/png;base64,' . $logoData;
    }
    
    // Configuración de Carbon para formatear la fecha en español
    \Carbon\Carbon::setLocale('es');
@endphp

<style>
    /* Estilos CSS para el PDF - Compactos para asegurar una sola página en tamaño Carta horizontal */
    body {
        font-family: Arial, sans-serif;
        font-size: 6pt; 
        margin: 3mm; /* Márgenes mínimos */
        padding: 0;
    }
    
    /* === ENCABEZADO Y TÍTULOS === */
    .header-empresa {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        border-bottom: 1px solid #333;
        padding-bottom: 5px;
    }
    .header-empresa .logo {
        width: 50px; /* Tamaño fijo del logo */
        height: auto;
        margin-right: 10px;
        vertical-align: middle;
    }
    .header-text {
        text-align: left;
        flex-grow: 1;
    }
    .razon-social {
        font-size: 9pt;
        font-weight: bold;
        color: #942044;
        margin: 0;
        line-height: 1;
    }
    h1 {
        margin: 0;
        font-size: 11pt;
        color: #333;
        line-height: 1.1;
        text-align: center; /* TÍTULO CENTRADO */
    }
    .header-info {
        text-align: center;
        margin-bottom: 5px;
        font-size: 7pt;
    }

    /* === TABLA DE PLANILLA === */
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; 
        margin-top: 5px;
    }
    th, td {
        border: 1px solid #333;
        padding: 1px 2px;
        text-align: right; 
        vertical-align: middle;
        word-wrap: break-word; 
        font-size: 5.5pt; /* Tamaño de fuente crítico */
        height: 12px; /* Altura mínima de fila */
    }
    th {
        background-color: #f0f0f0;
        text-align: center;
        font-weight: bold;
    }
    /* AJUSTE DE ANCHOS DE COLUMNA (OPTIMIZADO) */
    .col-nro { width: 2.5%; } /* <-- NUEVA CLASE */
    .col-nombre { text-align: left; width: 12.5%; } /* Reducido para Nro */
    .col-identidad { width: 6%; } 
    .col-date { width: 5%; } 
    .col-num-small { width: 3%; } 
    .col-money-narrow { width: 5.2%; } 
    .col-money-base { width: 6.2%; } 
    .col-money-total { width: 6.8%; font-weight: bold; } 
    .col-firma { width: 6.5%; } 
    
    .sumas-totales td {
        font-weight: bold;
        background-color: #ffe0b2; 
    }

    /* === PIE DE PÁGINA (FIRMAS Y FECHA) === */
    .footer-seccion {
        margin-top: 25px;
        font-size: 7pt;
        width: 100%;
    }
    .tabla-firmas {
        width: 100%;
        border-collapse: collapse;
    }
    .tabla-firmas td {
        border: none;
        width: 50%;
        text-align: center;
        padding: 0 15px; /* Separación horizontal */
    }
    .firma-linea {
        height: 1px;
        border-top: 1px solid #333;
        margin: 40px auto 5px; /* Arriba mucha separación, abajo poca */
        width: 30%; /* Línea de firma achicada al 50% (de 60% a 30%) */
    }
    .fecha-generacion {
        text-align: right;
        margin-top: 15px;
        font-size: 7pt;
        width: 100%;
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
        <h1>PLANILLA DE SUELDOS Y SALARIOS</h1>
    </div>
</div>

<div class="header-info">
    CORRESPONDIENTE AL MES: {{ strtoupper($mes) }} / {{ $anio }}
</div>

<table>
    <thead>
        <tr>
            <th rowspan="2" class="col-nro">Nro.</th>
            {{-- 1 --}}
            <th rowspan="2" class="col-identidad">DOC IDENTIDAD NRO.</th>
            {{-- 2, 3, 4 --}}
            <th colspan="3" class="col-nombre">APELLIDO PATERNO/MATERNO/NOMBRE</th>
            {{-- 5 --}}
            <th rowspan="2" class="col-date">F. INGRESO</th>
            {{-- 6, 7 --}}
            <th colspan="2">PERIODO</th>
            {{-- 8, 9, 10, 11, 12 --}}
            <th colspan="5">GANADO</th>
            {{-- 13 --}}
            <th rowspan="2" class="col-money-total">TOTAL GANADO</th>
            {{-- 14, 15, 16 --}}
            <th colspan="3">DESCUENTOS</th>
            {{-- 17 --}}
            <th rowspan="2" class="col-money-total">TOTAL DESC.</th>
            {{-- 18 --}}
            <th rowspan="2" class="col-money-total">LÍQUIDO PAGABLE</th>
            {{-- 19 --}}
            <th rowspan="2" class="col-firma">FIRMAS</th>
        </tr>
        <tr>
            <th class="col-nombre">PATERNO</th>
            <th class="col-nombre">MATERNO</th>
            <th class="col-nombre">PRIMER NOMBRE</th>
            
            <th class="col-num-small">DIAS PAG.</th>
            <th class="col-num-small">HRS PAG.</th>
            
            <th class="col-money-base">SUELDO BÁSICO</th>
            <th class="col-money-base">BONO ANTIG.</th>
            <th class="col-money-base">TRAB. EXTRA.</th>
            <th class="col-money-base">PAGO DOMINGO</th>
            <th class="col-money-base">OTROS BONOS</th>
            
            <th class="col-money-narrow">RC IVA</th>
            <th class="col-money-narrow">AFP (12.71%)</th>
            <th class="col-money-narrow">OTROS DESC.</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sumaHaberBasico = 0; $sumaBonoAntiguedad = 0; $sumaTrabExtra = 0;
            $sumaPagoDomingo = 0; $sumaOtrosBonos = 0; $sumaTotalGanado = 0;
            $sumaRcIva = 0; $sumaAporteLaboral = 0; $sumaOtrosDescuentos = 0;
            $sumaTotalDescuento = 0; $sumaLiquidoPagable = 0;
        @endphp

        @foreach ($datosPlanilla as $nomina)
            <tr>
                <td class="col-nro" style="text-align: center;">{{ $loop->iteration }}</td>
                
                <td class="col-identidad">{{ $nomina->empleado->documento_identidad }}</td>
                <td class="col-nombre">{{ $nomina->empleado->primerapellido }}</td>
                <td class="col-nombre">{{ $nomina->empleado->segundoapellido }}</td>
                <td class="col-nombre">{{ $nomina->empleado->nombres }}</td>
                <td class="col-date">{{ \Carbon\Carbon::parse($nomina->empleado->fecha_ingreso)->format('d-m-y') }}</td>
                <td class="col-num-small">{{ $nomina->dias_pagados }}</td>
                <td class="col-num-small">{{ $nomina->horas_pagadas }}</td>
                
                {{-- INGRESOS --}}
                <td class="col-money-base">{{ number_format($nomina->haber_basico, 2, ',', '.') }}</td>
                <td class="col-money-base">{{ number_format($nomina->bono_antiguedad, 2, ',', '.') }}</td>
                <td class="col-money-base">{{ number_format($nomina->trabajo_extraordinario, 2, ',', '.') }}</td>
                <td class="col-money-base">{{ number_format($nomina->pago_domingo, 2, ',', '.') }}</td>
                <td class="col-money-base">{{ number_format($nomina->otros_bonos, 2, ',', '.') }}</td>
                <td class="col-money-total">{{ number_format($nomina->total_ganado, 2, ',', '.') }}</td>

                {{-- DESCUENTOS --}}
                <td class="col-money-narrow">{{ number_format($nomina->rc_iva, 2, ',', '.') }}</td>
                <td class="col-money-narrow">{{ number_format($nomina->aporte_laboral, 2, ',', '.') }}</td>
                
                @php
                    // OTROS DESCUENTOS = Anticipos + Aporte Nacional Solidario
                    $otrosDescuentos = ($nomina->anticipos ?? 0) + ($nomina->aporte_nacional_solidario ?? 0);
                    $nominaTotalDescuentos = $nomina->total_descuentos;
                @endphp
                <td class="col-money-narrow">{{ number_format($otrosDescuentos, 2, ',', '.') }}</td>
                
                {{-- Total Descuento y Liquido Pagable --}}
                <td class="col-money-total">{{ number_format($nominaTotalDescuentos, 2, ',', '.') }}</td>
                <td class="col-money-total">{{ number_format($nomina->liquido, 2, ',', '.') }}</td>
                
                <td class="col-firma"></td>

                @php
                    // Sumas para el pie de página
                    $sumaHaberBasico += $nomina->haber_basico;
                    $sumaBonoAntiguedad += $nomina->bono_antiguedad;
                    $sumaTrabExtra += $nomina->trabajo_extraordinario;
                    $sumaPagoDomingo += $nomina->pago_domingo;
                    $sumaOtrosBonos += $nomina->otros_bonos;
                    $sumaTotalGanado += $nomina->total_ganado;
                    $sumaRcIva += $nomina->rc_iva;
                    $sumaAporteLaboral += $nomina->aporte_laboral;
                    $sumaOtrosDescuentos += $otrosDescuentos;
                    $sumaTotalDescuento += $nominaTotalDescuentos;
                    $sumaLiquidoPagable += $nomina->liquido;
                @endphp
            </tr>
        @endforeach

        {{-- Fila de Sumas Totales --}}
        <tr class="sumas-totales">
            <td colspan="5" style="text-align: center;">SUMAS TOTALES</td>
            <td colspan="3"></td> {{-- F. INGRESO, DIAS PAG., HRS PAG. --}}
            <td class="col-money-base">{{ number_format($sumaHaberBasico, 2, ',', '.') }}</td>
            <td class="col-money-base">{{ number_format($sumaBonoAntiguedad, 2, ',', '.') }}</td>
            <td class="col-money-base">{{ number_format($sumaTrabExtra, 2, ',', '.') }}</td>
            <td class="col-money-base">{{ number_format($sumaPagoDomingo, 2, ',', '.') }}</td>
            <td class="col-money-base">{{ number_format($sumaOtrosBonos, 2, ',', '.') }}</td>
            <td class="col-money-total">{{ number_format($sumaTotalGanado, 2, ',', '.') }}</td>
            <td class="col-money-narrow">{{ number_format($sumaRcIva, 2, ',', '.') }}</td>
            <td class="col-money-narrow">{{ number_format($sumaAporteLaboral, 2, ',', '.') }}</td>
            <td class="col-money-narrow">{{ number_format($sumaOtrosDescuentos, 2, ',', '.') }}</td>
            <td class="col-money-total">{{ number_format($sumaTotalDescuento, 2, ',', '.') }}</td>
            <td class="col-money-total">{{ number_format($sumaLiquidoPagable, 2, ',', '.') }}</td>
            <td class="col-firma"></td>
        </tr>
    </tbody>
</table>

<div class="footer-seccion">
    <table class="tabla-firmas">
        <tr>
            <td class="firma-col">
                {{-- Línea de firma acortada --}}
                <div class="firma-linea"></div>
                <p style="font-weight: bold; margin-bottom: 5px;">REPRESENTANTE LEGAL</p>
                <p>FIRMA Y SELLO</p>
            </td>
        </tr>
    </table>
    
    <p class="fecha-generacion">
        Fecha: {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}
    </p>
</div>


</body>
</html>