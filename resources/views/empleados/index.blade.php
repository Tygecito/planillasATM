@extends('layouts.app')

@section('title', 'Gestión de Empleados y Usuarios')

@section('content')
    <h1 class="welcome-message">Gestión de Empleados y Usuarios</h1>

    <div class="card">
        <div class="search-bar">
            <form action="{{ route('empleados.index') }}" method="GET">
                <input type="text" name="search" placeholder="Buscar empleado..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                <a href="{{ route('empleados.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Empleado
                </a>
            </form>
        </div>

        <h2>Gestión de Empleados</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th> <th>Nombres</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>CI</th>
                        <th>NIT Dependiente</th> <th>CUA</th>             <th>Cargo</th>
                        <th>Estado</th>
                        <th>Username</th> 
                        <th>Rol</th>      
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empleados as $empleado)
                    <tr>
                        <td>{{ $loop->iteration }}</td> <td>{{ $empleado->nombres }}</td>
                        <td>{{ $empleado->primerapellido }}</td>
                        <td>{{ $empleado->segundoapellido }}</td>
                        
                        <td>{{ $empleado->documento_identidad }} {{ $empleado->complemento }}</td>
                        
                        <td>{{ $empleado->nit_dependiente }}</td> <td>{{ $empleado->cua }}</td>             <td>{{ $empleado->cargo_laboral }}</td>
                        <td>{{ $empleado->estado == 1 ? 'Activo' : 'Inactivo' }}</td>
                        <td>{{ $empleado->usuario ? $empleado->usuario->username : '' }}</td> 
                        <td>{{ $empleado->usuario ? ucfirst($empleado->usuario->role) : '' }}</td> 
                        
                        <td>
                            <div style="display: flex; gap: 5px; align-items: center;">
                                <a href="{{ route('empleados.edit', $empleado->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('empleados.destroy', $empleado->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" onclick="return confirm('¿Estás seguro de eliminar este empleado?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection