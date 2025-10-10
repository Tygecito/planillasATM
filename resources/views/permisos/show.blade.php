@extends('layouts.app')

@section('title', 'Detalle del Permiso - ' . $permiso->id)

@section('content')

<!-- Estilos dedicados para esta vista (usando los colores de su app.css: #942044) -->
<style>
    /* Estructura de la tarjeta */
    .permiso-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin: 2rem auto;
        max-width: 900px;
        overflow: hidden;
        border: 1px solid #ddd;
    }
    
    /* Encabezado: Usa el color principal de su aplicación */
    .card-header {
        background-color: #942044; /* Color principal de su app.css */
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .card-header h1 {
        font-size: 1.75rem;
        font-weight: bold;
    }
    .card-content {
        padding: 2rem;
    }

    /* Títulos de sección */
    .section-title {
        font-size: 1.3rem;
        font-weight: bold;
        color: #942044; /* Color de acento */
        border-bottom: 2px solid #eee;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }

    /* Estructura de la cuadrícula (para detalles) */
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .detail-item strong {
        display: block;
        color: #333;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
        font-weight: bold;
    }
    .detail-item span {
        font-size: 1rem;
        color: #555;
    }
    .dias-solicitados {
        font-size: 2rem;
        font-weight: bold;
        color: #942044;
    }
    
    /* Caja de información del empleado */
    .info-box {
        background-color: #f7f7f7; /* Fondo sutil */
        border: 1px solid #ddd; 
        border-radius: 6px;
        padding: 1.25rem;
        margin-bottom: 2rem;
    }
    .info-box h3 {
        font-size: 1.25rem; 
        font-weight: bold; 
        color: #333; 
        margin-bottom: 1rem;
    }

    /* Caja de motivo */
    .motivo-box {
        background-color: #f7f7f7;
        border: 1px solid #ddd;
        padding: 1rem;
        border-radius: 6px;
        white-space: pre-wrap;
        color: #555;
    }

    /* Estilos de Badges (usa colores específicos para estados) */
    .badge {
        padding: 0.4rem 0.8rem;
        border-radius: 16px;
        font-weight: bold;
        font-size: 0.8rem;
        text-transform: uppercase;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    /* Mapeo a los colores de su CSS (.vacation-status) o colores de estado estándar */
    .badge-PENDIENTE { background-color: #fff3cd; color: #856404; } /* Similar a .pending */
    .badge-APROBADO { background-color: #d4edda; color: #155724; } /* Similar a .approved */
    .badge-RECHAZADO { background-color: #f8d7da; color: #721c24; } /* Similar a .rejected */
    .badge-CANCELADO { background-color: #e0e0e0; color: #424242; }

    /* Botón de regreso */
    .btn-container {
        padding-top: 1.5rem; 
        border-top: 1px solid #eee; 
        text-align: right;
    }
    /* La clase .btn .btn-primary se usa directamente desde su app.css */
</style>

<div class="permiso-card">
    
    <!-- HEADER VISUAL (usa el color #942044) -->
    <div class="card-header">
        <h1>SOLICITUD DE PERMISO #{{ $permiso->id }}</h1>
         @php
            // Se usa la clase badge-ESTADO definida en el <style>
            $statusClass = 'badge-' . $permiso->estado;
        @endphp
        <span class="badge {{ $statusClass }}">{{ $permiso->estado }}</span>
    </div>
    
    <!-- CONTENIDO INTERIOR -->
    <div class="card-content">
        <!-- Título secundario (Tipo de Permiso) -->
        <h2 class="section-title">{{ str_replace('_', ' ', $permiso->tipo_permiso) }}</h2>
        
        <!-- INFORMACIÓN DEL EMPLEADO -->
        <div class="info-box">
            <h3>Información del Solicitante</h3>
            <div class="detail-grid">
                <p class="detail-item"><strong>Nombre Completo:</strong> <span>{{ $permiso->empleado->nombres }} {{ $permiso->empleado->primerapellido }} {{ $permiso->empleado->segundoapellido }}</span></p>
                <p class="detail-item"><strong>C.I.:</strong> <span>{{ $permiso->empleado->documento_identidad }}</span></p>
                <p class="detail-item"><strong>Cargo:</strong> <span>{{ $permiso->empleado->cargo_laboral }}</span></p>
            </div>
        </div>
        
        <!-- RESUMEN DE DÍAS DE VACACIONES (NUEVA SECCIÓN) -->
        <div class="info-box" style="background-color: #fff7e6; border-color: #ffebcc; border-left: 5px solid #ffaa00;">
            <h3>Resumen de Vacaciones (Saldo)</h3>
            <p class="detail-item">
                <!-- Se asume que $permiso->empleado->saldo_vacaciones existe. AJUSTE ESTA VARIABLE SI ES NECESARIO. -->
                @php
                    $saldo = $permiso->empleado->saldo_vacaciones ?? 0.0; // Usar 0.0 si no existe la variable
                    // Formato sin ceros a la derecha y pluralización
                    $formattedSaldo = rtrim(rtrim(number_format($saldo, 3, '.', ''), '0'), '.');
                    $unidadSaldo = ($saldo == 1.0 || $saldo == 1) ? 'día disponible' : 'días disponibles';
                @endphp
                <strong>Días de Vacaciones Restantes:</strong> <span class="dias-solicitados" style="color: #ffaa00;">{{ $formattedSaldo }} {{ $unidadSaldo }}</span>
            </p>
            <p style="font-size: 0.85rem; color: #856404; margin-top: 0.5rem;">
                * Este es el saldo antes de aplicar esta solicitud.
            </p>
        </div>
        <!-- FIN RESUMEN DE DÍAS DE VACACIONES -->
        
        
        <!-- DETALLES DE LA SOLICITUD -->
        <h3 class="section-title">Detalles del Período</h3>
        <div class="detail-grid">
            <p class="detail-item"><strong>Fecha Solicitud:</strong> <span>{{ \Carbon\Carbon::parse($permiso->fecha_solicitud)->format('d/m/Y') }}</span></p>
            
            <p class="detail-item"><strong>Inicio:</strong> <span>{{ \Carbon\Carbon::parse($permiso->fecha_inicio)->format('d/m/Y') }}</span></p>
            <p class="detail-item"><strong>Fin:</strong> <span>{{ \Carbon\Carbon::parse($permiso->fecha_fin)->format('d/m/Y') }}</span></p>
            
            <div class="detail-item">
                @php
                    $dias = $permiso->dias_solicitados;
                    // Formatear el número para eliminar los ceros a la derecha y los puntos innecesarios (ej: 2.000 -> 2)
                    // Usamos number_format con 3 decimales, luego rtrim para quitar ceros y puntos/comas si es necesario.
                    $formattedDays = rtrim(rtrim(number_format($dias, 3, '.', ''), '0'), '.');
                    
                    // Decidir si usar 'día' o 'días'
                    $unidad = ($dias == 1.0 || $dias == 1) ? 'día' : 'días';
                @endphp
                <strong>Días Solicitados:</strong> <span class="dias-solicitados">{{ $formattedDays }} {{ $unidad }}</span>
            </div>

            @if ($permiso->hora_inicio)
                <p class="detail-item"><strong>Horario:</strong> <span>{{ $permiso->hora_inicio }} - {{ $permiso->hora_fin }}</span></p>
            @endif

            @if ($permiso->duracion_horas)
                <p class="detail-item"><strong>Duración Total (Horas):</strong> <span>{{ number_format($permiso->duracion_horas, 2) }} hrs.</span></p>
            @endif

        </div>

        <!-- MOTIVO -->
        <div style="margin-bottom: 2rem;">
            <h3 class="section-title">Motivo de la Solicitud</h3>
            <div class="motivo-box">
                <p>{{ $permiso->motivo }}</p>
            </div>
        </div>
        
        <!-- Botón de Regreso -->
        <div class="btn-container">
            <a href="{{ route('permisos.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>

</div>
@endsection
