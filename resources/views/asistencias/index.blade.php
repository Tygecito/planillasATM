@extends('layouts.app')

@section('title', 'Control de Asistencia')

@section('content')

    <h1 class="welcome-message">Gestión de Asistencia - Carga de Datos Biométricos</h1>

    @if (session('success'))
        <div class="card" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
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
                        @php
                            $meses = [
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            ];
                        @endphp
                        @foreach ($meses as $num => $nombre)
                            <option value="{{ $num }}" {{ (int)date('m') === $num ? 'selected' : '' }}>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="anio_a_procesar" style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Año a Procesar:</label>
                    <select name="anio_a_procesar" id="anio_a_procesar" required class="filter-section select" style="width: 100%;">
                        <option value="">-- Selecciona el Año --</option>
                        @php
                            $anioActual = date('Y');
                            $anioInicio = $anioActual - 2;
                        @endphp
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
    
    <div class="card" style="margin-top: 2rem;">
        <h2><i class="fas fa-list-alt"></i> Historial de Importaciones</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Fecha de Carga</th>
                        <th>Archivo</th>
                        <th>Mes Procesado</th>
                        <th>Registros</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" style="text-align: center;">No hay datos de importación recientes.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    @endpush