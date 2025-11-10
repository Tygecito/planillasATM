<!DOCTYPE html>
<html>
<head>
    <title>Planilla RC IVA - {{ $mes }} {{ $anio }}</title>
    
    @php
        // Lógica para codificar el logo en Base64
        $logoPath = public_path('img/logo.png');
        $logoSrc = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        }
        
        // Configuración de Carbon para formatear la fecha en español
        \Carbon\Carbon::setLocale('es');
        $mes_anio = strtoupper($mes) . '-' . substr($anio, 2); // Ejemplo: JULIO-25
    @endphp

    <style>
        /* Estilos CSS para el PDF - MÁXIMA COMPRESIÓN y DISEÑO SIMPLE (RC-IVA) */
        body {
            font-family: Arial, sans-serif;
            font-size: 5.5pt; 
            margin: 5mm; /* Márgenes mínimos */
            padding: 0;
        }
        
        /* === ENCABEZADO === */
        .header-empresa {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        .header-empresa .logo {
            width: 40px; 
            height: auto;
            margin-right: 5px;
            vertical-align: middle;
        }
        .header-text {
            text-align: left;
            flex-grow: 1;
        }
        .razon-social {
            font-size: 8pt;
            font-weight: bold;
            color: #942044;
            margin: 0;
            line-height: 1;
        }
        h1 {
            margin: 0;
            font-size: 10pt;
            color: #333;
            line-height: 1.1;
            text-align: center; /* TÍTULO CENTRADO */
            margin-top: 10px; /* Separación después del logo/razón social */
        }
        .header-info {
            text-align: center;
            margin-bottom: 5px;
            font-size: 7pt;
        }

        /* === TABLA DE PLANILLA (Diseño simple de una fila de encabezado) === */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; 
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #333;
            padding: 1px 1.5px; /* Padding ultra-reducido */
            text-align: center; 
            vertical-align: middle;
            word-wrap: break-word; 
            font-size: 5pt; /* Fuente muy pequeña */
            height: 10px; /* Altura mínima de fila */
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        /* Celdas específicas */
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .sumas-totales td {
            font-weight: bold;
            background-color: #000; /* Fondo negro */
            color: white;
            text-align: center;
        }
        .sumas-totales .total-numeric {
            background-color: #000;
            color: white;
            text-align: right !important;
        }

        /* AJUSTE DE ANCHOS DE COLUMNA (TOTAL 100%) */
        /* Columnas combinadas de la imagen: PERIODO, CI, NOMBRES, TIPO DOC, etc. */
        
        .w-idx { width: 2.5%; } 
        .w-ci { width: 5%; } 
        .w-names { width: 14%; }
        .w-date { width: 4.5%; }
        .w-base { width: 6.5%; }
        .w-money-small { width: 4.2%; }
        .w-money-base { width: 5.5%; }
        .w-money-total { width: 6.5%; }
        .w-saldo { width: 5%; }
        .w-pago { width: 4%; }
    </style>
</head>
<body>

    <div class="header-empresa">
        @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logo" class="logo">
        @endif
        <div class="header-text">
            <p class="razon-social">IMPORTADORA Y LABORATORIO ATM S.R.L.</p>
            <h1>PLANILLA DE RÉGIMEN COMPLEMENTARIO<br>IMPUESTO AL VALOR AGREGADO (RC-IVA)</h1>
        </div>
    </div>
    
    <div style="text-align: center; font-size: 7pt; margin-bottom: 10px;">
        CORRESPONDIENTE AL MES DE: <span style="font-weight: bold;">{{ $mes_anio }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="w-idx">N°</th>
                <th class="w-ci">PERIODO</th>
                <th class="w-ci">AÑO</th>
                <th class="w-ci">CODIGO DEPENDIENTE RC-IVA</th>
                <th class="w-names">PATERNO</th>
                <th class="w-names">MATERNO</th>
                <th class="w-names">NOMBRES</th>
                <th class="w-ci">CÉDULA DE IDENTIDAD</th>
                <th class="w-ci">TIPO DOCUMENTO</th>
                <th class="w-date">NOV. EDAD (comp/incorp)</th>
                
                <th class="w-base">BASE IMPONIBLE</th>
                <th class="w-base">MÍNIMO NO IMPONIBLE</th>
                <th class="w-base">IMPORTE SUJETO IMPUESTO</th>
                <th class="w-money-base">RC IVA</th>
                <th class="w-money-base">13% 2SMN</th>
                <th class="w-money-base">IMPUESTO NETO RC-IVA</th>
                <th class="w-money-base">F-110</th>
                
                <th class="w-saldo">SALDO A FAVOR DEL DEPENDIENTE</th>
                <th class="w-saldo">SALDO ANTERIOR</th>
                <th class="w-pago">ACTUALIZACIÓN</th>
                <th class="w-pago">SALDO UTILIZADO</th>
                <th class="w-pago">IMPUEST RETENIDO</th>
                <th class="w-saldo">SALDO FINAL A FAVOR DEL DEP.</th>
            </tr>
        </thead>
        <tbody>
            @php
                // --- INICIALIZACIÓN DE SUMAS ---
                $sumaBaseImponible = 0;
                $sumaMinimoImponible = 0;
                $sumaImpuestoSujeto = 0;
                $sumaRcIva = 0;
                $sumaImpuestoNeto = 0;
                $sumaF110 = 0;
                $sumaSaldoFavorDep = 0;
                $sumaSaldoAnterior = 0;
                $sumaSaldoUtilizado = 0;
                $sumaImpuestoRetenido = 0;
                $sumaSaldoFinal = 0;
                $sumaCreditoFijo = 0;

                // Valores Fijos para el cálculo
                $smn = 2750.00; 
                $smn_x_2 = $smn * 2;
                $credito_fijo = $smn_x_2 * 0.13;
                $tasa_rc_iva = 0.13;
                $nov_edad = 0; // Asumido
            @endphp

            @foreach ($datosPlanilla as $nomina)
                @php
                    $tg = $nomina->total_ganado;
                    $remuneracion_neta = $tg - ($nomina->aporte_laboral + $nomina->aporte_nacional_solidario); 
                    $importe_sujeto_impuesto = max(0, $remuneracion_neta - $smn_x_2);
                    $impuesto_rc_iva = $importe_sujeto_impuesto * $tasa_rc_iva; 
                    
                    // Saldo anterior simulado (tomamos un valor fijo para el ejemplo)
                    $saldo_anterior = ($nomina->anticipos ?? 0); // Usamos anticipos para simular Saldo Anterior

                    $impuesto_neto = max(0, $impuesto_rc_iva - $credito_fijo); 
                    $f110 = 0; // Asumido
                    
                    // Cálculo de saldos y pagos
                    $saldo_utilizar = min($saldo_anterior, $impuesto_neto);
                    $impuesto_retenido = max(0, $impuesto_neto - $saldo_utilizar);
                    $saldo_final = $saldo_anterior - $saldo_utilizar;

                    $saldo_favor_dep = max(0, $saldo_anterior - $impuesto_neto);
                    $saldo_fisco = max(0, $impuesto_neto - $saldo_anterior);
                @endphp
                
                <tr>
                    <td class="w-idx">{{ $loop->iteration }}</td>
                    <td class="w-ci">{{ $nomina->mes . '-' . substr($nomina->anio, 2) }}</td>
                    <td class="w-ci">{{ $nomina->anio }}</td> <td class="w-ci">{{ $nomina->empleado->documento_identidad }}</td>
                    <td class="w-names text-left">{{ $nomina->empleado->primerapellido }}</td>
                    <td class="w-names text-left">{{ $nomina->empleado->segundoapellido }}</td>
                    <td class="w-names text-left">{{ $nomina->empleado->nombres }}</td>
                    <td class="w-ci">{{ $nomina->empleado->documento_identidad }}</td>
                    <td class="w-ci">CI</td> 
                    <td class="w-date">{{ $nov_edad }}</td>
                    
                    <td class="w-base text-right">{{ number_format($remuneracion_neta, 2, ',', '.') }}</td>
                    <td class="w-base text-right">{{ number_format($smn_x_2, 2, ',', '.') }}</td>
                    <td class="w-base text-right">{{ number_format($importe_sujeto_impuesto, 2, ',', '.') }}</td>
                    <td class="w-money-base text-right">{{ number_format($impuesto_rc_iva, 2, ',', '.') }}</td>
                    <td class="w-money-base text-right">{{ number_format($credito_fijo, 2, ',', '.') }}</td>
                    <td class="w-money-base text-right">{{ number_format($impuesto_neto, 2, ',', '.') }}</td>
                    <td class="w-money-base text-right">{{ number_format($f110, 2, ',', '.') }}</td>
                    
                    <td class="w-saldo text-right">{{ number_format($saldo_favor_dep, 2, ',', '.') }}</td>
                    <td class="w-saldo text-right">{{ number_format($saldo_anterior, 2, ',', '.') }}</td>
                    
                    <td class="w-pago text-right">{{ number_format(0, 2, ',', '.') }}</td> {{-- Actualización --}}
                    <td class="w-pago text-right">{{ number_format($saldo_utilizar, 2, ',', '.') }}</td>
                    <td class="w-pago text-right">{{ number_format($impuesto_retenido, 2, ',', '.') }}</td>
                    
                    <td class="w-saldo text-right">{{ number_format($saldo_final, 2, ',', '.') }}</td>
                </tr>
                
                @php
                    $sumaBaseImponible += $remuneracion_neta;
                    $sumaMinimoImponible += $smn_x_2;
                    $sumaImpuestoSujeto += $importe_sujeto_impuesto;
                    $sumaRcIva += $impuesto_rc_iva;
                    $sumaCreditoFijo += $credito_fijo;
                    $sumaImpuestoNeto += $impuesto_neto;
                    $sumaF110 += $f110;
                    $sumaSaldoFavorDep += $saldo_favor_dep;
                    $sumaSaldoAnterior += $saldo_anterior;
                    $sumaSaldoUtilizado += $saldo_utilizar;
                    $sumaImpuestoRetenido += $impuesto_retenido;
                    $sumaSaldoFinal += $saldo_final;
                @endphp
            @endforeach

            {{-- Fila de Sumas Totales (tu colspan="10" ya era correcto) --}}
            <tr class="sumas-totales">
                <td colspan="10" class="text-left" style="text-align: left; font-size: 6pt; padding-left: 5px;">**TOTALES**</td>
                <td class="w-base text-right">{{ number_format($sumaBaseImponible, 2, ',', '.') }}</td>
                <td class="w-base text-right">{{ number_format($sumaMinimoImponible, 2, ',', '.') }}</td>
                <td class="w-base text-right">{{ number_format($sumaImpuestoSujeto, 2, ',', '.') }}</td>
                
                <td class="w-money-base text-right">{{ number_format($sumaRcIva, 2, ',', '.') }}</td>
                <td class="w-money-base text-right">{{ number_format($sumaCreditoFijo, 2, ',', '.') }}</td>
                <td class="w-money-base text-right">{{ number_format($sumaImpuestoNeto, 2, ',', '.') }}</td>
                <td class="w-money-base text-right">{{ number_format($sumaF110, 2, ',', '.') }}</td>
                
                <td class="w-saldo text-right">{{ number_format($sumaSaldoFavorDep, 2, ',', '.') }}</td>
                <td class="w-saldo text-right">{{ number_format($sumaSaldoAnterior, 2, ',', '.') }}</td>
                <td class="w-pago text-right">{{ number_format(0, 2, ',', '.') }}</td>
                <td class="w-pago text-right">{{ number_format($sumaSaldoUtilizado, 2, ',', '.') }}</td>
                <td class="w-pago text-right">{{ number_format($sumaImpuestoRetenido, 2, ',', '.') }}</td>
                
                <td class="w-saldo text-right">{{ number_format($sumaSaldoFinal, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="footer-seccion">
        <table class="tabla-firmas">
            <tr>
                <td class="firma-col">
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