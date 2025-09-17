@extends('layouts.app')

@section('title', 'Empleados - Mi App')

@section('content')
    <h1 class="welcome-message">Gestión de Empleados</h1>
    
    <div class="card">
        <div class="search-bar">
            <form action="{{ route('empleados.index') }}" method="GET">
                <input type="text" name="search" placeholder="Buscar empleado..." value="{{ request('search') }}">
                <select name="empresa_id" id="empresa_id" class="form-control">
                    <option value="">Seleccione una empresa...</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ request('empresa_id') == $empresa->id ? 'selected' : '' }}>
                            {{ $empresa->nombre }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                <a href="{{ route('empleados.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Empleado
                </a>
            </form>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>DNI</th>
                        <th>Cargo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empleados as $empleado)
                    <tr>
                        <td>{{ $empleado->nombres }}</td>
                        <td>{{ $empleado->primerapellido }}</td>
                        <td>{{ $empleado->segundoapellido }}</td>
                        <td>{{ $empleado->documento_identidad }}</td>
                        <td>{{ $empleado->cargo_laboral }}</td>
                        <td>{{ $empleado->estado == 1 ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <a href="{{ route('empleados.edit', $empleado->id) }}" class="btn btn-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('empleados.destroy', $empleado->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary" onclick="return confirm('¿Estás seguro de eliminar este empleado?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection