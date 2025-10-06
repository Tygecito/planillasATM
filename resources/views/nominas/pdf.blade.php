<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nómina {{ $nomina->empleado->nombres }} {{ $nomina->empleado->primerapellido }}</title>

    @php
        $logoPath = public_path('img/logo.png');
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        } else {
            $logoSrc = '';
        }
    @endphp

    <style>
        /* Fuentes y márgenes generales (más compactos) */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px; 
            color: #333;
            margin: 0;
            padding: 0;
        }

        @page {
            size: letter;
            margin: 5mm; 
        }

        /* Contenedor principal de la boleta */
        .nomina-copia {
            border: 1px solid #942044; 
            border-radius: 5px; 
            padding: 5px; 
            margin: 5mm auto;
            width: 85%; 
            box-sizing: border-box;
            background: #fff;
            page-break-inside: avoid; 
        }

        /* ENCABEZADO */
        .nomina-header {
            display: flex; 
            align-items: center; 
            border-bottom: 1px solid #942044;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }

        .logo {
            width: 100px; 
            height: auto;
            margin-right: 5px;
        }

        .titulo {
            line-height: 1; 
        }

        .razon-social {
            margin: 0 0 1px 0;
            font-size: 9px;
            font-weight: bold;
            color: #555;
        }

        .titulo h2 {
            margin: 0;
            color: #942044;
            font-size: 12px;
        }

        .titulo h3 {
            margin: 0;
            font-size: 9px; 
            color: #555;
        }

        .periodo, .copia {
            margin: 0;
            font-size: 8.5px;
        }
        
        /* TÍTULOS Y TABLAS GENERALES */
        h4 {
            background: #942044;
            color: white;
            padding: 2px 4px; 
            border-radius: 3px;
            margin-top: 3px;
            margin-bottom: 2px;
            font-size: 9px;
            width: 98%; 
        }
        
        /* ESTRUCTURA DE DOS COLUMNAS USANDO TABLA */
        .tabla-dos-columnas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        
        .tabla-dos-columnas > tbody > tr > td {
            width: 50%; 
            padding: 0;
            vertical-align: top;
        }

        /* Estilos aplicables a todas las tablas anidadas */
        .detalle-tabla {
            width: 98%; 
            border-collapse: collapse;
            margin-bottom: 5px; 
        }

        .detalle-tabla th, .detalle-tabla td {
            border: 1px solid #b7b7b8;
            padding: 2px 4px; 
            font-size: 8.5px; 
        }

        .detalle-tabla th {
            background: #f1f1f1;
            text-align: left;
        }

        .total td {
            font-weight: bold;
            background: #e9ecef;
        }

        /* RESUMEN */
        .resumen-contenedor {
            width: 100%;
            text-align: center;
        }
        .resumen {
            width: 70%;
            margin: 5px auto; 
        }
        
        .resumen th, .resumen td {
             font-size: 10px;
             padding: 3px 5px;
        }
        
        .resumen td {
            background: #f8f9fa;
            font-weight: bold;
            color: #942044;
        }

        /* 🔥 CORRECCIÓN FINAL FIRMAS: Volvemos a flex, pero con contenedor de ancho y márgenes limpios. 🔥 */
        /* ... código CSS anterior (mantener todo como estaba antes, excepto las firmas) ... */

                /* Estilos de la tabla de firmas (la más estable) */
                .tabla-firmas {
                    width: 100%;
                    margin-top: 15px; 
                    border-collapse: collapse; /* Asegura que no haya bordes de tabla */
                }

                .tabla-firmas td {
                    width: 50%;
                    text-align: center;
                    padding: 0 10px; /* Separación horizontal */
                    border: none; /* Quitamos bordes de tabla */
                }

                .tabla-firmas p {
                    margin: 0;
                    font-size: 8px; 
                }
    </style>
</head>
<body>

<div class="pagina">
    <div class="nomina-copia">
        
        <div class="nomina-header">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="Logo" class="logo">
            @endif
            <div class="titulo">
                <p class="razon-social">Importadora y Laboratorio ATM S.R.L.</p>
                <h2>BOLETA DE PAGO</h2>
                <h3>Detalle de Nómina</h3>
                <p class="periodo"><strong>Periodo:</strong> {{ $nomina->mes }} / {{ $nomina->anio }}</p>
                <p class="copia">Copia Oficial</p>
            </div>
        </div>

        <h4>🧍 Datos del Empleado</h4>
        <table class="detalle-tabla">
            <tr><th>Nombre Completo</th><td>{{ $nomina->empleado->nombres }} {{ $nomina->empleado->primerapellido }}</td></tr>
            <tr><th>Documento</th><td>{{ $nomina->empleado->documento_identidad }}</td></tr>
            <tr><th>Cargo</th><td>{{ $nomina->empleado->cargo_laboral }}</td></tr>
        </table>
        
        <table class="tabla-dos-columnas">
            <tr>
                <td>
                    <h4>💰 Ingresos</h4>
                    <table class="detalle-tabla">
                        <tr><th>Concepto</th><th>Monto (Bs)</th></tr>
                        <tr><td>Haber Básico</td><td>{{ number_format($nomina->haber_basico, 2) }}</td></tr>
                        <tr><td>Bono Antigüedad</td><td>{{ number_format($nomina->bono_antiguedad, 2) }}</td></tr>
                        <tr><td>Trabajo Extraordinario</td><td>{{ number_format($nomina->trabajo_extraordinario, 2) }}</td></tr>
                        <tr><td>Pago Domingo</td><td>{{ number_format($nomina->pago_domingo, 2) }}</td></tr>
                        <tr><td>Otros Bonos</td><td>{{ number_format($nomina->otros_bonos, 2) }}</td></tr>
                        <tr class="total"><td>Total Ganado</td><td>{{ number_format($nomina->total_ganado, 2) }}</td></tr>
                    </table>
                </td>

                <td>
                    <h4>📉 Descuentos</h4>
                    <table class="detalle-tabla">
                        <tr><th>Concepto</th><th>Monto (Bs)</th></tr>
                        <tr><td>Aporte Laboral</td><td>{{ number_format($nomina->aporte_laboral, 2) }}</td></tr>
                        <tr><td>Aporte Nacional Solidario</td><td>{{ number_format($nomina->aporte_nacional_solidario, 2) }}</td></tr>
                        <tr><td>RC-IVA</td><td>{{ number_format($nomina->rc_iva, 2) }}</td></tr>
                        <tr><td>Anticipos</td><td>{{ number_format($nomina->anticipos, 2) }}</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr> 
                        <tr class="total"><td>Total Descuentos</td><td>{{ number_format($nomina->total_descuentos, 2) }}</td></tr>
                    </table>
                </td>
            </tr>
        </table> 
        <div class="resumen-contenedor">
            <h4>💵 Resumen</h4>
            <table class="resumen detalle-tabla">
                <tr><th>Líquido Pagable</th><td>**{{ number_format($nomina->liquido, 2) }} Bs**</td></tr>
            </table>
        </div>

<table class="tabla-firmas">
            <tr>
                <td>
                    <p>_________________________</p>
                    <p>**Firma del Empleado**</p>
                </td>
                <td>
                    <p>_________________________</p>
                    <p>**Firma de Gerencia**</p>
                </td>
            </tr>
        </table>
        
    </div>
</div>

</body>
</html>