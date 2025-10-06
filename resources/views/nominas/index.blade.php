@extends('layouts.app')

@section('title', 'Nóminas - Mi App')

@section('content')
    <h1 class="welcome-message">Gestión de Nóminas</h1>
    
    @if(isset($nominaDetalle))
    <!-- SECCIÓN DE DETALLES (solo se muestra cuando hay $nominaDetalle) -->
    <div class="card mb-4">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Detalles de Nómina #{{ $nominaDetalle->id }}</h2>
            <div>
                <a href="{{ route('nominas.edit', $nominaDetalle->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modificar
                </a>
                <a href="{{ route('nominas.download', $nominaDetalle->id) }}" class="btn btn-secondary">
                    <i class="fas fa-download"></i> Descargar
                </a>
                <button onclick="hideDetails()" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Datos del Empleado</h4>
                    <p><strong>Nombre:</strong> {{ $nominaDetalle->empleado->nombres }} {{ $nominaDetalle->empleado->primerapellido }}</p>
                    <p><strong>Documento:</strong> {{ $nominaDetalle->empleado->documento_identidad }}</p>
                    <p><strong>Cargo:</strong> {{ $nominaDetalle->empleado->cargo_laboral }}</p>
                </div>
                
                <div class="col-md-6">
                    <h4>Periodo</h4>
                    <p><strong>Mes:</strong> {{ $nominaDetalle->mes }}</p>
                    <p><strong>Año:</strong> {{ $nominaDetalle->anio }}</p>
                    <p><strong>Días pagados:</strong> {{ $nominaDetalle->dias_pagados }}</p>
                    <p><strong>Horas pagadas:</strong> {{ $nominaDetalle->horas_pagadas }}</p>
                    <p><strong>SMN:</strong>  {{ number_format($nominaDetalle->smn, 2) }}</p>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <h4>Ingresos</h4>
                    <p><strong>Haber Básico:</strong>  {{ number_format($nominaDetalle->haber_basico, 2) }}</p>
                    <p><strong>Bono Antigüedad:</strong>  {{ number_format($nominaDetalle->bono_antiguedad, 2) }}</p>
                    <p><strong>Trabajo Extraordinario:</strong>  {{ number_format($nominaDetalle->trabajo_extraordinario, 2) }}</p>
                    <p><strong>Pago Domingo:</strong>  {{ number_format($nominaDetalle->pago_domingo, 2) }}</p>
                    <p><strong>Otros Bonos:</strong>  {{ number_format($nominaDetalle->otros_bonos, 2) }}</p>
                    <p><strong>Total Ganado:</strong>  {{ number_format($nominaDetalle->total_ganado, 2) }}</p>
                </div>
                
                <div class="col-md-6">
                    <h4>Descuentos</h4>
                    <p><strong>Aporte Laboral:</strong>  {{ number_format($nominaDetalle->aporte_laboral, 2) }}</p>
                    <p><strong>Aporte Nacional Solidario:</strong>  {{ number_format($nominaDetalle->aporte_nacional_solidario, 2) }}</p>
                    <p><strong>RC-IVA:</strong>  {{ number_format($nominaDetalle->rc_iva, 2) }}</p>
                    <p><strong>Anticipos:</strong>  {{ number_format($nominaDetalle->anticipos, 2) }}</p>
                    <p><strong>Total Descuentos:</strong>  {{ number_format($nominaDetalle->total_descuentos, 2) }}</p>
                    <p><strong>Líquido Pagable:</strong>  {{ number_format($nominaDetalle->liquido, 2) }}</p>
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
                        <td>
                            <a href="{{ route('nominas.show', $nomina->id) }}" class="btn btn-secondary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('nominas.edit', $nomina->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                            <a href="{{ route('nominas.download', $nomina->id) }}" class="btn btn-secondary"><i class="fas fa-download"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No se encontraron nóminas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($nominas->count())
            <!-- PAGINACIÓN PERSONALIZADA EN ESPAÑOL -->
            <div class="pagination-custom">
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
                            
                            // Asegurar que siempre mostremos 5 páginas si es posible
                            if ($end - $start < 4) {
                                if ($start == 1) {
                                    $end = min($start + 4, $last);
                                } else {
                                    $start = max($end - 4, 1);
                                }
                            }
                        @endphp

                        {{-- Mostrar primera página si no está en el rango --}}
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

                        {{-- Rango de páginas --}}
                        @for ($i = $start; $i <= $end; $i++)
                            <li class="pagination-item {{ $i == $current ? 'active' : '' }}">
                                @if($i == $current)
                                    <span class="pagination-link pagination-current">{{ $i }}</span>
                                @else
                                    <a href="{{ $nominas->url($i) }}" class="pagination-link">{{ $i }}</a>
                                @endif
                            </li>
                        @endfor

                        {{-- Mostrar última página si no está en el rango --}}
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