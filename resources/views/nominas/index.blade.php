@extends('layouts.app')

@section('title', 'Nóminas - Mi App')

@section('content')
    <h1 class="welcome-message">Gestión de Nóminas</h1>
    
            @if(isset($nominaDetalle))
<div class="detalle-nomina-container">
    <div class="detalle-header">
        <h4>🧾 Detalle de Nómina #{{ $nominaDetalle->id }}</h4>
        <div class="detalle-actions">
            <a href="{{ route('nominas.edit', $nominaDetalle->id) }}" class="btn-accion editar"><i class="fas fa-edit"></i> Modificar</a>
            <a href="{{ route('nominas.download', $nominaDetalle->id) }}" class="btn-accion descargar"><i class="fas fa-download"></i> Descargar</a>
            <button onclick="hideDetails()" class="btn-accion cerrar"><i class="fas fa-times"></i> Cerrar</button>
        </div>
    </div>

    <div class="detalle-body">
        <section class="detalle-section">
            <h5>🧍 Datos del Empleado</h5>
            <div class="detalle-grid">
                <div><strong>Nombre:</strong><br>{{ $nominaDetalle->empleado->nombres }} {{ $nominaDetalle->empleado->primerapellido }}</div>
                <div><strong>Documento:</strong><br>{{ $nominaDetalle->empleado->documento_identidad }}</div>
                <div><strong>Cargo:</strong><br>{{ $nominaDetalle->empleado->cargo_laboral }}</div>
            </div>
        </section>

        <section class="detalle-section">
            <h5>📅 Periodo</h5>
            <div class="detalle-grid">
                <div><strong>Mes:</strong><br>{{ $nominaDetalle->mes }}</div>
                <div><strong>Año:</strong><br>{{ $nominaDetalle->anio }}</div>
                <div><strong>Días Pagados:</strong><br>{{ $nominaDetalle->dias_pagados }}</div>
                <div><strong>Horas Pagadas:</strong><br>{{ $nominaDetalle->horas_pagadas }}</div>
                <div><strong>SMN:</strong><br>{{ number_format($nominaDetalle->smn, 2) }}</div>
            </div>
        </section>

        {{-- --- NUEVO --- Sección de Subsidios en el Detalle --}}
        <section class="detalle-section">
            <h5>🎁 Subsidios (Aprobados en este periodo)</h5>
            @php
                // --- CORREGIDO --- Comentario cambiado a formato PHP
                $mesNumDetalle = \Carbon\Carbon::createFromLocaleFormat('F', 'es_ES', $nominaDetalle->mes)->month;
                
                $subsidiosDetalle = $nominaDetalle->empleado->subsidios->filter(function($s) use ($mesNumDetalle, $nominaDetalle) {
                    $fechaSub = \Carbon\Carbon::parse($s->fecha_solicitud);
                    return $s->estado == 'APROBADO' && 
                           $fechaSub->year == $nominaDetalle->anio && 
                           $fechaSub->month == $mesNumDetalle;
                });
            @endphp
            <div class="detalle-grid">
                @forelse($subsidiosDetalle as $sub)
                    <div><strong>{{ $sub->tipo_subsidio }}:</strong><br>{{ number_format($sub->monto, 2) }}</div>
                @empty
                    <div><strong>Subsidios:</strong><br>0.00</div>
                @endforelse
                <div><strong>Total Subsidios:</strong><br>{{ number_format($subsidiosDetalle->sum('monto'), 2) }}</div>
            </div>
        </section>
        {{-- --- FIN NUEVO --- --}}


        <div class="detalle-dual">
            <div class="detalle-card ingresos">
                <h5>💰 Ingresos</h5>
                <ul>
                    <li><strong>Haber Básico:</strong> {{ number_format($nominaDetalle->haber_basico, 2) }}</li>
                    <li><strong>Bono Antigüedad:</strong> {{ number_format($nominaDetalle->bono_antiguedad, 2) }}</li>
                    <li><strong>Trabajo Extraordinario:</strong> {{ number_format($nominaDetalle->trabajo_extraordinario, 2) }}</li>
                    <li><strong>Pago Domingo:</strong> {{ number_format($nominaDetalle->pago_domingo, 2) }}</li>
                    <li><strong>Otros Bonos:</strong> {{ number_format($nominaDetalle->otros_bonos, 2) }}</li>
                </ul>
                <div class="total ingreso-total">Total Ganado: {{ number_format($nominaDetalle->total_ganado, 2) }}</div>
            </div>

            <div class="detalle-card descuentos">
                <h5>📉 Descuentos</h5>
                <ul>
                    <li><strong>Aporte Laboral:</strong> {{ number_format($nominaDetalle->aporte_laboral, 2) }}</li>
                    <li><strong>Aporte Nacional Solidario:</strong> {{ number_format($nominaDetalle->aporte_nacional_solidario, 2) }}</li>
                    <li><strong>RC-IVA:</strong> {{ number_format($nominaDetalle->rc_iva, 2) }}</li>
                    <li><strong>Anticipos:</strong> {{ number_format($nominaDetalle->anticipos, 2) }}</li>
                </ul>
                <div class="total descuento-total">Total Descuentos: {{ number_format($nominaDetalle->total_descuentos, 2) }}</div>
                <div class="total liquido-total">💵 Líquido Pagable: {{ number_format($nominaDetalle->liquido, 2) }}</div>
            </div>
        </div>
    </div>
