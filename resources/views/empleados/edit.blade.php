@extends('layouts.app')

@section('title', 'Editar Empleado y Usuario - Mi App')

@section('content')
<h1 class="welcome-message">Editar Empleado y Usuario</h1>

<div class="card">
    <form id="edit-employee-user-form" action="{{ route('empleados.update', $empleado->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <p><strong>Por favor, corrija los siguientes errores:</strong></p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2>Datos del Empleado</h2>
        
        <div class="form-grid-empleado">
            
            <div class="form-group">
                <label for="nombres">Nombres *</label>
                <input type="text" name="nombres" id="nombres" class="form-control @error('nombres') is-invalid @enderror" value="{{ old('nombres', $empleado->nombres) }}" required>
                @error('nombres')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="primerapellido">Primer Apellido *</label>
                <input type="text" name="primerapellido" id="primerapellido" class="form-control @error('primerapellido') is-invalid @enderror" value="{{ old('primerapellido', $empleado->primerapellido) }}" required>
                @error('primerapellido')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="segundoapellido">Segundo Apellido</label>
                <input type="text" name="segundoapellido" id="segundoapellido" class="form-control @error('segundoapellido') is-invalid @enderror" value="{{ old('segundoapellido', $empleado->segundoapellido) }}">
                @error('segundoapellido')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="sucursal">Sucursal *</label>
                <select name="sucursal" id="sucursal" class="form-control @error('sucursal') is-invalid @enderror" required>
                    <option value="0" {{ old('sucursal', $empleado->sucursal) == 0 ? 'selected' : '' }}>Central</option>
                    <option value="1" {{ old('sucursal', $empleado->sucursal) == 1 ? 'selected' : '' }}>Sucursal</option>
                </select>
                @error('sucursal')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="fecha_ingreso">Fecha de Ingreso *</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control @error('fecha_ingreso') is-invalid @enderror" value="{{ old('fecha_ingreso', $empleado->fecha_ingreso) }}" required>
                @error('fecha_ingreso')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="caja_de_salud">Caja de Salud</label>
                <select name="caja_de_salud" id="caja_de_salud" class="form-control @error('caja_de_salud') is-invalid @enderror">
                    <option value="">Seleccione...</option>
                    <option value="Caja Nacional de Salud" {{ old('caja_de_salud', $empleado->caja_de_salud) == 'Caja Nacional de Salud' ? 'selected' : '' }}>Caja Nacional de Salud</option>
                    <option value="Caja Bancaria Estatal de Salud" {{ old('caja_de_salud', $empleado->caja_de_salud) == 'Caja Bancaria Estatal de Salud' ? 'selected' : '' }}>Caja Bancaria Estatal de Salud</option>
                    <option value="Caja de Salud de la Banca Privada" {{ old('caja_de_salud', $empleado->caja_de_salud) == 'Caja de Salud de la Banca Privada' ? 'selected' : '' }}>Caja de Salud de la Banca Privada</option>
                    <option value="Caja Petrolera de Salud" {{ old('caja_de_salud', $empleado->caja_de_salud) == 'Caja Petrolera de Salud' ? 'selected' : '' }}>Caja Petrolera de Salud</option>
                </select>
                @error('caja_de_salud')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="tipo_de_contrato">Tipo de Contrato</label>
                <select name="tipo_de_contrato" id="tipo_de_contrato" class="form-control @error('tipo_de_contrato') is-invalid @enderror">
                    <option value="">Seleccione...</option>
                    <option value="Contrato escrito" {{ old('tipo_de_contrato', $empleado->tipo_de_contrato) == 'Contrato escrito' ? 'selected' : '' }}>Contrato escrito</option>
                    <option value="Contrato verbal" {{ old('tipo_de_contrato', $empleado->tipo_de_contrato) == 'Contrato verbal' ? 'selected' : '' }}>Contrato verbal</option>
                </select>
                @error('tipo_de_contrato')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="modalidad_contrato">Modalidad de Contrato</label>
                <select name="modalidad_contrato" id="modalidad_contrato" class="form-control @error('modalidad_contrato') is-invalid @enderror">
                    <option value="">Seleccione...</option>
                    <option value="Contrato por tiempo indefinido" {{ old('modalidad_contrato', $empleado->modalidad_contrato) == 'Contrato por tiempo indefinido' ? 'selected' : '' }}>Contrato por tiempo indefinido</option>
                    <option value="Contrato a plazo fijo" {{ old('modalidad_contrato', $empleado->modalidad_contrato) == 'Contrato a plazo fijo' ? 'selected' : '' }}>Contrato a plazo fijo</option>
                    <option value="Contrato por temporada" {{ old('modalidad_contrato', $empleado->modalidad_contrato) == 'Contrato por temporada' ? 'selected' : '' }}>Contrato por temporada</option>
                    <option value="Contrato por obra o servicio" {{ old('modalidad_contrato', $empleado->modalidad_contrato) == 'Contrato por obra o servicio' ? 'selected' : '' }}>Contrato por obra o servicio</option>
                    <option value="Contrato de teletrabajo" {{ old('modalidad_contrato', $empleado->modalidad_contrato) == 'Contrato de teletrabajo' ? 'selected' : '' }}>Contrato de teletrabajo</option>
                </select>
                @error('modalidad_contrato')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="cargo_laboral">Cargo Laboral *</label>
                <select name="cargo_laboral" id="cargo_laboral" class="form-control @error('cargo_laboral') is-invalid @enderror" required>
                    <option value="">Seleccione...</option>
                    {{-- Leemos la constante CARGOS_LABORALES del Modelo --}}
                    @foreach(\App\Models\Empleado::CARGOS_LABORALES as $cargo)
                        <option value="{{ $cargo }}" {{ old('cargo_laboral', $empleado->cargo_laboral) == $cargo ? 'selected' : '' }}>{{ $cargo }}</option>
                    @endforeach
                </select>
                @error('cargo_laboral')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label for="fecha_de_nacimiento">Fecha de Nacimiento *</label>
                <input type="date" name="fecha_de_nacimiento" id="fecha_de_nacimiento" class="form-control @error('fecha_de_nacimiento') is-invalid @enderror" value="{{ old('fecha_de_nacimiento', $empleado->fecha_de_nacimiento) }}" required>
                @error('fecha_de_nacimiento')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="genero">Género *</label>
                <select name="genero" id="genero" class="form-control @error('genero') is-invalid @enderror" required>
                    <option value="">Seleccione...</option>
                    <option value="M" {{ old('genero', $empleado->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero', $empleado->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
                @error('genero')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="estado_civil">Estado Civil</label>
                <select name="estado_civil" id="estado_civil" class="form-control @error('estado_civil') is-invalid @enderror">
                    <option value="">Seleccione...</option>
                    <option value="Soltero" {{ old('estado_civil', $empleado->estado_civil) == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                    <option value="Casado" {{ old('estado_civil', $empleado->estado_civil) == 'Casado' ? 'selected' : '' }}>Casado</option>
                    <option value="Divorciado" {{ old('estado_civil', $empleado->estado_civil) == 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
                    <option value="Viudo" {{ old('estado_civil', $empleado->estado_civil) == 'Viudo' ? 'selected' : '' }}>Viudo</option>
                    <option value="Unión libre" {{ old('estado_civil', $empleado->estado_civil) == 'Unión libre' ? 'selected' : '' }}>Unión libre</option>
                </select>
                @error('estado_civil')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="documento_identidad">Documento de Identidad *</label>
                <input type="text" name="documento_identidad" id="documento_identidad" class="form-control @error('documento_identidad') is-invalid @enderror" value="{{ old('documento_identidad', $empleado->documento_identidad) }}" required inputmode="numeric">
                @error('documento_identidad')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="complemento">Complemento</label>
                <input type="text" name="complemento" id="complemento" class="form-control @error('complemento') is-invalid @enderror" value="{{ old('complemento', $empleado->complemento) }}" maxlength="2" placeholder="Ej: 1A, E5">
                @error('complemento')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="nit_dependiente">NIT Dependiente</label>
                <input type="text" name="nit_dependiente" id="nit_dependiente" class="form-control @error('nit_dependiente') is-invalid @enderror" value="{{ old('nit_dependiente', $empleado->nit_dependiente) }}" placeholder="Solo números" inputmode="numeric">
                @error('nit_dependiente')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $empleado->telefono) }}" inputmode="numeric">
                @error('telefono')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $empleado->direccion) }}">
                @error('direccion')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $empleado->email) }}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="cua">CUA (Código Único de Aportante)</label>
                <input type="number" name="cua" id="cua" class="form-control @error('cua') is-invalid @enderror" value="{{ old('cua', $empleado->cua) }}" placeholder="Solo números (8-10 dígitos)" min="10000000" max="9999999999">
                @error('cua')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="estado">Estado *</label>
                <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                    <option value="1" {{ old('estado', $empleado->estado) == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $empleado->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('estado')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div> {{-- Esta lógica de @if/$usuario ya es avanzada y está correcta. La mantenemos intacta. --}}
        @if($usuario)
            
            @if(Auth::user()->role === 'user' && Auth::user()->id == $usuario->id)
                <h2>Mis Datos de Usuario</h2>
                <div class="form-grid-usuario">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $usuario->username) }}" readonly style="background-color: #e9ecef;">
                        <small class="text-muted">No puedes cambiar tu username</small>
                        @error('username')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña (dejar en blanco para no cambiar)</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror">
                        @error('password_confirmation')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="role">Rol</label>
                        <input type="text" name="role_display" id="role_display" class="form-control" value="{{ $usuario->role == 'admin' ? 'Administrador' : 'Usuario' }}" readonly style="background-color: #e9ecef;">
                        <input type="hidden" name="role" value="{{ $usuario->role }}">
                        <small class="text-muted">No puedes cambiar tu rol</small>
                    </div>
                </div>

            @elseif(Auth::user()->role === 'admin')
                <h2>Datos del Usuario</h2>
                <div class="form-grid-usuario">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $usuario->username) }}" required>
                        @error('username')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña (dejar en blanco para no cambiar)</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror">
                        @error('password_confirmation')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="role">Rol</label>
                        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="admin" {{ old('role', $usuario->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ old('role', $usuario->role) == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                        @error('role')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

            @else
                <input type="hidden" name="username" value="{{ $usuario->username }}">
                <input type="hidden" name="role" value="{{ $usuario->role }}">
            @endif
        @endif

        <div class="form-actions">
            <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
            <button type="button" class="btn btn-primary" onclick="showCustomConfirm()">
                <i class="fas fa-save"></i> Actualizar
            </button>
        </div>
    </form>
</div>

<div id="custom-confirm-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 400px; border-radius: 8px;">
        <p><strong>¿Estás seguro de actualizar el empleado y el usuario?</strong></p>
        <button onclick="submitForm()" style="background-color: #942044; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">Sí, Actualizar</button>
        <button onclick="hideCustomConfirm()" style="background-color: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
    </div>
</div>

{{-- CSS INLINE para garantizar que los errores y el modal se posicionen correctamente --}}
{{-- (Añadido desde create.blade.php para consistencia) --}}
<style>
.alert-danger { background-color: #f8d7da; color: #721c24; padding: 1rem; margin-bottom: 1.5rem; border: 1px solid #f5c6cb; border-radius: 4px; }
.alert-danger ul { list-style: none; margin: 0; padding: 0; }
.text-danger { color: #dc3545 !important; font-size: 0.85rem; margin-top: 5px; display: block; }
#custom-confirm-modal > div { text-align: center; }
.form-grid-empleado, .form-grid-usuario { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
.form-actions { margin-top: 2rem; border-top: 1px solid #ddd; padding-top: 1.5rem; display: flex; justify-content: flex-end; gap: 10px; }
@media (max-width: 768px) { .form-grid-empleado, .form-grid-usuario { grid-template-columns: 1fr; } }
.form-group.full-width { grid-column: 1 / -1; }
</style>

<script>
    function showCustomConfirm() {
        document.getElementById('custom-confirm-modal').style.display = 'block';
    }

    function hideCustomConfirm() {
        document.getElementById('custom-confirm-modal').style.display = 'none';
    }
    
    function submitForm() {
        document.getElementById('custom-confirm-modal').style.display = 'none';
        document.getElementById('edit-employee-user-form').submit();
    }
</script>

@endsection