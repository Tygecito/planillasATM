@extends('layouts.app')

@section('title', 'Dashboard - Mi App')

@section('content')
    <h1 class="welcome-message">Bienvenido, {{ Auth::user()->username }}</h1>

    <div class="stats-cards">
        
        <div class="stat-card">
            <i class="fas fa-user-check" style="color: #942044;"></i>
            <h3>Empleados Activos</h3>
            <p>{{ number_format($totalEmpleadosActivos, 0, ',', '.') }}</p> 
        </div>

        <div class="stat-card">
            <i class="fas fa-suitcase-rolling" style="color: #007bff;"></i>
            <h3>De Vacaciones (Hoy)</h3>
            <p>{{ $empleadosEnVacaciones }}</p>
        </div>

        <div class="stat-card">
            <i class="fas fa-calendar-alt" style="color: green;"></i>
            <h3>Próximos Feriados</h3>
            @if ($proximosFeriados->isNotEmpty())
                @php $feriadoProximo = $proximosFeriados->first(); @endphp
                <p style="font-size: 1.5rem; color: #942044;">
                    {{ \Carbon\Carbon::parse($feriadoProximo['fecha'])->isoFormat('DD MMM') }}
                </p>
                <small>{{ $feriadoProximo['nombre'] }} ({{ $feriadoProximo['dia_semana'] }})</small>
            @else
                <p>No hay feriados cercanos.</p>
            @endif
        </div>

    </div>
    
    @if ($proximosFeriados->count() > 1)
        <div class="card mt-4">
            <h2>Detalles de los Feriados Siguientes</h2>
            <ul>
                @foreach ($proximosFeriados->skip(1) as $feriado)
                    <li>
                        **{{ $feriado['nombre'] }}** el {{ $feriado['dia_semana'] }} 
                        ({{ \Carbon\Carbon::parse($feriado['fecha'])->isoFormat('DD/MM/YYYY') }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    
    {{-- Esto es un comentario de Blade. El usuario no lo verá en el código fuente de la página. --}}


    {{-- BLOQUE COMENTADO: Notificaciones Recientes --}}
    {{-- 
    <div class="card">
        <h2><i class="fas fa-bell"></i> Notificaciones Recientes</h2>
        <p>Listado de notificaciones o actividades recientes.</p>
    </div>
    --}}
    
    {{-- BLOQUE COMENTADO: Tareas Pendientes --}}
    {{--
    <div class="card">
        <h2><i class="fas fa-tasks"></i> Tareas Pendientes</h2>
        <p>Listado de tareas o acciones requeridas.</p>
    </div>
    --}}
@endsection