</div>
@endif


    
    <div class="card">
        <div class="filter-section">
            <form method="GET" action="{{ route('nominas.index') }}">
                <select name="mes">
                    <option value="">Seleccionar mes</option>
                    <option value="Enero" {{ request('mes') == 'Enero' ? 'selected' : '' }}>Enero</option>
                    <option value="Febrero" {{ request('mes') == 'Febrero' ? 'selected' : '' }}>Febrero</option>
                    <option value="Marzo" {{ request('mes') == 'Marzo' ? 'selected' : '' }}>Marzo</option>
                    <option value="Abril" {{ request('mes') == 'Abril' ? 'selected' : '' }}>Abril</option>
                    <option value="Mayo" {{ request('mes') == 'Mayo' ? 'selected' : '' }}>Mayo</option>
                    <option value="Junio" {{ request('mes') == 'Junio' ? 'selected' : '' }}>Junio</option>
                    <option value="Julio" {{ request('mes') == 'Julio' ? 'selected' : '' }}>Julio</option>
                    <option value="Agosto" {{ request('mes') == 'Agosto' ? 'selected' : '' }}>Agosto</option>
                    <option value="Septiembre" {{ request('mes') == 'Septiembre' ? 'selected' : '' }}>Septiembre</option>
                    <option value="Octubre" {{ request('mes') == 'Octubre' ? 'selected' : '' }}>Octubre</option>
                    <option value="Noviembre" {{ request('mes') == 'Noviembre' ? 'selected' : '' }}>Noviembre</option>
                    <option value="Diciembre" {{ request('mes') == 'Diciembre' ? 'selected' : '' }}>Diciembre</option>
                </select>
                <select name="anio">
                    <option value="">Seleccionar año</option>
                    @for($year = date('Y'); $year >= 2020; $year--)
                        <option value="{{ $year }}" {{ request('anio') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            </form>
            <a href="{{ route('nominas.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Nómina</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Empleado</th>
                        <th>Periodo</th>
                        <th>SMN</th>
                        <th>Haber Básico</th>
                        <th>Bonificaciones</th>
                        <th>Descuentos</th>
                        <th>Neto</th>
                        <th>Subsidios</th> {{-- Columna Subsidios --}}
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nominas as $nomina)
                    <tr>
                        <td>{{ $nomina->id }}</td>
                        <td>{{ $nomina->empleado->nombres }} {{ $nomina->empleado->primerapellido }}</td>
                        <td>{{ $nomina->mes }} {{ $nomina->anio }}</td>
                        <td> {{ number_format($nomina->smn, 2) }}</td>
                        <td> {{ number_format($nomina->haber_basico, 2) }}</td>
                        <td> {{ number_format($nomina->bono_antiguedad + $nomina->trabajo_extraordinario + $nomina->pago_domingo + $nomina->otros_bonos, 2) }}</td>
                        <td> {{ number_format($nomina->total_descuentos, 2) }}</td>
                        <td> {{ number_format($nomina->liquido, 2) }}</td>
                        
                        {{-- Celda con el total de subsidios del periodo --}}
                        <td>
                            @php
                                // --- CORREGIDO --- Comentario cambiado a formato PHP
                                $mesNumero = \Carbon\Carbon::createFromLocaleFormat('F', 'es_ES', $nomina->mes)->month;

                                $subsidiosDelPeriodo = $nomina->empleado->subsidios->filter(function($subsidio) use ($mesNumero, $nomina) {
                                    $fechaSubsidio = \Carbon\Carbon::parse($subsidio->fecha_solicitud);
                                    
                                    return $subsidio->estado == 'APROBADO' && 
                                           $fechaSubsidio->year == $nomina->anio && 
                                           $fechaSubsidio->month == $mesNumero;
                                });

                                $totalSubsidios = $subsidiosDelPeriodo->sum('monto');
                            @endphp
                            
                            {{ number_format($totalSubsidios, 2) }}
                        </td>

                        <td>
                            <a href="{{ route('nominas.show', $nomina->id) }}" class="btn btn-secondary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('nominas.edit', $nomina->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                            <a href="{{ route('nominas.download', $nomina->id) }}" class="btn btn-secondary"><i class="fas fa-download"></i></a>
                            
                            {{-- Botón para gestionar subsidios --}}
                            <a href="{{ route('subsidios.reporte', $nomina->empleado_id) }}" 
                               class="btn btn-info" 
                               title="Gestionar Subsidios">
                                <i class="fas fa-gift"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- Colspan actualizado a 10 --}}
                        <td colspan="10" class="text-center">No se encontraron nóminas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($nominas->count())
            <div class="pagination-custom">
                {{-- (Tu código de paginación se queda igual) --}}
                <nav class="pagination-nav">
                    <ul class="pagination-list">
                        {{-- Botón Anterior --}}
                        <li class="pagination-item {{ $nominas->onFirstPage() ? 'disabled' : '' }}">
                            @if($nominas->onFirstPage())
                                <span class="pagination-link pagination-disabled">
                                    <i class="fas fa-chevron-left"></i> Anterior
                                </span>
                            @else
                                <a href="{{ $nominas->previousPageUrl() }}" class="pagination-link">
                                    <i class="fas fa-chevron-left"></i> Anterior
                                </a>
                            @endif
                        </li>

                        {{-- Números de página --}}
                        @php
                            $current = $nominas->currentPage();
                            $last = $nominas->lastPage();
                            $start = max($current - 2, 1);
                            $end = min($current + 2, $last);
                            
                            if ($end - $start < 4) {
                                if ($start == 1) {
                                    $end = min($start + 4, $last);
                                } else {
                                    $start = max($end - 4, 1);
                                }
                            }
                        @endphp

                        @if($start > 1)
                            <li class="pagination-item">
                                <a href="{{ $nominas->url(1) }}" class="pagination-link">1</a>
                            </li>
                            @if($start > 2)
                                <li class="pagination-item disabled">
                                    <span class="pagination-link pagination-ellipsis">...</span>
                                </li>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            <li class="pagination-item {{ $i == $current ? 'active' : '' }}">
                                @if($i == $current)
                                    <span class="pagination-link pagination-current">{{ $i }}</span>
                                @else
                                    <a href="{{ $nominas->url($i) }}" class="pagination-link">{{ $i }}</a>
                                @endif
                            </li>
                        @endfor

                        @if($end < $last)
                            @if($end < $last - 1)
                                <li class="pagination-item disabled">
                                    <span class="pagination-link pagination-ellipsis">...</span>
                                </li>
                            @endif
                            <li class="pagination-item">
                                <a href="{{ $nominas->url($last) }}" class="pagination-link">{{ $last }}</a>
                            </li>
                        @endif

                        {{-- Botón Siguiente --}}
                        <li class="pagination-item {{ !$nominas->hasMorePages() ? 'disabled' : '' }}">
                            @if(!$nominas->hasMorePages())
                                <span class="pagination-link pagination-disabled">
                                    Siguiente <i class="fas fa-chevron-right"></i>
                                </span>
                            @else
                                <a href="{{ $nominas->nextPageUrl() }}" class="pagination-link">
                                    Siguiente <i class="fas fa-chevron-right"></i>
                                </a>
                            @endif
                        </li>
                    </ul>
                    
                    {{-- Información de la página --}}
                    <div class="pagination-info">
                        Mostrando {{ $nominas->firstItem() ?? 0 }} - {{ $nominas->lastItem() ?? 0 }} de {{ $nominas->total() }} registros
                    </div>
                </nav>
            </div>
            @endif
        </div>
    </div>
    
    @if(isset($nominaDetalle))
    <script>
    function hideDetails() {
        window.location.href = "{{ route('nominas.index') }}";
    }
    </script>
    @endif
@endsection