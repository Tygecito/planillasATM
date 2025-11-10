@extends('layouts.app')

@section('title', 'Reporte de Subsidios - ' . $empleado->nombres)

@section('content')
    <h1 class="welcome-message">Gestión de Subsidios</h1>
    
    <div class="card">
        <div class="card-header">
            <h4>Reporte para: {{ $empleado->nombres }} {{ $empleado->primerapellido }}</h4>
            <p style="margin: 0;">Cargo: {{ $empleado->cargo_laboral }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="filter-section">
            <div>
                <a href="{{ route('nominas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Nóminas
                </a>
            </div>
            
            <a href="{{ route('subsidios.create', $empleado->id) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Registrar Nuevo Subsidio
            </a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo de Subsidio</th>
                        <th>Periodo</th>
                        <th>Monto (Bs)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($empleado->subsidios as $subsidio)
                    <tr>
                        <td>{{ $subsidio->id }}</td>
                        <td>{{ $subsidio->tipo_subsidio }}</td>
                        <td>{{ $subsidio->mes }} {{ $subsidio->anio }}</td>
                        <td>{{ number_format($subsidio->monto, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $subsidio->estado == 'PAGADO' ? 'success' : 'warning' }}">
                                {{ $subsidio->estado }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('subsidios.edit', $subsidio->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                            
                            <form action="{{ route('subsidios.destroy', $subsidio->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Estás seguro de eliminar este subsidio?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Este empleado no tiene subsidios registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Estilos para las insignias de estado --}}
    <style>
        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: .75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
    </style>
@endsection