<!DOCTYPE html>
<html>
<head>
    <title>Planilla RC IVA - {{ $mes }} {{ $anio }}</title>
    
    @php
        // --- CORRECCIÓN CRÍTICA ---
        // Recuperamos el SMN del primer registro de la planilla, si existe.
        // Usamos 2750.00 como fallback si no hay registros.
        $smn_base_reporte = $datosPlanilla->first()->smn ?? 2750.00; 
        
        $smn_x_2 = $smn_base_reporte * 2;
        $tasa_rc_iva = 0.13;
        
        // D.S. 5383: El crédito fijo se calcula sobre 1 SMN
        $credito_fijo_ds5383 = $smn_base_reporte * $tasa_rc_iva;
        
        // Lógica para codificar el logo en Base64
        $logoPath = public_path('img/logo.png');
        $logoSrc = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        }
        
        // Configuración de Carbon
        \Carbon\Carbon::setLocale('es');
        $mes_anio = strtoupper($mes) . '-' . substr($anio, 2); 
        $nov_edad = 0; // Asumido
    @endphp

    <style>
        /* Estilos CSS para el PDF */
        body { font-family: Arial, sans-serif; font-size: 5.5pt; margin: 5mm; padding: 0; }
        .header-empresa { display: flex; align-items: center; margin-bottom: 5px; border-bottom: 1px solid #333; padding-bottom: 5px; }
        .header-empresa .logo { width: 40px; height: auto; margin-right: 5px; vertical-align: middle; }
        .razon-social { font-size: 8pt; font-weight: bold; color: #942044; margin: 0; line-height: 1; }
        h1 { margin: 0; font-size: 10pt; color: #333; line-height: 1.1; text-align: center; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 5px; }
        th, td { border: 1px solid #333; padding: 1px 1.5px; text-align: center; vertical-align: middle; word-wrap: break-word; font-size: 5pt; height: 10px; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .sumas-totales td { font-weight: bold; background-color: #000; color: white; text-align: center; }
        .w-idx { width: 2.5%; } 
        .w-ci { width: 4.5%; } 
        .w-names { width: 7.5%; }
        .w-base { width: 6.0%; }
        .w-money-base { width: 5.0%; }
        .w-saldo { width: 5.0%; }
        .w-pago { width: 4.5%; }
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
                <th class="w-ci">CÓDIGO DEPENDIENTE RC-IVA</th>
                <th class="w-names">PATERNO</th>
                <th class="w-names">MATERNO</th>
                <th class="w-names">NOMBRES</th>
                <th class="w-ci">CÉDULA DE IDENTIDAD</th>
                <th class="w-ci">TIPO DOCUMENTO</th>
                <th class="w-date">NOV. EDAD (comp/incorp)</th>
                
                {{-- COLUMNAS BASE --}}
                <th class="w-base">BASE IMPONIBLE (Sueldo Neto)</th>
                <th class="w-base">MÍNIMO NO IMPONIBLE (2 SMN)</th>
                <th class="w-base">IMPORTE SUJETO IMPUESTO</th>
                <th class="w-money-base">RC IVA (13% Impuesto Bruto)</th>
                
                {{-- COLUMNAS CRÉDITOS --}}
                <th class="w-money-base">CRÉDITO FIJO (13% 1SMN)</th>
                <th class="w-money-base">IMPUESTO NETO (a compensar)</th>
                <th class="w-money-base">F-110 (Monto Crédito)</th>
                
                {{-- COLUMNAS SALDOS Y RETENCIÓN --}}
                <th class="w-saldo">SALDO A FAVOR DEL DEP. (Período Actual)</th>
                <th class="w-saldo">SALDO ANTERIOR (Valor Guardado)</th>
                <th class="w-pago">ACTUALIZACIÓN</th>
                <th class="w-pago">SALDO UTILIZADO</th>
                <th class="w-pago">IMPUEST RETENIDO (RC-IVA Final)</th>
                <th class="w-saldo">SALDO FINAL A FAVOR DEL DEP.</th>
            </tr>
        </thead>
        <tbody>
            @php
                // --- INICIALIZACIÓN DE SUMAS ---
                $sumaBaseImponible = 0;
                $sumaMinimoImponible = 0;
                $sumaImpuestoSujeto = 0;
                $sumaImpuestoBruto = 0;
                $sumaCreditoFijo = 0;
                $sumaImpuestoNeto = 0;
                $sumaF110 = 0;
                $sumaSaldoFavorDep = 0; 
                $sumaSaldoAnterior = 0;
                $sumaSaldoUtilizado = 0;
                $sumaImpuestoRetenido = 0;
                $sumaSaldoFinal = 0;
            @endphp

            @foreach ($datosPlanilla as $nomina)
                @php
                    // 1. OBTENER DATOS DE LA NOMINA (Usamos el SMN individual)
                    $smn_record = $nomina->smn ?? $smn_base_reporte; // Usa el SMN del registro o el valor base.
                    $smn_x_2_record = $smn_record * 2;
                    $credito_fijo_ds5383_record = $smn_record * $tasa_rc_iva;
                    
                    $tg = $nomina->total_ganado ?? 0;
                    $aporte_laboral = $nomina->aporte_laboral ?? 0;
                    $aporte_nacional_solidario = $nomina->aporte_nacional_solidario ?? 0;
                    
                    // INPUTS DESDE LA DB (Nuevas columnas)
                    $f110_monto_bruto = $nomina->rc_iva_f110_monto ?? 0;
                    $saldo_anterior = $nomina->rc_iva_saldo_anterior ?? 0;
                    
                    // OUTPUTS DESDE LA DB
                    $impuesto_retenido_final = $nomina->rc_iva ?? 0; 
                    $saldo_siguiente_final = $nomina->rc_iva_saldo_siguiente ?? 0;

                    // 2. CÁLCULO INTERMEDIO
                    $remuneracion_neta = $tg - ($aporte_laboral + $aporte_nacional_solidario); // COL 11: BASE IMPONIBLE
                    $importe_sujeto_impuesto = max(0, $remuneracion_neta - $smn_x_2_record); // COL 13
                    $impuesto_bruto = $importe_sujeto_impuesto * $tasa_rc_iva; // COL 14

                    // 3. CÁLCULO DE CRÉDITOS
                    $credito_f110_valor = $f110_monto_bruto * $tasa_rc_iva; // Columna 17 (F110)
                    $total_creditos_compensables = $credito_fijo_ds5383_record + $credito_f110_valor;
                    
                    // Impuesto Neto (Impuesto Bruto - Créditos)
                    $impuesto_neto = max(0, $impuesto_bruto - $total_creditos_compensables); // COLUMNA 16

                    // 4. SALDO UTILIZADO (Cuánto del Saldo Anterior se usó para cubrir el Impuesto Neto)
                    $saldo_utilizar = min($saldo_anterior, $impuesto_neto); // COL 21
                    
                    // 5. Saldo a Favor del Dependiente (COL 18: Créditos actuales no utilizados)
                    $saldo_favor_dep = max(0, $total_creditos_compensables - $impuesto_bruto); 
                    
                @endphp
                
                <tr>
                    <td class="w-idx">{{ $loop->iteration }}</td>
                    <td class="w-ci">{{ $nomina->mes }}</td>
                    <td class="w-ci">{{ $nomina->anio }}</td>
                    <td class="w-ci">{{ $nomina->empleado->nit_dependiente ?? $nomina->empleado->documento_identidad }}</td> {{-- NIT o CI --}}
                    <td class="w-names text-left">{{ $nomina->empleado->primerapellido }}</td>
                    <td class="w-names text-left">{{ $nomina->empleado->segundoapellido }}</td>
                    <td class="w-names text-left">{{ $nomina->empleado->nombres }}</td>
                    <td class="w-ci">{{ $nomina->empleado->documento_identidad }}</td>
                    <td class="w-ci">CI</td> 
                    <td class="w-date">{{ $nov_edad }}</td>
                    
                    {{-- COLUMNAS BASE E IMPUESTO --}}
                    <td class="w-base text-right">{{ number_format($remuneracion_neta, 2, ',', '.') }}</td>
                    <td class="w-base text-right">{{ number_format($smn_x_2_record, 2, ',', '.') }}</td> {{-- CORREGIDO --}}
                    <td class="w-base text-right">{{ number_format($importe_sujeto_impuesto, 2, ',', '.') }}</td>
                    <td class="w-money-base text-right">{{ number_format($impuesto_bruto, 2, ',', '.') }}</td>
                    
                    {{-- COLUMNAS CRÉDITOS --}}
                    <td class="w-money-base text-right">{{ number_format($credito_fijo_ds5383_record, 2, ',', '.') }}</td> {{-- CORREGIDO --}}
                    <td class="w-money-base text-right">{{ number_format($impuesto_neto, 2, ',', '.') }}</td>
                    <td class="w-money-base text-right">{{ number_format($credito_f110_valor, 2, ',', '.') }}</td>
                    
                    {{-- COLUMNAS SALDOS Y RETENCIÓN --}}
                    <td class="w-saldo text-right">{{ number_format($saldo_favor_dep, 2, ',', '.') }}</td>
                    <td class="w-saldo text-right">{{ number_format($saldo_anterior, 2, ',', '.') }}</td>
                    
                    <td class="w-pago text-right">{{ number_format(0, 2, ',', '.') }}</td> {{-- Actualización (UFV) --}}
                    <td class="w-pago text-right">{{ number_format($saldo_utilizar, 2, ',', '.') }}</td>
                    <td class="w-pago text-right">{{ number_format($impuesto_retenido_final, 2, ',', '.') }}</td> {{-- Valor Final de la DB --}}
                    
                    <td class="w-saldo text-right">{{ number_format($saldo_siguiente_final, 2, ',', '.') }}</td> {{-- Valor Final de la DB --}}
                </tr>
                
                {{-- Sumas para la fila TOTALES --}}
                @php
                    $sumaBaseImponible += $remuneracion_neta;
                    $sumaMinimoImponible += $smn_x_2_record; // Sumar el valor individual
                    $sumaImpuestoSujeto += $importe_sujeto_impuesto;
                    $sumaImpuestoBruto += $impuesto_bruto;
                    $sumaCreditoFijo += $credito_fijo_ds5383_record; // Sumar el valor individual
                    $sumaImpuestoNeto += $impuesto_neto;
                    $sumaF110 += $credito_f110_valor;
                    $sumaSaldoFavorDep += $saldo_favor_dep;
                    $sumaSaldoAnterior += $saldo_anterior;
                    $sumaSaldoUtilizado += $saldo_utilizar;
                    $sumaImpuestoRetenido += $impuesto_retenido_final;
                    $sumaSaldoFinal += $saldo_siguiente_final;
                @endphp
            @endforeach

            {{-- Fila de Sumas Totales --}}
            <tr class="sumas-totales">
                <td colspan="10" class="text-left" style="text-align: left; font-size: 6pt; padding-left: 5px;">**TOTALES**</td>
                <td class="w-base text-right">{{ number_format($sumaBaseImponible, 2, ',', '.') }}</td>
                <td class="w-base text-right">{{ number_format($sumaMinimoImponible, 2, ',', '.') }}</td>
                <td class="w-base text-right">{{ number_format($sumaImpuestoSujeto, 2, ',', '.') }}</td>
                
                <td class="w-money-base text-right">{{ number_format($sumaImpuestoBruto, 2, ',', '.') }}</td>
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