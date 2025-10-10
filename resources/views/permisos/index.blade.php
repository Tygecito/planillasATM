@extends('layouts.app')

@section('title', 'Gestión de Permisos')

@section('content')

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2>Lista de Solicitudes de Permiso</h2>
            <a href="{{ route('permisos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Crear Nuevo Permiso
            </a>
        </div>

        @if (session('success'))
            <div class="alert approved" style="margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Empleado</th>
                        <th>Tipo</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Días Solicitados</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permisos as $permiso)
                        <tr>
                            <td>{{ $permiso->id }}</td>
                            <td>{{ $permiso->empleado->nombres }} {{ $permiso->empleado->primerapellido }}</td>
                            <td>{{ $permiso->tipo_permiso }}</td>
                            <td>{{ \Carbon\Carbon::parse($permiso->fecha_inicio)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($permiso->fecha_fin)->format('d/m/Y') }}</td>
                            <td>{{ number_format($permiso->dias_solicitados, 3) }}</td>
                            <td>
                                @php
                                    $claseEstado = match($permiso->estado) {
                                        'APROBADO' => 'approved',
                                        'PENDIENTE' => 'pending',
                                        'RECHAZADO', 'CANCELADO' => 'rejected',
                                        default => 'pending',
                                    };
                                @endphp
                                <span class="vacation-status {{ $claseEstado }}">
                                    {{ $permiso->estado }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('permisos.show', $permiso) }}" class="btn btn-secondary" title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('permisos.edit', $permiso) }}" class="btn btn-secondary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('permisos.destroy', $permiso) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este permiso?');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center;">No se encontraron solicitudes de permiso.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection