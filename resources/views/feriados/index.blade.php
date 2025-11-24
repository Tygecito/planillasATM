@extends('layouts.app')

@section('title', 'Administración de Feriados')

@section('content')

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2>Gestión de Días No Hábiles (Feriados y Colectivas)</h2>
            <a href="{{ route('feriados.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Crear Nuevo Feriado
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
                        <th>Fecha</th>
                        <th>Día</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Empleado Asignado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($feriados as $feriado)
                        <tr>
                            {{-- Muestra la fecha en formato DD/MM/YYYY y garantiza el orden cronológico del Controller --}}
                            <td>{{ \Carbon\Carbon::parse($feriado->fecha)->format('d/m/Y') }}</td>
                            {{-- Muestra el nombre del día de la semana --}}
                            <td>{{ \Carbon\Carbon::parse($feriado->fecha)->isoFormat('dddd') }}</td> 
                            
                            <td>{{ $feriado->descripcion }}</td>
                            <td>
                                <span class="vacation-status pending" style="background-color: #e3f2fd; color: #1e88e5;">
                                    {{ $feriado->tipo }}
                                </span>
                            </td>
                            <td>
                                @if ($feriado->empleado_id)
                                    {{-- El método empleado() debe estar definido en el modelo Feriado --}}
                                    {{ $feriado->empleado->nombres }} {{ $feriado->empleado->primerapellido }}
                                @else
                                    N/A (General)
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('feriados.edit', $feriado) }}" class="btn btn-secondary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('feriados.destroy', $feriado) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este feriado?');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">No hay feriados registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection