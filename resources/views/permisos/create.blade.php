@extends('layouts.app')

@section('title', 'Crear Permiso')

@section('content')
<style>
    /* Estilos generales para el formulario */
    .card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin: 2rem auto;
        max-width: 700px;
        padding: 2rem;
        border-top: 5px solid #942044; /* Color principal */
    }
    .card h2 {
        font-size: 1.75rem;
        font-weight: bold;
        color: #942044;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 0.5rem;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.4rem;
        font-weight: 600;
        color: #333;
    }
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus {
        border-color: #942044;
        box-shadow: 0 0 0 3px rgba(148, 32, 68, 0.2);
        outline: none;
    }
    .text-danger {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    .error-alert {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 0.75rem;
        border-radius: 4px;
        margin-top: 0.5rem;
        font-size: 0.9rem;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .btn-primary, .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s, opacity 0.3s;
        font-weight: 600;
        text-decoration: none;
    }
    .btn-primary {
        background-color: #942044;
        color: white;
    }
    .btn-primary:hover {
        background-color: #7a1a38;
    }
    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    .btn-secondary:hover {
        background-color: #5a6268;
    }
</style>

<div class="card">
    <h2>Solicitar Nuevo Permiso</h2>
    
    <form action="{{ route('permisos.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="empleado_id">Empleado (Solo Activos):</label>
            <select name="empleado_id" id="empleado_id" class="form-control" required>
                <option value="">Seleccione un empleado</option>
                @foreach ($empleados as $empleado)
                    {{-- Usamos la comparación booleana estricta ya que se configuró el casteo en el Modelo Empleado --}}
                    @if ($empleado->estado === true) 
                        <option value="{{ $empleado->id }}" 
                            {{ old('empleado_id') == $empleado->id ? 'selected' : '' }}>
                            {{ $empleado->nombres }} {{ $empleado->primerapellido }} {{ $empleado->segundoapellido }}
                        </option>
                    @endif
                @endforeach
            </select>
            @error('empleado_id') <p class="text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="tipo_permiso">Tipo de Permiso:</label>
            <select name="tipo_permiso" id="tipo_permiso" class="form-control" required>
                <option value="">Seleccione un tipo</option>
                <option value="VACACION" {{ old('tipo_permiso') == 'VACACION' ? 'selected' : '' }}>Vacación</option>
                <option value="PERMISO_REMUNERADO" {{ old('tipo_permiso') == 'PERMISO_REMUNERADO' ? 'selected' : '' }}>Permiso Remunerado</option>
                <option value="PERMISO_POR_HORAS" {{ old('tipo_permiso') == 'PERMISO_POR_HORAS' ? 'selected' : '' }}>Permiso por Horas</option>
                <option value="LICENCIA_MEDICA" {{ old('tipo_permiso') == 'LICENCIA_MEDICA' ? 'selected' : '' }}>Licencia Médica</option>
                <option value="OTRO" {{ old('tipo_permiso') == 'OTRO' ? 'selected' : '' }}>Otro</option>
            </select>
            @error('tipo_permiso') <p class="text-danger">{{ $message }}</p> @enderror
            @error('saldo') <p class="error-alert">{{ $message }}</p> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}" required>
                @error('fecha_inicio') <p class="text-danger">{{ $message }}</p> @enderror
            </div>
            
            <div class="form-group">
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}" required>
                @error('fecha_fin') <p class="text-danger">{{ $message }}</p> @enderror
            </div>
        </div>

        <div id="horas_fields" class="form-row" style="display: none;">
            <div class="form-group">
                <label for="hora_inicio">Hora de Inicio:</label>
                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" value="{{ old('hora_inicio') }}">
                @error('hora_inicio') <p class="text-danger">{{ $message }}</p> @enderror
            </div>
            
            <div class="form-group">
                <label for="hora_fin">Hora de Fin:</label>
                <input type="time" name="hora_fin" id="hora_fin" class="form-control" value="{{ old('hora_fin') }}">
                @error('hora_fin') <p class="text-danger">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="motivo">Motivo:</label>
            <textarea name="motivo" id="motivo" class="form-control" rows="3">{{ old('motivo') }}</textarea>
            @error('motivo') <p class="text-danger">{{ $message }}</p> @enderror
        </div>

        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Solicitar Permiso
            </button>
            <a href="{{ route('permisos.index') }}" class="btn btn-secondary">
                <i class="fas fa-times-circle"></i> Cancelar
            </a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoPermisoSelect = document.getElementById('tipo_permiso');
        const horasFields = document.getElementById('horas_fields');
        const horaInicioInput = document.getElementById('hora_inicio');
        const horaFinInput = document.getElementById('hora_fin');

        // Mover el estilo de display: grid para 'horas_fields' al CSS si no está ya
        // Aquí lo ajustamos al estilo inline para mantener la funcionalidad.
        horasFields.style.display = 'grid'; 
        
        function toggleHorasFields() {
            const isPermisoPorHoras = tipoPermisoSelect.value === 'PERMISO_POR_HORAS';
            if (isPermisoPorHoras) {
                horasFields.style.display = 'grid'; // Usamos 'grid' para la disposición de columnas
                horaInicioInput.setAttribute('required', 'required');
                horaFinInput.setAttribute('required', 'required');
            } else {
                horasFields.style.display = 'none';
                horaInicioInput.removeAttribute('required');
                horaFinInput.removeAttribute('required');
            }
        }

        // Inicializar al cargar la página (útil si hay old() values)
        toggleHorasFields();

        // Escuchar cambios
        tipoPermisoSelect.addEventListener('change', toggleHorasFields);
    });
</script>
@endpush
