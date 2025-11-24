@extends('layouts.app')

@section('title', 'Editar Feriado')

@section('content')

<div class="card" style="max-width: 600px; margin: 2rem auto;">
    <h2>Editar Día No Hábil: {{ $feriado->descripcion }}</h2>
    
    <form action="{{ route('feriados.update', $feriado) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" class="form-control" 
                   value="{{ old('fecha', $feriado->fecha) }}" required>
            @error('fecha') <p class="text-danger">{{ $message }}</p> @enderror
        </div>
        
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" 
                   value="{{ old('descripcion', $feriado->descripcion) }}" required>
            @error('descripcion') <p class="text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="tipo">Tipo de Exclusión:</label>
            <select name="tipo" id="tipo" class="form-control" required>
                <option value="">Seleccione un tipo</option>
                @foreach ($tipos as $tipo)
                    <option value="{{ $tipo }}" 
                        {{ old('tipo', $feriado->tipo) == $tipo ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', $tipo) }}
                    </option>
                @endforeach
            </select>
            @error('tipo') <p class="text-danger">{{ $message }}</p> @enderror
        </div>

        <div id="empleado_field" class="form-group" style="display: {{ old('tipo', $feriado->tipo) == 'CUMPLEANOS' ? 'block' : 'none' }};">
            <label for="empleado_id">Empleado Asignado (Solo para CUMPLEANOS):</label>
            <select name="empleado_id" id="empleado_id" class="form-control">
                <option value="">Seleccione un empleado</option>
                @foreach ($empleados as $empleado)
                    <option value="{{ $empleado->id }}" 
                        {{ old('empleado_id', $feriado->empleado_id) == $empleado->id ? 'selected' : '' }}>
                        {{ $empleado->nombres }} {{ $empleado->primerapellido }}
                    </option>
                @endforeach
            </select>
            @error('empleado_id') <p class="text-danger">{{ $message }}</p> @enderror
        </div>

        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sync"></i> Actualizar Feriado
            </button>
            <a href="{{ route('feriados.index') }}" class="btn btn-secondary">
                <i class="fas fa-times-circle"></i> Cancelar
            </a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    // Lógica JavaScript para mostrar/ocultar el campo Empleado
    document.addEventListener('DOMContentLoaded', function () {
        const tipoSelect = document.getElementById('tipo');
        const empleadoField = document.getElementById('empleado_field');
        
        function toggleEmpleadoField() {
            // Muestra el campo si el tipo seleccionado es CUMPLEANOS
            if (tipoSelect.value === 'CUMPLEANOS') {
                empleadoField.style.display = 'block';
            } else {
                empleadoField.style.display = 'none';
            }
        }

        toggleEmpleadoField();
        tipoSelect.addEventListener('change', toggleEmpleadoField);
    });
</script>
@endpush