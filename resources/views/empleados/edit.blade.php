@extends('layouts.app')

@section('title', 'Editar Empleado - Mi App')

@section('content')
    <h1 class="welcome-message">Editar Empleado</h1>

    <div class="card">
        <form id="edit-employee-form" action="{{ route('empleados.update', $empleado->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="empresa_id">Empresa</label>
                <select name="empresa_id" id="empresa_id" class="form-control" required>
                    <option value="">Seleccione una empresa...</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ $empleado->empresa_id == $empresa->id ? 'selected' : '' }}>
                            {{ $empresa->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" name="nombres" id="nombres" class="form-control" value="{{ $empleado->nombres }}" required>
            </div>
            <div class="form-group">
                <label for="primerapellido">Primer Apellido</label>
                <input type="text" name="primerapellido" id="primerapellido" class="form-control" value="{{ $empleado->primerapellido }}" required>
            </div>
            <div class="form-group">
                <label for="segundoapellido">Segundo Apellido</label>
                <input type="text" name="segundoapellido" id="segundoapellido" class="form-control" value="{{ $empleado->segundoapellido }}">
            </div>
            <div class="form-group">
                <label for="sucursal">Sucursal</label>
                <select name="sucursal" id="sucursal" class="form-control" required>
                    <option value="0" {{ $empleado->sucursal == 0 ? 'selected' : '' }}>Central</option>
                    <option value="1" {{ $empleado->sucursal == 1 ? 'selected' : '' }}>Sucursal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_ingreso">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" value="{{ $empleado->fecha_ingreso }}" required>
            </div>
            <div class="form-group">
                <label for="caja_de_salud">Caja de Salud</label>
                <select name="caja_de_salud" id="caja_de_salud" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Caja Nacional de Salud" {{ $empleado->caja_de_salud == 'Caja Nacional de Salud' ? 'selected' : '' }}>Caja Nacional de Salud</option>
                    <option value="Caja Bancaria Estatal de Salud" {{ $empleado->caja_de_salud == 'Caja Bancaria Estatal de Salud' ? 'selected' : '' }}>Caja Bancaria Estatal de Salud</option>
                    <option value="Caja de Salud de la Banca Privada" {{ $empleado->caja_de_salud == 'Caja de Salud de la Banca Privada' ? 'selected' : '' }}>Caja de Salud de la Banca Privada</option>
                    <option value="Caja Petrolera de Salud" {{ $empleado->caja_de_salud == 'Caja Petrolera de Salud' ? 'selected' : '' }}>Caja Petrolera de Salud</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tipo_de_contrato">Tipo de Contrato</label>
                <select name="tipo_de_contrato" id="tipo_de_contrato" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Contrato escrito" {{ $empleado->tipo_de_contrato == 'Contrato escrito' ? 'selected' : '' }}>Contrato escrito</option>
                    <option value="Contrato verbal" {{ $empleado->tipo_de_contrato == 'Contrato verbal' ? 'selected' : '' }}>Contrato verbal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="modalidad_contrato">Modalidad de Contrato</label>
                <select name="modalidad_contrato" id="modalidad_contrato" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Contrato por tiempo indefinido" {{ $empleado->modalidad_contrato == 'Contrato por tiempo indefinido' ? 'selected' : '' }}>Contrato por tiempo indefinido</option>
                    <option value="Contrato a plazo fijo" {{ $empleado->modalidad_contrato == 'Contrato a plazo fijo' ? 'selected' : '' }}>Contrato a plazo fijo</option>
                    <option value="Contrato por temporada" {{ $empleado->modalidad_contrato == 'Contrato por temporada' ? 'selected' : '' }}>Contrato por temporada</option>
                    <option value="Contrato por obra o servicio" {{ $empleado->modalidad_contrato == 'Contrato por obra o servicio' ? 'selected' : '' }}>Contrato por obra o servicio</option>
                    <option value="Contrato de teletrabajo" {{ $empleado->modalidad_contrato == 'Contrato de teletrabajo' ? 'selected' : '' }}>Contrato de teletrabajo</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cargo_laboral">Cargo Laboral</label>
                <input type="text" name="cargo_laboral" id="cargo_laboral" class="form-control" value="{{ $empleado->cargo_laboral }}">
            </div>
            <div class="form-group">
                <label for="fecha_de_nacimiento">Fecha de Nacimiento</label>
                <input type="date" name="fecha_de_nacimiento" id="fecha_de_nacimiento" class="form-control" value="{{ $empleado->fecha_de_nacimiento }}" required>
            </div>
            <div class="form-group">
                <label for="genero">Género</label>
                <select name="genero" id="genero" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="M" {{ $empleado->genero == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ $empleado->genero == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>
            <div class="form-group">
                <label for="estado_civil">Estado Civil</label>
                <select name="estado_civil" id="estado_civil" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Soltero" {{ $empleado->estado_civil == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                    <option value="Casado" {{ $empleado->estado_civil == 'Casado' ? 'selected' : '' }}>Casado</option>
                    <option value="Divorciado" {{ $empleado->estado_civil == 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
                    <option value="Viudo" {{ $empleado->estado_civil == 'Viudo' ? 'selected' : '' }}>Viudo</option>
                    <option value="Unión libre" {{ $empleado->estado_civil == 'Unión libre' ? 'selected' : '' }}>Unión libre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="documento_identidad">Documento de Identidad</label>
                <input type="text" name="documento_identidad" id="documento_identidad" class="form-control" value="{{ $empleado->documento_identidad }}" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="{{ $empleado->telefono }}">
            </div>
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control" value="{{ $empleado->direccion }}">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ $empleado->email }}">
            </div>
            <div class="form-group">
                <label for="foto">Foto</label>
                <input type="text" name="foto" id="foto" class="form-control" value="{{ $empleado->foto }}">
            </div>
            <div class="form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" class="form-control">
                    <option value="1" {{ $empleado->estado == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ $empleado->estado == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="confirmUpdate()">
                    <i class="fas fa-save"></i> Actualizar Empleado
                </button>
                <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        function confirmUpdate() {
            if (confirm('¿Estás seguro de actualizar el empleado?')) {
                document.getElementById('edit-employee-form').submit();
            }
        }
    </script>
@endsection