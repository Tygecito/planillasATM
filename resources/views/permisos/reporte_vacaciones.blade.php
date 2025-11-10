@extends('layouts.app')

@section('title', 'Reporte de Saldo de Vacaciones')

@section('content')

<style>
    /* Estilos base (puedes copiarlos de tu index.blade.php) */
    .card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }
    .table-container {
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #f7f7f7;
        font-weight: bold;
        color: #333;
    }
    tr:hover {
        background-color: #f0f0f0;
    }
    /* Clases de estado (adaptadas de tu index.blade.php) */
    .vacation-status {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
        display: inline-block;
        min-width: 70px; /* Asegura un ancho mínimo */
        text-align: center;
    }
    .approved { background-color: #d4edda; color: #155724; } /* Verde para saldo positivo */
    .pending { background-color: #fff3cd; color: #856404; } /* Amarillo/Naranja para derecho */
    .rejected { background-color: #f8d7da; color: #721c24; } /* Rojo para saldo cero o negativo */
</style>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>📊 Reporte de Saldo de Vacaciones (Ley Boliviana)</h2>
        <a href="{{ route('permisos.index') }}" class="btn btn-secondary">
            <i class="fas fa-list"></i> Volver a Solicitudes
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Fecha Ingreso</th>
                    <th>Antigüedad</th>
                    <th>Derecho Anual</th>
                    <th>**SALDO DISPONIBLE**</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($empleados as $empleado)
                    @php
                        // Obtenemos los datos clave del Modelo Empleado
                        $antiguedad = $empleado->getAntiguedadEnAnios();
                        $derechoAnual = $empleado->calcularDerechoAnual();
                        $saldo = $empleado->getSaldoVacaciones();
                        
                        // Formato de saldo: ej. 15, 20.5, 3.125
                        $formattedSaldo = rtrim(rtrim(number_format($saldo, 3, '.', ''), '0'), '.');
                        
                        // Determinar la clase para resaltar el saldo
                        $claseSaldo = match(true) {
                            $saldo > 0 => 'approved',
                            $saldo == 0 && $derechoAnual > 0 => 'rejected', // Saldo 0, pero con derecho
                            default => 'rejected', // Saldo 0 o empleado sin derecho
                        };
                    @endphp
                    <tr>
                        <td>{{ $empleado->nombres }} {{ $empleado->primerapellido }} {{ $empleado->segundoapellido }}</td>
                        <td>{{ \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') }}</td>
                        <td>
                            @if ($antiguedad > 0)
                                **{{ $antiguedad }} años**
                            @else
                                Menos de 1 año
                            @endif
                        </td>
                        <td>
                            <span class="vacation-status {{ ($derechoAnual > 0) ? 'pending' : 'rejected' }}" style="{{ ($derechoAnual > 0) ? 'background-color: #ffe0b2; color: #e65100;' : '' }}">
                                **{{ $derechoAnual }} días**
                            </span>
                        </td>
                        <td>
                            <span class="vacation-status {{ $claseSaldo }}">
                                **{{ $formattedSaldo }} días**
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No se encontraron empleados activos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection