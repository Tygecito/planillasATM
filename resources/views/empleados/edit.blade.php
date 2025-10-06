@extends('layouts.app')

@section('title', 'Editar Empleado y Usuario - Mi App')

@section('content')
    <h1 class="welcome-message">Editar Empleado y Usuario</h1>

    <div class="card">
        <form id="edit-employee-user-form" action="{{ route('empleados.update', $empleado->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Formulario de Empleado (visible para ambos roles) -->
            <h2>Datos del Empleado</h2>
            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" name="nombres" id="nombres" class="form-control" value="{{ old('nombres', $empleado->nombres) }}" required>
            </div>
            <div class="form-group">
                <label for="primerapellido">Primer Apellido</label>
                <input type="text" name="primerapellido" id="primerapellido" class="form-control" value="{{ old('primerapellido', $empleado->primerapellido) }}" required>
            </div>
            <div class="form-group">
                <label for="segundoapellido">Segundo Apellido</label>
                <input type="text" name="segundoapellido" id="segundoapellido" class="form-control" value="{{ old('segundoapellido', $empleado->segundoapellido) }}">
            </div>
            <div class="form-group">
                <label for="sucursal">Sucursal</label>
                <select name="sucursal" id="sucursal" class="form-control" required>
                    <option value="0" {{ old('sucursal', $empleado->sucursal) == 0 ? 'selected' : '' }}>Central</option>
                    <option value="1" {{ old('sucursal', $empleado->sucursal) == 1 ? 'selected' : '' }}>Sucursal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_ingreso">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" value="{{ old('fecha_ingreso', $empleado->fecha_ingreso) }}" required>
            </div>
            <div class="form-group">
                <label for="caja_de_salud">Caja de Salud</label>
                <select name="caja_de_salud" id="caja_de_salud" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Caja Nacional de Salud" {{ old('caja_de_salud', $empleado->caja_de_salud) == 'Caja Nacional de Salud' ? 'selected' : '' }}>Caja Nacional de Salud</option>
                    <option value="Caja Bancaria Estatal de Salud" {{ old('caja_de_salud', $empleado->caja_de_salud) == 'Caja Bancaria Estatal de Salud' ? 'selected' : '' }}>Caja Bancaria Estatal de Salud</option>
                    <option value="Caja de Salud de la Banca Privada" {{ old('caja_de_salud', $empleado->caja_de_salud) == 'Caja de Salud de la Banca Privada' ? 'selected' : '' }}>Caja de Salud de la Banca Privada</option>
                    <option value="Caja Petrolera de Salud" {{ old('caja_de_salud', $empleado->caja_de_salud) == 'Caja Petrolera de Salud' ? 'selected' : '' }}>Caja Petrolera de Salud</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tipo_de_contrato">Tipo de Contrato</label>
                <select name="tipo_de_contrato" id="tipo_de_contrato" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Contrato escrito" {{ old('tipo_de_contrato', $empleado->tipo_de_contrato) == 'Contrato escrito' ? 'selected' : '' }}>Contrato escrito</option>
                    <option value="Contrato verbal" {{ old('tipo_de_contrato', $empleado->tipo_de_contrato) == 'Contrato verbal' ? 'selected' : '' }}>Contrato verbal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="modalidad_contrato">Modalidad de Contrato</label>
                <select name="modalidad_contrato" id="modalidad_contrato" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Contrato por tiempo indefinido" {{ old('modalidad_contrato', $empleado->modalidad_contrato) == 'Contrato por tiempo indefinido' ? 'selected' : '' }}>Contrato por tiempo indefinido</option>
                    <option value="Contrato a plazo fijo" {{ old('modalidad_contrato', $empleado->modalidad_contrato) == 'Contrato a plazo fijo' ? 'selected' : '' }}>Contrato a plazo fijo</option>
                    <option value="Contrato por temporada" {{ old('modalidad_contrato', $empleado->modalidad_contrato) == 'Contrato por temporada' ? 'selected' : '' }}>Contrato por temporada</option>
                    <option value="Contrato por obra o servicio" {{ old('modalidad_contrato', $empleado->modalidad_contrato) == 'Contrato por obra o servicio' ? 'selected' : '' }}>Contrato por obra o servicio</option>
                    <option value="Contrato de teletrabajo" {{ old('modalidad_contrato', $empleado->modalidad_contrato) == 'Contrato de teletrabajo' ? 'selected' : '' }}>Contrato de teletrabajo</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cargo_laboral">Cargo Laboral</label>
                <input type="text" name="cargo_laboral" id="cargo_laboral" class="form-control" value="{{ old('cargo_laboral', $empleado->cargo_laboral) }}">
            </div>
            <div class="form-group">
                <label for="fecha_de_nacimiento">Fecha de Nacimiento</label>
                <input type="date" name="fecha_de_nacimiento" id="fecha_de_nacimiento" class="form-control" value="{{ old('fecha_de_nacimiento', $empleado->fecha_de_nacimiento) }}" required>
            </div>
            <div class="form-group">
                <label for="genero">Género</label>
                <select name="genero" id="genero" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="M" {{ old('genero', $empleado->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero', $empleado->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>
            <div class="form-group">
                <label for="estado_civil">Estado Civil</label>
                <select name="estado_civil" id="estado_civil" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Soltero" {{ old('estado_civil', $empleado->estado_civil) == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                    <option value="Casado" {{ old('estado_civil', $empleado->estado_civil) == 'Casado' ? 'selected' : '' }}>Casado</option>
                    <option value="Divorciado" {{ old('estado_civil', $empleado->estado_civil) == 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
                    <option value="Viudo" {{ old('estado_civil', $empleado->estado_civil) == 'Viudo' ? 'selected' : '' }}>Viudo</option>
                    <option value="Unión libre" {{ old('estado_civil', $empleado->estado_civil) == 'Unión libre' ? 'selected' : '' }}>Unión libre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="documento_identidad">Documento de Identidad</label>
                <input type="text" name="documento_identidad" id="documento_identidad" class="form-control" value="{{ old('documento_identidad', $empleado->documento_identidad) }}" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $empleado->telefono) }}">
            </div>
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion', $empleado->direccion) }}">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $empleado->email) }}">
            </div>
            <div class="form-group">
                <label for="foto">Foto</label>
                <input type="text" name="foto" id="foto" class="form-control" value="{{ old('foto', $empleado->foto) }}">
            </div>
            <div class="form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" class="form-control">
                    <option value="1" {{ old('estado', $empleado->estado) == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $empleado->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <!-- Formulario de Usuario - Solo para admin -->
            @if(Auth::user()->role === 'admin' && $usuario)
                <h2>Datos del Usuario</h2>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $usuario->username) }}" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>
                <div class="form-group">
                    <label for="role">Rol</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="admin" {{ old('role', $usuario->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ old('role', $usuario->role) == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>
            @else
                <!-- Campos ocultos para mantener los valores actuales del usuario cuando no es admin -->
                @if($usuario)
                    <input type="hidden" name="username" value="{{ $usuario->username }}">
                    <input type="hidden" name="role" value="{{ $usuario->role }}">
                @endif
            @endif

            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="confirmUpdate()">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        function confirmUpdate() {
            if (confirm('¿Estás seguro de actualizar el empleado y el usuario?')) {
                document.getElementById('edit-employee-user-form').submit();
            }
        }
    </script>
@endsection