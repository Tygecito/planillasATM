@extends('layouts.app')

@section('title', 'Reporte de Retrasos Mensuales')

@section('content')

    <h1 class="welcome-message">Reporte de Retrasos por Periodo</h1>

    {{-- Bloque de errores --}}
    @if ($errors->any())
        <div class="card" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
            <p>Por favor, corrige los siguientes errores:</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario de Filtro --}}
    <div class="card">
        <h2><i class="fas fa-filter"></i> Filtrar Reporte</h2>
        <form action="{{ route('asistencias.generar') }}" method="POST" class="form-import-asistencia">
            @csrf

            <div class="filter-section" style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="mes_reporte" style="display: block; font-weight: bold;">Mes:</label>
                    <select name="mes_reporte" id="mes_reporte" required class="form-control" style="width: 100%;">
                        <option value="">-- Selecciona el Mes --</option>
                        @php $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre']; @endphp
                        @foreach ($meses as $num => $nombre)
                            {{-- Selecciona el mes enviado o el mes actual --}}
                            <option value="{{ $num }}" {{ (isset($mes) && $mes == $num) ? 'selected' : ((int)date('m') === $num ? 'selected' : '') }}>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="anio_reporte" style="display: block; font-weight: bold;">Año:</label>
                    <select name="anio_reporte" id="anio_reporte" required class="form-control" style="width: 100%;">
                        <option value="">-- Selecciona el Año --</option>
                        @php $anioActual = date('Y'); @endphp
                        @for ($i = $anioActual; $i >= $anioActual - 2; $i--)
                            {{-- Selecciona el año enviado o el año actual --}}
                            <option value="{{ $i }}" {{ (isset($anio) && $anio == $i) ? 'selected' : ($anioActual == $i ? 'selected' : '') }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-info" style="margin-top: 1rem;"><i class="fas fa-chart-bar"></i> Generar Reporte</button>
        </form>
    </div>

    {{-- Tabla de Resultados --}}
    @if (isset($resultados) && $resultados->count() > 0 && isset($mes) && $mes !== null)
        <div class="card" style="margin-top: 2rem;">
            <h2>Resultados del Reporte ({{ $meses[$mes] }} {{ $anio }})</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Tipo de Retraso</th>
                            <th>Total Días con Retraso</th>
                            <th>Total Minutos Tarde</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($resultados as $resultado)
                            <tr>
                                <td>{{ $resultado->empleado->nombres }} {{ $resultado->empleado->primerapellido }}</td>
                                <td>
                                    <span class="{{ $resultado->tipo == 'INGRESO_MANANA' ? 'text-warning' : 'text-danger' }}">
                                        {{ $resultado->tipo == 'INGRESO_MANANA' ? 'Ingreso Mañana' : 'Ingreso Tarde' }}
                                    </span>
                                </td>
                                <td><strong>{{ $resultado->total_retrasos }} días</strong></td>
                                <td><strong>{{ $resultado->total_minutos }} min</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
    {{-- Bloque de no resultados (corregido para no fallar en el acceso a $mes) --}}
    @elseif (isset($resultados) && isset($mes) && $mes !== null)
        <div class="card" style="margin-top: 2rem; background-color: #fff3cd; color: #856404; border-color: #ffeeba;">
            <p style="text-align: center;">No se encontraron retrasos para el periodo seleccionado ({{ $meses[$mes] }} {{ $anio }}).</p>
        </div>
    @endif

@endsection