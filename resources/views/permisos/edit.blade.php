@extends('layouts.app')

@section('title', 'Editar Permiso')

@section('content')

    <div class="card">
        <h2>Editar Solicitud de Permiso #{{ $permiso->id }}</h2>
        
        <form action="{{ route('permisos.update', $permiso) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="empleado_id">Empleado:</label>
                <select name="empleado_id" id="empleado_id" class="form-control" required>
                    <option value="">Seleccione un empleado</option>
                    @foreach ($empleados as $empleado)
                        <option value="{{ $empleado->id }}" 
                            {{ old('empleado_id', $permiso->empleado_id) == $empleado->id ? 'selected' : '' }}>
                            {{ $empleado->nombres }} {{ $empleado->primerapellido }} {{ $empleado->segundoapellido }}
                        </option>
                    @endforeach
                </select>
                @error('empleado_id') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="tipo_permiso">Tipo de Permiso:</label>
                <select name="tipo_permiso" id="tipo_permiso" class="form-control" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="VACACION" {{ old('tipo_permiso', $permiso->tipo_permiso) == 'VACACION' ? 'selected' : '' }}>Vacación</option>
                    <option value="PERMISO_REMUNERADO" {{ old('tipo_permiso', $permiso->tipo_permiso) == 'PERMISO_REMUNERADO' ? 'selected' : '' }}>Permiso Remunerado</option>
                    <option value="PERMISO_POR_HORAS" {{ old('tipo_permiso', $permiso->tipo_permiso) == 'PERMISO_POR_HORAS' ? 'selected' : '' }}>Permiso por Horas</option>
                    <option value="LICENCIA_MEDICA" {{ old('tipo_permiso', $permiso->tipo_permiso) == 'LICENCIA_MEDICA' ? 'selected' : '' }}>Licencia Médica</option>
                    <option value="OTRO" {{ old('tipo_permiso', $permiso->tipo_permiso) == 'OTRO' ? 'selected' : '' }}>Otro</option>
                </select>
                @error('tipo_permiso') <p class="text-danger">{{ $message }}</p> @enderror
                @error('saldo') <p class="text-danger" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 0.75rem; border-radius: 4px;">{{ $message }}</p> @enderror
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', $permiso->fecha_inicio) }}" required>
                    @error('fecha_inicio') <p class="text-danger">{{ $message }}</p> @enderror
                </div>
                
                <div class="form-group">
                    <label for="fecha_fin">Fecha de Fin:</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ old('fecha_fin', $permiso->fecha_fin) }}" required>
                    @error('fecha_fin') <p class="text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div id="horas_fields" class="form-row" style="display: none; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="hora_inicio">Hora de Inicio:</label>
                    <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" value="{{ old('hora_inicio', $permiso->hora_inicio ? \Carbon\Carbon::parse($permiso->hora_inicio)->format('H:i') : '') }}">
                    @error('hora_inicio') <p class="text-danger">{{ $message }}</p> @enderror
                </div>
                
                <div class="form-group">
                    <label for="hora_fin">Hora de Fin:</label>
                    <input type="time" name="hora_fin" id="hora_fin" class="form-control" value="{{ old('hora_fin', $permiso->hora_fin ? \Carbon\Carbon::parse($permiso->hora_fin)->format('H:i') : '') }}">
                    @error('hora_fin') <p class="text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="motivo">Motivo:</label>
                <textarea name="motivo" id="motivo" class="form-control" rows="3">{{ old('motivo', $permiso->motivo) }}</textarea>
                @error('motivo') <p class="text-danger">{{ $message }}</p> @enderror
            </div>
            
            <div class="form-group">
                <label for="estado">Estado:</label>
                <select name="estado" id="estado" class="form-control" required>
                    <option value="PENDIENTE" {{ old('estado', $permiso->estado) == 'PENDIENTE' ? 'selected' : '' }}>PENDIENTE</option>
                    <option value="APROBADO" {{ old('estado', $permiso->estado) == 'APROBADO' ? 'selected' : '' }}>APROBADO</option>
                    <option value="RECHAZADO" {{ old('estado', $permiso->estado) == 'RECHAZADO' ? 'selected' : '' }}>RECHAZADO</option>
                    <option value="CANCELADO" {{ old('estado', $permiso->estado) == 'CANCELADO' ? 'selected' : '' }}>CANCELADO</option>
                </select>
                @error('estado') <p class="text-danger">{{ $message }}</p> @enderror
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Permiso
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

        function toggleHorasFields() {
            const isPermisoPorHoras = tipoPermisoSelect.value === 'PERMISO_POR_HORAS';
            if (isPermisoPorHoras) {
                horasFields.style.display = 'grid';
                horaInicioInput.setAttribute('required', 'required');
                horaFinInput.setAttribute('required', 'required');
            } else {
                horasFields.style.display = 'none';
                horaInicioInput.removeAttribute('required');
                horaFinInput.removeAttribute('required');
                // Mantener valores de Old/DB pero sin la validación de required si no aplica
            }
        }

        // Inicializar al cargar la página (útil para el valor de DB)
        toggleHorasFields();

        // Escuchar cambios
        tipoPermisoSelect.addEventListener('change', toggleHorasFields);
    });
</script>
@endpush