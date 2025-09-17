@extends('layouts.app')

@section('title', 'Crear Usuario - Mi App')

@section('content')
    <h1 class="welcome-message">Crear Nuevo Usuario</h1>

    <div class="card">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
            @if(Auth::user()->role === 'admin') <!-- Solo los administradores pueden seleccionar el rol -->
                <div class="form-group">
                    <label for="role">Rol</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
            @else
                <input type="hidden" name="role" value="user"> <!-- Asignar automáticamente el rol de usuario si no es admin -->
            @endif
            <div class="form-group">
                <label for="empleado_id">Empleado</label>
                <select name="empleado_id" id="empleado_id" class="form-control">
                    <option value="">Seleccione un empleado...</option>
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->id }}" {{ old('empleado_id') == $empleado->id ? 'selected' : '' }}>
                            {{ $empleado->nombres }} 
                            {{ $empleado->primerapellido ? $empleado->primerapellido . ' ' : '' }}
                            {{ $empleado->segundoapellido ? $empleado->segundoapellido . ' ' : '' }}
                            - {{ $empleado->cargo_laboral }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Usuario
                </button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection