@extends('layouts.app')

@section('title', 'Crear Empleado - Mi App')

@section('content')
    <h1 class="welcome-message">Crear Nuevo Empleado</h1>

    <div class="card">
        <form id="create-employee-form" action="{{ route('empleados.store') }}" method="POST">
            @csrf
            
            <!-- Formulario de Empleado (visible para todos) -->
            <h2>Datos del Empleado</h2>
            <div class="form-group">
                <label for="nombres">Nombres *</label>
                <input type="text" name="nombres" id="nombres" class="form-control" value="{{ old('nombres') }}" required>
                @error('nombres')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="primerapellido">Primer Apellido *</label>
                <input type="text" name="primerapellido" id="primerapellido" class="form-control" value="{{ old('primerapellido') }}" required>
                @error('primerapellido')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="segundoapellido">Segundo Apellido</label>
                <input type="text" name="segundoapellido" id="segundoapellido" class="form-control" value="{{ old('segundoapellido') }}">
                @error('segundoapellido')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="sucursal">Sucursal *</label>
                <select name="sucursal" id="sucursal" class="form-control" required>
                    <option value="">Seleccionar...</option>
                    <option value="0" {{ old('sucursal') == '0' ? 'selected' : '' }}>Central</option>
                    <option value="1" {{ old('sucursal') == '1' ? 'selected' : '' }}>Sucursal</option>
                </select>
                @error('sucursal')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="fecha_ingreso">Fecha de Ingreso *</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" value="{{ old('fecha_ingreso') }}" required>
                @error('fecha_ingreso')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="caja_de_salud">Caja de Salud</label>
                <select name="caja_de_salud" id="caja_de_salud" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Caja Nacional de Salud" {{ old('caja_de_salud') == 'Caja Nacional de Salud' ? 'selected' : '' }}>Caja Nacional de Salud</option>
                    <option value="Caja Bancaria Estatal de Salud" {{ old('caja_de_salud') == 'Caja Bancaria Estatal de Salud' ? 'selected' : '' }}>Caja Bancaria Estatal de Salud</option>
                    <option value="Caja de Salud de la Banca Privada" {{ old('caja_de_salud') == 'Caja de Salud de la Banca Privada' ? 'selected' : '' }}>Caja de Salud de la Banca Privada</option>
                    <option value="Caja Petrolera de Salud" {{ old('caja_de_salud') == 'Caja Petrolera de Salud' ? 'selected' : '' }}>Caja Petrolera de Salud</option>
                </select>
                @error('caja_de_salud')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="tipo_de_contrato">Tipo de Contrato</label>
                <select name="tipo_de_contrato" id="tipo_de_contrato" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Contrato escrito" {{ old('tipo_de_contrato') == 'Contrato escrito' ? 'selected' : '' }}>Contrato escrito</option>
                    <option value="Contrato verbal" {{ old('tipo_de_contrato') == 'Contrato verbal' ? 'selected' : '' }}>Contrato verbal</option>
                </select>
                @error('tipo_de_contrato')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="modalidad_contrato">Modalidad de Contrato</label>
                <select name="modalidad_contrato" id="modalidad_contrato" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Contrato por tiempo indefinido" {{ old('modalidad_contrato') == 'Contrato por tiempo indefinido' ? 'selected' : '' }}>Contrato por tiempo indefinido</option>
                    <option value="Contrato a plazo fijo" {{ old('modalidad_contrato') == 'Contrato a plazo fijo' ? 'selected' : '' }}>Contrato a plazo fijo</option>
                    <option value="Contrato por temporada" {{ old('modalidad_contrato') == 'Contrato por temporada' ? 'selected' : '' }}>Contrato por temporada</option>
                    <option value="Contrato por obra o servicio" {{ old('modalidad_contrato') == 'Contrato por obra o servicio' ? 'selected' : '' }}>Contrato por obra o servicio</option>
                    <option value="Contrato de teletrabajo" {{ old('modalidad_contrato') == 'Contrato de teletrabajo' ? 'selected' : '' }}>Contrato de teletrabajo</option>
                </select>
                @error('modalidad_contrato')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="cargo_laboral">Cargo Laboral *</label>
                <input type="text" name="cargo_laboral" id="cargo_laboral" class="form-control" value="{{ old('cargo_laboral') }}" required>
                @error('cargo_laboral')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="fecha_de_nacimiento">Fecha de Nacimiento *</label>
                <input type="date" name="fecha_de_nacimiento" id="fecha_de_nacimiento" class="form-control" value="{{ old('fecha_de_nacimiento') }}" required>
                @error('fecha_de_nacimiento')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="genero">Género *</label>
                <select name="genero" id="genero" class="form-control" required>
                    <option value="">Seleccione...</option>
                    <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
                @error('genero')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="estado_civil">Estado Civil</label>
                <select name="estado_civil" id="estado_civil" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="Soltero" {{ old('estado_civil') == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                    <option value="Casado" {{ old('estado_civil') == 'Casado' ? 'selected' : '' }}>Casado</option>
                    <option value="Divorciado" {{ old('estado_civil') == 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
                    <option value="Viudo" {{ old('estado_civil') == 'Viudo' ? 'selected' : '' }}>Viudo</option>
                    <option value="Unión libre" {{ old('estado_civil') == 'Unión libre' ? 'selected' : '' }}>Unión libre</option>
                </select>
                @error('estado_civil')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="documento_identidad">Documento de Identidad *</label>
                <input type="text" name="documento_identidad" id="documento_identidad" class="form-control" value="{{ old('documento_identidad') }}" required>
                @error('documento_identidad')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono') }}">
                @error('telefono')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion') }}">
                @error('direccion')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="foto">Foto</label>
                <input type="text" name="foto" id="foto" class="form-control" value="{{ old('foto') }}" placeholder="URL de la foto">
                @error('foto')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="estado">Estado *</label>
                <select name="estado" id="estado" class="form-control" required>
                    <option value="1" {{ old('estado', 1) == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado') == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('estado')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Formulario de Usuario - Solo para admin -->
            @if(Auth::user()->role === 'admin')
                <h2>Datos del Usuario (Opcional)</h2>
                <p class="text-muted">Complete solo si desea dar acceso al sistema a este empleado</p>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}">
                    @error('username')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control">
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>
                <div class="form-group">
                    <label for="role">Rol</label>
                    <select name="role" id="role" class="form-control">
                        <option value="">Seleccionar rol (opcional)</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                    @error('role')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="confirmCreate()">
                    <i class="fas fa-save"></i> Crear Empleado
                </button>
                <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        function confirmCreate() {
            if (confirm('¿Estás seguro de crear el nuevo empleado?')) {
                document.getElementById('create-employee-form').submit();
            }
        }
    </script>
@endsection