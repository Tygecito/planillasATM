@extends('layouts.app')

@section('title', 'Crear Nóminas - Mi App')

@section('content')
    <div class="nominas-create-header">
        <h1 class="welcome-message">Crear Nóminas</h1>
        <div class="header-actions">
            <a href="{{ route('nominas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>Crear Nóminas para Todos los Empleados</h2>
        </div>
        
        <div class="card-body">
            <form action="{{ route('nominas.store') }}" method="POST" id="nominasForm">
                @csrf
                
                <div class="periodo-data">
                    <h4>Datos Generales del Periodo</h4>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="mes">Mes *</label>
                            <select class="form-control" id="mes" name="mes" required>
                                <option value="">Seleccionar mes</option>
                                <option value="Enero" {{ old('mes') == 'Enero' ? 'selected' : '' }}>Enero</option>
                                <option value="Febrero" {{ old('mes') == 'Febrero' ? 'selected' : '' }}>Febrero</option>
                                <option value="Marzo" {{ old('mes') == 'Marzo' ? 'selected' : '' }}>Marzo</option>
                                <option value="Abril" {{ old('mes') == 'Abril' ? 'selected' : '' }}>Abril</option>
                                <option value="Mayo" {{ old('mes') == 'Mayo' ? 'selected' : '' }}>Mayo</option>
                                <option value="Junio" {{ old('mes') == 'Junio' ? 'selected' : '' }}>Junio</option>
                                <option value="Julio" {{ old('mes') == 'Julio' ? 'selected' : '' }}>Julio</option>
                                <option value="Agosto" {{ old('mes') == 'Agosto' ? 'selected' : '' }}>Agosto</option>
                                <option value="Septiembre" {{ old('mes') == 'Septiembre' ? 'selected' : '' }}>Septiembre</option>
                                <option value="Octubre" {{ old('mes') == 'Octubre' ? 'selected' : '' }}>Octubre</option>
                                <option value="Noviembre" {{ old('mes') == 'Noviembre' ? 'selected' : '' }}>Noviembre</option>
                                <option value="Diciembre" {{ old('mes') == 'Diciembre' ? 'selected' : '' }}>Diciembre</option>
                            </select>
                            @error('mes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group col-md-3">
                            <label for="anio">Año *</label>
                            <select class="form-control" id="anio" name="anio" required>
                                <option value="">Seleccionar año</option>
                                @for($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ old('anio') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                            @error('anio')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group col-md-2">
                            <label for="dias_pagados">Días Pagados *</label>
                            <input type="number" class="form-control no-spinner" id="dias_pagados" name="dias_pagados" 
                                   value="{{ old('dias_pagados', 30) }}" required min="1" max="31" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                            @error('dias_pagados')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group col-md-2">
                            <label for="horas_pagadas">Horas Pagadas *</label>
                            <input type="number" class="form-control no-spinner" id="horas_pagadas" name="horas_pagadas" 
                                   value="{{ old('horas_pagadas', 160) }}" required min="0" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                            @error('horas_pagadas')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-2">
                            <label for="smn_comun">SMN General (Bs)</label>
                            <input type="text" class="form-control decimal-input" id="smn_comun" 
                                   placeholder="0.00" onchange="aplicarSMNGeneral()">
                            <small class="form-text text-muted">Aplica a todos los empleados</small>
                        </div>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="table table-bordered table-striped nominas-table">
                        <thead class="table-header">
                            <tr>
                                <th>Empleado</th>
                                <th>SMN (Bs)</th>
                                <th>Haber Básico (Bs)</th>
                                <th>Horas Extras</th>
                                <th>Bono Antigüedad (Bs)</th>
                                <th>Trab. Extra (Bs)</th>
                                <th>Pago Domingo (Bs)</th>
                                <th>Otros Bonos (Bs)</th>
                                <th>Aporte Laboral (Bs)</th>
                                <th>Aporte Solidario (Bs)</th>
                                <th>RC IVA (Bs)</th>
                                <th>Anticipos (Bs)</th>
                                <th>Total (Bs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($empleados as $empleado)
                            @php
                                $ultimaNomina = App\Models\Nomina::where('empleado_id', $empleado->id)
                                    ->orderBy('anio', 'desc')
                                    ->orderByRaw("FIELD(mes, 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre')")
                                    ->first();
                                $haberBasico = $ultimaNomina ? $ultimaNomina->haber_basico : '0.00';
                                $smn = $ultimaNomina ? $ultimaNomina->smn : '0.00';
                            @endphp
                            <tr>
                                <td class="empleado-info" data-fecha-ingreso="{{ $empleado->fecha_ingreso }}">
                                    <div class="empleado-nombre">{{ $empleado->nombres }}</div>
                                    <div class="empleado-apellidos">{{ $empleado->primerapellido }} {{ $empleado->segundoapellido }}</div>
                                    <div class="empleado-cargo">* {{ $empleado->cargo_laboral }}</div>
                                    <input type="hidden" name="empleados[{{ $empleado->id }}][empleado_id]" value="{{ $empleado->id }}">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control decimal-input smn-input" 
                                           name="empleados[{{ $empleado->id }}][smn]" 
                                           value="{{ old('empleados.'.$empleado->id.'.smn', $smn) }}" 
                                           placeholder="0.00" oninput="calcularTotal(this)">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control decimal-input" 
                                           name="empleados[{{ $empleado->id }}][haber_basico]" 
                                           value="{{ old('empleados.'.$empleado->id.'.haber_basico', $haberBasico) }}" 
                                           required placeholder="0.00" oninput="calcularTotal(this)">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control no-spinner" 
                                           name="empleados[{{ $empleado->id }}][horas_extras]" 
                                           value="{{ old('empleados.'.$empleado->id.'.horas_extras', '') }}" 
                                           placeholder="0" oninput="calcularTotal(this)">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control decimal-input bono-antiguedad-input" 
                                           name="empleados[{{ $empleado->id }}][bono_antiguedad]" 
                                           value="{{ old('empleados.'.$empleado->id.'.bono_antiguedad', '') }}" 
                                           placeholder="0.00" oninput="calcularTotal(this)">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control decimal-input" 
                                           name="empleados[{{ $empleado->id }}][trabajo_extraordinario]" 
                                           value="{{ old('empleados.'.$empleado->id.'.trabajo_extraordinario', '') }}" 
                                           placeholder="0.00" oninput="calcularTotal(this)">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control decimal-input" 
                                           name="empleados[{{ $empleado->id }}][pago_domingo]" 
                                           value="{{ old('empleados.'.$empleado->id.'.pago_domingo', '') }}" 
                                           placeholder="0.00" oninput="calcularTotal(this)">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control decimal-input" 
                                           name="empleados[{{ $empleado->id }}][otros_bonos]" 
                                           value="{{ old('empleados.'.$empleado->id.'.otros_bonos', '') }}" 
                                           placeholder="0.00" oninput="calcularTotal(this)">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control decimal-input aporte-laboral-input" 
                                           name="empleados[{{ $empleado->id }}][aporte_laboral]" 
                                           value="{{ old('empleados.'.$empleado->id.'.aporte_laboral', '') }}" 
                                           placeholder="0.00" oninput="calcularTotal(this)">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control decimal-input ans-input" 
                                           name="empleados[{{ $empleado->id }}][aporte_nacional_solidario]" 
                                           value="{{ old('empleados.'.$empleado->id.'.aporte_nacional_solidario', '') }}" 
                                           placeholder="0.00" oninput="calcularTotal(this)">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control decimal-input" 
                                           name="empleados[{{ $empleado->id }}][rc_iva]" 
                                           value="{{ old('empleados.'.$empleado->id.'.rc_iva', '') }}" 
                                           placeholder="0.00" oninput="calcularTotal(this)">
                                </td>
                                
                                <td>
                                    <input type="text" class="form-control decimal-input" 
                                           name="empleados[{{ $empleado->id }}][anticipos]" 
                                           value="{{ old('empleados.'.$empleado->id.'.anticipos', '') }}" 
                                           placeholder="0.00" oninput="calcularTotal(this)">
                                </td>
                                
                                <td class="total-cell">
                                    <span class="total-value">0.00</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Crear Todas las Nóminas
                    </button>
                    <a href="{{ route('nominas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection