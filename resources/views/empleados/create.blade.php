@extends('layouts.app')

@section('title', 'Crear Empleado - Mi App')

@section('content')
    <h1 class="welcome-message">Crear Nuevo Empleado</h1>

    <div class="card">
        <form id="create-employee-form" action="{{ route('empleados.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="empresa_id">Empresa</label>
                <select name="empresa_id" id="empresa_id" class="form-control" required>
                    <option value="">Seleccione una empresa...</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" name="nombres" id="nombres" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="primerapellido">Primer Apellido</label>
                <input type="text" name="primerapellido" id="primerapellido" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="segundoapellido">Segundo Apellido</label>
                <input type="text" name="segundoapellido" id="segundoapellido" class="form-control">
            </div>
            <div class="form-group">
                <label for="sucursal">Sucursal</label>
                <select name="sucursal" id="sucursal" class="form-control" required>
                    <option value="0">Central</option>
                    <option value="1">Sucursal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_ingreso">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="caja_de_salud">Caja de Salud</label>
                <select name="caja_de_salud" id="caja_de_salud" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Caja Nacional de Salud">Caja Nacional de Salud</option>
                    <option value="Caja Bancaria Estatal de Salud">Caja Bancaria Estatal de Salud</option>
                    <option value="Caja de Salud de la Banca Privada">Caja de Salud de la Banca Privada</option>
                    <option value="Caja Petrolera de Salud">Caja Petrolera de Salud</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tipo_de_contrato">Tipo de Contrato</label>
                <select name="tipo_de_contrato" id="tipo_de_contrato" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Contrato escrito">Contrato escrito</option>
                    <option value="Contrato verbal">Contrato verbal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="modalidad_contrato">Modalidad de Contrato</label>
                <select name="modalidad_contrato" id="modalidad_contrato" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Contrato por tiempo indefinido">Contrato por tiempo indefinido</option>
                    <option value="Contrato a plazo fijo">Contrato a plazo fijo</option>
                    <option value="Contrato por temporada">Contrato por temporada</option>
                    <option value="Contrato por obra o servicio">Contrato por obra o servicio</option>
                    <option value="Contrato de teletrabajo">Contrato de teletrabajo</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cargo_laboral">Cargo Laboral</label>
                <input type="text" name="cargo_laboral" id="cargo_laboral" class="form-control">
            </div>
            <div class="form-group">
                <label for="fecha_de_nacimiento">Fecha de Nacimiento</label>
                <input type="date" name="fecha_de_nacimiento" id="fecha_de_nacimiento" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="genero">Género</label>
                <select name="genero" id="genero" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
            <div class="form-group">
                <label for="estado_civil">Estado Civil</label>
                <select name="estado_civil" id="estado_civil" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Soltero">Soltero</option>
                    <option value="Casado">Casado</option>
                    <option value="Divorciado">Divorciado</option>
                    <option value="Viudo">Viudo</option>
                    <option value="Unión libre">Unión libre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="documento_identidad">Documento de Identidad</label>
                <input type="text" name="documento_identidad" id="documento_identidad" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control">
            </div>
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>
            <div class="form-group">
                <label for="foto">Foto</label>
                <input type="text" name="foto" id="foto" class="form-control">
            </div>
            <div class="form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" class="form-control">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="confirmSave()">
                    <i class="fas fa-save"></i> Guardar Empleado
                </button>
                <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        function confirmSave() {
            if (confirm('¿Estás seguro de guardar el empleado?')) {
                document.getElementById('create-employee-form').submit();
            }
        }
    </script>
@endsection