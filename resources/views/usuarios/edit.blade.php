@extends('layouts.app')

@section('title', 'Editar Usuario - Mi App')

@section('content')
    <h1 class="welcome-message">Editar Usuario</h1>

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

        <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $usuario->username) }}" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Nueva contraseña">
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirmar contraseña">
            </div>

            <div class="form-group">
                <label for="role">Rol</label>
                <select name="role" id="role" class="form-control" required {{ Auth::user()->role !== 'admin' ? 'disabled' : '' }}>
                    <option value="admin" {{ $usuario->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $usuario->role == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>

            <div class="form-group">
                <label for="empleado_id">Empleado</label>
                <select name="empleado_id" id="empleado_id" class="form-control">
                    <option value="">Seleccione un empleado...</option>
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->id }}" {{ $usuario->empleado_id == $empleado->id ? 'selected' : '' }}>
                            {{ $empleado->nombres }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Usuario
                </button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection