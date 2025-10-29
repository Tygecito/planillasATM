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

        {{-- CONTENEDOR DE ERRORES GENERALES (Visible si Laravel detecta fallos) --}}
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <p><strong>Por favor, corrija los siguientes errores:</strong></p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="periodo-data">
            <h4>Datos Generales del Periodo</h4>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="mes">Mes *</label>
                    <select class="form-control @error('mes') is-invalid @enderror" id="mes" name="mes" required>
                        <option value="">Seleccionar mes</option>
                        @php
                            $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                        @endphp
                        @foreach ($meses as $mesNombre)
                            <option value="{{ $mesNombre }}" {{ old('mes') == $mesNombre ? 'selected' : '' }}>{{ $mesNombre }}</option>
                        @endforeach
                    </select>
                    @error('mes')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group col-md-3">
                    <label for="anio">Año *</label>
                    <select class="form-control @error('anio') is-invalid @enderror" id="anio" name="anio" required>
                        <option value="">Seleccionar año</option>
                        @for($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ old('anio') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                    @error('anio')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group col-md-2">
                    <label for="dias_pagados">Días Pagados *</label>
                    {{-- CLASE CORREGIDA: integer-input para asegurar solo enteros --}}
                    <input type="text" class="form-control integer-input @error('dias_pagados') is-invalid @enderror" id="dias_pagados" name="dias_pagados" 
                            value="{{ old('dias_pagados', 30) }}" required min="1" max="31">
                    @error('dias_pagados')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group col-md-2">
                    <label for="horas_pagadas">Horas Pagadas *</label>
                    {{-- CLASE CORREGIDA: integer-input para asegurar solo enteros --}}
                    <input type="text" class="form-control integer-input @error('horas_pagadas') is-invalid @enderror" id="horas_pagadas" name="horas_pagadas" 
                            value="{{ old('horas_pagadas', 160) }}" required min="0">
                    @error('horas_pagadas')
                        <small class="text-danger">{{ $message }}</small>
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
                        $empleadoIndex = $empleado->id;
                        $errorBase = 'empleados.' . $empleadoIndex;
                    @endphp
                    <tr>
                        <td class="empleado-info" data-fecha-ingreso="{{ $empleado->fecha_ingreso }}">
                            <div class="empleado-nombre">{{ $empleado->nombres }}</div>
                            <div class="empleado-apellidos">{{ $empleado->primerapellido }} {{ $empleado->segundoapellido }}</div>
                            <div class="empleado-cargo">* {{ $empleado->cargo_laboral }}</div>
                            <input type="hidden" name="empleados[{{ $empleadoIndex }}][empleado_id]" value="{{ $empleado->id }}">
                        </td>
                        
                        {{-- SMN (Numeric) --}}
                        <td>
                            <input type="text" name="empleados[{{ $empleadoIndex }}][smn]" 
                                    class="form-control decimal-input smn-input @error($errorBase . '.smn') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.smn', $smn) }}" 
                                    placeholder="0.00" oninput="calcularTotal(this)">
                            @error($errorBase . '.smn') <small class="text-danger">{{ $message }}</small> @enderror
                        </td>
                        
                        {{-- HABER BÁSICO (Numeric, Required) --}}
                        <td>
                            <input type="text" name="empleados[{{ $empleadoIndex }}][haber_basico]" 
                                    class="form-control decimal-input @error($errorBase . '.haber_basico') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.haber_basico', $haberBasico) }}" 
                                    required placeholder="0.00" oninput="calcularTotal(this)">
                            @error($errorBase . '.haber_basico') <small class="text-danger">{{ $message }}</small> @enderror
                        </td>
                        
                        {{-- HORAS EXTRAS (Integer) --}}
                        <td>
                            {{-- CLASE CORREGIDA: integer-input --}}
                            <input type="text" name="empleados[{{ $empleadoIndex }}][horas_extras]" 
                                    class="form-control integer-input no-spinner @error($errorBase . '.horas_extras') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.horas_extras', 0) }}" 
                                    placeholder="0" oninput="calcularTotal(this)">
                            @error($errorBase . '.horas_extras') <small class="text-danger">{{ $message }}</small> @enderror
                        </td>
                        
                        {{-- BONO ANTIGUEDAD (Numeric) --}}
                        <td>
                            <input type="text" name="empleados[{{ $empleadoIndex }}][bono_antiguedad]" 
                                    class="form-control decimal-input bono-antiguedad-input @error($errorBase . '.bono_antiguedad') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.bono_antiguedad', 0) }}" 
                                    placeholder="0.00" oninput="calcularTotal(this)">
                            @error($errorBase . '.bono_antiguedad') <small class="text-danger">{{ $message }}</small> @enderror
                        </td>

                        {{-- TRABAJO EXTRAORDINARIO (Numeric) --}}
                        <td>
                            <input type="text" name="empleados[{{ $empleadoIndex }}][trabajo_extraordinario]" 
                                    class="form-control decimal-input @error($errorBase . '.trabajo_extraordinario') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.trabajo_extraordinario', 0) }}" 
                                    placeholder="0.00" oninput="calcularTotal(this)">
                            @error($errorBase . '.trabajo_extraordinario') <small class="text-danger">{{ $message }}</small> @enderror
                        </td>

                        {{-- PAGO DOMINGO (Numeric) --}}
                        <td>
                            <input type="text" name="empleados[{{ $empleadoIndex }}][pago_domingo]" 
                                    class="form-control decimal-input @error($errorBase . '.pago_domingo') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.pago_domingo', 0) }}" 
                                    placeholder="0.00" oninput="calcularTotal(this)">
                            @error($errorBase . '.pago_domingo') <small class="text-danger">{{ $message }}</small> @enderror
                        </td>
                        
                        {{-- OTROS BONOS (Numeric) --}}
                        <td>
                            <input type="text" name="empleados[{{ $empleadoIndex }}][otros_bonos]" 
                                    class="form-control decimal-input @error($errorBase . '.otros_bonos') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.otros_bonos', 0) }}" 
                                    placeholder="0.00" oninput="calcularTotal(this)">
                            @error($errorBase . '.otros_bonos') <small class="text-danger">{{ $message }}</small> @enderror
                        </td>

                        {{-- APORTE LABORAL (Numeric) --}}
                        <td>
                            <input type="text" name="empleados[{{ $empleadoIndex }}][aporte_laboral]" 
                                    class="form-control decimal-input aporte-laboral-input @error($errorBase . '.aporte_laboral') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.aporte_laboral', 0) }}" 
                                    placeholder="0.00" oninput="calcularTotal(this)">
                            @error($errorBase . '.aporte_laboral') <small class="text-danger">{{ $message }}</small> @enderror
                        </td>
                        
                        {{-- APORTE NACIONAL SOLIDARIO (Numeric) --}}
                        <td>
                            <input type="text" name="empleados[{{ $empleadoIndex }}][aporte_nacional_solidario]" 
                                    class="form-control decimal-input ans-input @error($errorBase . '.aporte_nacional_solidario') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.aporte_nacional_solidario', 0) }}" 
                                    placeholder="0.00" oninput="calcularTotal(this)">
                            @error($errorBase . '.aporte_nacional_solidario') <small class="text-danger">{{ $message }}</small> @enderror
                        </td>

                        {{-- RC IVA (Numeric) --}}
                        <td>
                            <input type="text" name="empleados[{{ $empleadoIndex }}][rc_iva]" 
                                    class="form-control decimal-input @error($errorBase . '.rc_iva') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.rc_iva', 0) }}" 
                                    placeholder="0.00" oninput="calcularTotal(this)">
                            @error($errorBase . '.rc_iva') <small class="text-danger">{{ $message }}</small> @enderror
                        </td>
                        
                        {{-- ANTICIPOS (Numeric) --}}
                        <td>
                            <input type="text" name="empleados[{{ $empleadoIndex }}][anticipos]" 
                                    class="form-control decimal-input @error($errorBase . '.anticipos') is-invalid @enderror" 
                                    value="{{ old($errorBase . '.anticipos', 0) }}" 
                                    placeholder="0.00" oninput="calcularTotal(this)">
                            @error($errorBase . '.anticipos') <small class="text-danger">{{ $message }}</small> @enderror
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
            <button type="button" class="btn btn-primary btn-lg" onclick="showCustomConfirm()">
                <i class="fas fa-file-invoice"></i> Generar Nóminas
            </button>
            <a href="{{ route('nominas.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>


</div>

{{-- Modal de Confirmación Personalizado (Idéntico al de edición) --}}

<div id="custom-confirm-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
<div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 400px; border-radius: 8px;">
<p><strong>¿Estás seguro de generar las nóminas para el periodo seleccionado?</strong></p>
<button onclick="submitForm()" style="background-color: #942044; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">Sí, Generar</button>
<button onclick="hideCustomConfirm()" style="background-color: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
</div>
</div>

{{-- CSS INLINE para garantizar que los errores y el modal se posicionen correctamente --}}

<style>
.alert-danger { background-color: #f8d7da; color: #721c24; padding: 1rem; margin-bottom: 1.5rem; border: 1px solid #f5c6cb; border-radius: 4px; }
.alert-danger ul { list-style: none; margin: 0; padding: 0; }
.text-danger { color: #dc3545 !important; font-size: 0.85rem; margin-top: 5px; display: block; }
#custom-confirm-modal > div { text-align: center; }
.form-row { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
.form-group { flex: 1; }
.form-actions { margin-top: 2rem; border-top: 1px solid #ddd; padding-top: 1.5rem; display: flex; justify-content: flex-end; gap: 10px; }
.table-container { overflow-x: auto; }
table { min-width: 1200px; border-collapse: collapse; margin-top: 1rem; }
th, td { white-space: nowrap; padding: 8px; font-size: 0.9em; border: 1px solid #ddd; }
th { background-color: #942044; color: white; }
.form-control { width: 100%; box-sizing: border-box; }
.decimal-input { text-align: right; }
.integer-input { text-align: right; } /* Añadido para consistencia */
.empleado-info { font-weight: bold; }
.total-cell { font-weight: bold; background-color: #f0f0f0; }

@media (max-width: 768px) {
.form-row { flex-direction: column; }
}

</style>

<script>
function showCustomConfirm() { document.getElementById('custom-confirm-modal').style.display = 'block'; }
function hideCustomConfirm() { document.getElementById('custom-confirm-modal').style.display = 'none'; }
function submitForm() {
hideCustomConfirm();
document.getElementById('nominasForm').submit();
}
window.onclick = function(event) {
const modal = document.getElementById('custom-confirm-modal');
if (event.target === modal) { modal.style.display = 'none'; }
}

// Nota: Las funciones aplicarSMNGeneral, calcularTotal, setupIntegerValidation, etc.,
// Se asume que están cargadas desde el archivo public/js/nomina.js
</script>

@endsection