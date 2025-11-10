@extends('layouts.app')

@section('title', 'Control de Asistencia')

@section('content')

    <h1 class="welcome-message">Gestión de Asistencia - Carga de Datos Biométricos</h1>
    
    {{-- Bloques de Mensajes --}}
    @if (session('success'))
        <div class="card" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="card" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
            <p>Por favor, corrige los siguientes errores:</p>
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif
    @if (session('danger'))
        <div class="card" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
            <p>{{ session('danger') }}</p>
        </div>
    @endif

    {{-- BOTÓN DE REPORTE --}}
    <div style="margin-bottom: 1.5rem; text-align: right;">
        <a href="{{ route('asistencias.reporte') }}" class="btn btn-info">
            <i class="fas fa-chart-line"></i> Ir a Reporte Mensual
        </a>
    </div>

    <div class="card">
        <h2><i class="fas fa-file-upload"></i> Cargar Marcaciones de Biométrico</h2>
        <p>Selecciona el archivo Excel y el periodo deseado para procesar únicamente los registros de ese mes y año.</p>

        <form action="{{ route('asistencias.import') }}" method="POST" enctype="multipart/form-data" class="form-import-asistencia">
            @csrf

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="excel_file" style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Archivo de Marcaciones (Excel):</label>
                <input type="file" name="excel_file" id="excel_file" required 
                       accept=".xlsx, .xls, .csv" 
                       style="border: 1px solid #ddd; padding: 10px; border-radius: 4px; display: block; width: 100%;">
            </div>

            <div class="filter-section">
                <div class="form-group" style="flex: 1;">
                    <label for="mes_a_procesar" style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Mes a Procesar:</label>
                    <select name="mes_a_procesar" id="mes_a_procesar" required class="filter-section select" style="width: 100%;">
                        <option value="">-- Selecciona el Mes --</option>
                        @php $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre']; @endphp
                        @foreach ($meses as $num => $nombre)
                            <option value="{{ $num }}" {{ (int)date('m') === $num ? 'selected' : '' }}>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="anio_a_procesar" style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Año a Procesar:</label>
                    <select name="anio_a_procesar" id="anio_a_procesar" required class="filter-section select" style="width: 100%;">
                        <option value="">-- Selecciona el Año --</option>
                        @php $anioActual = date('Y'); $anioInicio = $anioActual - 2; @endphp
                        @for ($i = $anioActual; $i >= $anioInicio; $i--)
                            <option value="{{ $i }}" {{ $anioActual == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-cloud-upload-alt"></i> Subir y Procesar Marcaciones
            </button>
        </form>
    </div>
    
    {{-- TABLA DE RETRASOS --}}
    <div class="card" style="margin-top: 2rem;">
        <h2><i class="fas fa-list-alt"></i> Historial de Retrasos Recientes</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Fecha de Retraso</th>
                        <th>Empleado</th>
                        <th>Tipo de Retraso</th>
                        <th>Marcación</th>
                        <th>Minutos Tarde</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($retrasos as $retraso)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($retraso->fecha)->format('d/m/Y') }}</td>
                            <td>
                                @if ($retraso->empleado)
                                    {{ $retraso->empleado->nombres }} {{ $retraso->empleado->primerapellido }}
                                @else
                                    <span style="color: red;">ID Empleado: {{ $retraso->empleado_id }} (No encontrado)</span>
                                @endif
                            </td>
                            <td>
                                @if ($retraso->tipo == 'INGRESO_MANANA')
                                    <span style="color: #b08a00;">Ingreso Mañana</span>
                                @else
                                    <span style="color: #b00020;">Ingreso Tarde</span>
                                @endif
                            </td>
                            <td>
                                Límite: {{ \Carbon\Carbon::parse($retraso->hora_limite)->format('H:i') }}
                                <br>
                                Marcó: <strong>{{ \Carbon\Carbon::parse($retraso->hora_marcacion)->format('H:i:s') }}</strong>
                            </td>
                            <td><strong>{{ $retraso->minutos_retraso }} min</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">No hay retrasos registrados recientemente.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- (Aquí puedes añadir JS en el futuro) --}}
@endpush