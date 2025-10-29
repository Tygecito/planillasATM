@extends('layouts.app')

@section('title', 'Editar Nómina - Mi App')

@section('content')

<h1 class="welcome-message">Editar Nómina #{{ $nomina->id }}</h1>

<div class="card p-4">
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

<form action="{{ route('nominas.update', $nomina->id) }}" method="POST" id="editNominaForm">
@csrf
@method('PUT')

<!-- SECCIÓN 1: Datos Básicos (3 Columnas) -->

<h3>Información General</h3>
<div class="form-grid-3cols">

<div class="form-group">
<label for="empleado_id">Empleado</label>
{{-- BLOQUEADO: No se debe cambiar el empleado al editar una nómina --}}
<select name="empleado_id" class="form-control" required id="empleado_id" disabled>
@foreach($empleados as $empleado)
<option value="{{ $empleado->id }}"
{{ old('empleado_id', $nomina->empleado_id) == $empleado->id ? 'selected' : '' }}
data-fecha-ingreso="{{ $empleado->fecha_ingreso }}">
{{ $empleado->nombres }} {{ $empleado->primerapellido }}
</option>
@endforeach
</select>
{{-- Enviar el valor real con un campo hidden ya que el select está disabled --}}
<input type="hidden" name="empleado_id" value="{{ old('empleado_id', $nomina->empleado_id) }}">
@error('empleado_id') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="mes">Mes</label>
{{-- BLOQUEADO: No se debe cambiar el mes --}}
<select name="mes_display" class="form-control" required id="mes" disabled>
@foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $mes)
<option value="{{ $mes }}" {{ old('mes', $nomina->mes) == $mes ? 'selected' : '' }}>{{ $mes }}</option>
@endforeach
</select>
<input type="hidden" name="mes" value="{{ old('mes', $nomina->mes) }}">
@error('mes') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="anio">Año</label>
{{-- BLOQUEADO: No se debe cambiar el año --}}
<input type="text" name="anio" class="form-control integer-input @error('anio') is-invalid @enderror"
value="{{ old('anio', $nomina->anio) }}" required id="anio" readonly style="background-color: #e9ecef;">
@error('anio') <small class="text-danger">{{ $message }}</small> @enderror
</div>

</div>

<!-- SECCIÓN 2: Base Salarial y Horas (3 Columnas) -->

<h3>Base de Cálculo</h3>
<div class="form-grid-3cols">

<div class="form-group">
<label for="smn">Salario Mínimo Nacional (SMN)</label>
{{-- BLOQUEADO POR DEUDA: Es un valor de referencia, solo editable con el botón de cálculo --}}
<input type="text" name="smn" class="form-control smn-input decimal-input @error('smn') is-invalid @enderror"
value="{{ old('smn', $nomina->smn) }}" placeholder="0.00" id="smn" oninput="calcularTotalesEdit()" readonly style="background-color: #e9ecef;">
@error('smn') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="horas_pagadas">Horas Pagadas</label>
{{-- Mantener editable para correcciones --}}
<input type="text" name="horas_pagadas" class="form-control integer-input @error('horas_pagadas') is-invalid @enderror"
value="{{ old('horas_pagadas', $nomina->horas_pagadas) }}" required id="horas_pagadas">
@error('horas_pagadas') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="dias_pagados">Días Pagados</label>
{{-- Mantener editable para correcciones --}}
<input type="text" name="dias_pagados" class="form-control integer-input @error('dias_pagados') is-invalid @enderror"
value="{{ old('dias_pagados', $nomina->dias_pagados) }}" required id="dias_pagados">
@error('dias_pagados') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="haber_basico">Haber Básico *</label>
{{-- Mantener editable para correcciones --}}
<input type="text" name="haber_basico" class="form-control decimal-input @error('haber_basico') is-invalid @enderror"
value="{{ old('haber_basico', $nomina->haber_basico) }}" required id="haber_basico" oninput="calcularTotalesEdit()">
@error('haber_basico') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="horas_extras">Horas Extras</label>
{{-- Mantener editable para correcciones --}}
<input type="text" name="horas_extras" class="form-control integer-input @error('horas_extras') is-invalid @enderror"
value="{{ old('horas_extras', $nomina->horas_extras) }}" id="horas_extras">
@error('horas_extras') <small class="text-danger">{{ $message }}</small> @enderror
</div>

</div>

<!-- SECCIÓN 3: Bonificaciones e Ingresos Adicionales (4 Columnas) -->

<h3>Bonificaciones e Ingresos</h3>
<div class="form-grid-4cols">

<div class="form-group">
<label for="bono_antiguedad">Bono Antigüedad</label>
{{-- BLOQUEADO: Solo se edita al presionar 'Calcular Automáticos' --}}
<input type="text" name="bono_antiguedad" class="form-control bono-antiguedad-input decimal-input @error('bono_antiguedad') is-invalid @enderror"
value="{{ old('bono_antiguedad', $nomina->bono_antiguedad) }}" id="bono_antiguedad" oninput="calcularTotalesEdit()" readonly style="background-color: #e9ecef;">
@error('bono_antiguedad') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="trabajo_extraordinario">Trabajo Extraordinario</label>
<input type="text" name="trabajo_extraordinario" class="form-control decimal-input @error('trabajo_extraordinario') is-invalid @enderror"
value="{{ old('trabajo_extraordinario', $nomina->trabajo_extraordinario) }}" id="trabajo_extraordinario" oninput="calcularTotalesEdit()">
@error('trabajo_extraordinario') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="pago_domingo">Pago Domingo</label>
<input type="text" name="pago_domingo" class="form-control decimal-input @error('pago_domingo') is-invalid @enderror"
value="{{ old('pago_domingo', $nomina->pago_domingo) }}" id="pago_domingo" oninput="calcularTotalesEdit()">
@error('pago_domingo') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="otros_bonos">Otros Bonos</label>
<input type="text" name="otros_bonos" class="form-control decimal-input @error('otros_bonos') is-invalid @enderror"
value="{{ old('otros_bonos', $nomina->otros_bonos) }}" id="otros_bonos" oninput="calcularTotalesEdit()">
@error('otros_bonos') <small class="text-danger">{{ $message }}</small> @enderror
</div>

</div>

<!-- SECCIÓN 4: Descuentos (4 Columnas) -->

<h3>Descuentos</h3>
<div class="form-grid-4cols">

<div class="form-group">
<label for="aporte_laboral">Aporte Laboral</label>
{{-- BLOQUEADO: Solo se edita al presionar 'Calcular Automáticos' --}}
<input type="text" name="aporte_laboral" class="form-control aporte-laboral-input decimal-input @error('aporte_laboral') is-invalid @enderror"
value="{{ old('aporte_laboral', $nomina->aporte_laboral) }}" id="aporte_laboral" oninput="calcularTotalesEdit()" readonly style="background-color: #e9ecef;">
@error('aporte_laboral') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="aporte_nacional_solidario">Aporte Nacional Solidario</label>
{{-- BLOQUEADO: Solo se edita al presionar 'Calcular Automáticos' --}}
<input type="text" name="aporte_nacional_solidario" class="form-control ans-input decimal-input @error('aporte_nacional_solidario') is-invalid @enderror"
value="{{ old('aporte_nacional_solidario', $nomina->aporte_nacional_solidario) }}" id="aporte_nacional_solidario" oninput="calcularTotalesEdit()" readonly style="background-color: #e9ecef;">
@error('aporte_nacional_solidario') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="rc_iva">RC IVA</label>
<input type="text" name="rc_iva" class="form-control decimal-input @error('rc_iva') is-invalid @enderror"
value="{{ old('rc_iva', $nomina->rc_iva) }}" id="rc_iva" oninput="calcularTotalesEdit()">
@error('rc_iva') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="anticipos">Anticipos</label>
<input type="text" name="anticipos" class="form-control decimal-input @error('anticipos') is-invalid @enderror"
value="{{ old('anticipos', $nomina->anticipos) }}" id="anticipos" oninput="calcularTotalesEdit()">
@error('anticipos') <small class="text-danger">{{ $message }}</small> @enderror
</div>

</div>

<!-- SECCIÓN 5: Totales (3 Columnas, solo lectura) -->

<h3>Resumen</h3>
<div class="form-grid-3cols">

<div class="form-group">
<label for="total_ganado">Total Ganado</label>
{{-- BLOQUEADO: Campo calculado --}}
<input type="text" name="total_ganado" class="form-control"
value="{{ $nomina->total_ganado }}" readonly style="background-color: #e9ecef;" id="total_ganado">
</div>

<div class="form-group">
<label for="total_descuentos">Total Descuentos</label>
{{-- BLOQUEADO: Campo calculado --}}
<input type="text" name="total_descuentos" class="form-control"
value="{{ $nomina->total_descuentos }}" readonly style="background-color: #e9ecef;" id="total_descuentos">
</div>

<div class="form-group">
<label for="liquido">Líquido Pagable</label>
{{-- BLOQUEADO: Campo calculado --}}
<input type="text" name="liquido" class="form-control"
value="{{ $nomina->liquido }}" readonly style="background-color: #e9ecef;" id="liquido">
</div>

</div>

<!-- SECCIÓN 6: Botones de acción -->

<div class="form-actions">
<!-- Botones de cálculo a la izquierda lógica -->
<div class="form-actions-left">
<button type="button" class="btn btn-info" onclick="calcularAutomaticosEdit()">
<i class="fas fa-calculator"></i> Calcular Automáticos
</button>
<button type="button" class="btn btn-warning" onclick="limpiarCalculosEdit()">
<i class="fas fa-eraser"></i> Limpiar Cálculos
</button>
</div>
<!-- Botones de guardar/cancelar a la derecha lógica -->
<div>
<a href="{{ route('nominas.index') }}" class="btn btn-secondary">
<i class="fas fa-arrow-left"></i> Cancelar
</a>
{{-- Cambiado a type="button" para activar el modal --}}
<button type="button" class="btn btn-primary" onclick="showCustomConfirm()">
<i class="fas fa-save"></i> Actualizar Nómina
</button>
</div>
</div>

</form>

</div>

<!-- Modal de Confirmación Personalizado -->

<div id="custom-confirm-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
<div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 400px; border-radius: 8px;">
<p><strong>¿Estás seguro de actualizar esta nómina?</strong></p>
<button onclick="submitForm()" class="btn btn-primary" style="margin-right: 10px;">Sí, Actualizar</button>
<button onclick="hideCustomConfirm()" class="btn btn-secondary">Cancelar</button>
</div>
</div>

@push('scripts')

<script>
// ==============================================
// Lógica del Modal
// ==============================================
function showCustomConfirm() { document.getElementById('custom-confirm-modal').style.display = 'block'; }
function hideCustomConfirm() { document.getElementById('custom-confirm-modal').style.display = 'none'; }
function submitForm() {
hideCustomConfirm();
document.getElementById('editNominaForm').submit();
}
window.onclick = function(event) {
const modal = document.getElementById('custom-confirm-modal');
if (event.target === modal) { modal.style.display = 'none'; }
}

// ==============================================
// FUNCIONES ESPECÍFICAS PARA EDITAR NÓMINA (Lógica de Cálculo)
// ==============================================

function calcularTotalesEdit() {
// Obtener valores de los inputs
// Se usa parseFloat() ya que los inputs son text y dependen de JS para ser números
const haberBasico = parseFloat(document.getElementById('haber_basico').value) || 0;
const bonoAntiguedad = parseFloat(document.getElementById('bono_antiguedad').value) || 0;
const trabajoExtraordinario = parseFloat(document.getElementById('trabajo_extraordinario').value) || 0;
const pagoDomingo = parseFloat(document.getElementById('pago_domingo').value) || 0;
const otrosBonos = parseFloat(document.getElementById('otros_bonos').value) || 0;

const aporteLaboral = parseFloat(document.getElementById('aporte_laboral').value) || 0;
const aporteSolidario = parseFloat(document.getElementById('aporte_nacional_solidario').value) || 0;
const rcIva = parseFloat(document.getElementById('rc_iva').value) || 0;
const anticipos = parseFloat(document.getElementById('anticipos').value) || 0;

// Calcular total ganado
const totalGanado = haberBasico + bonoAntiguedad + trabajoExtraordinario + pagoDomingo + otrosBonos;

// Calcular total descuentos
const totalDescuentos = aporteLaboral + aporteSolidario + rcIva + anticipos;

// Calcular líquido
const liquido = totalGanado - totalDescuentos;

// Actualizar los campos de totales (usamos toFixed(2) para formato decimal)
document.getElementById('total_ganado').value = totalGanado.toFixed(2);
document.getElementById('total_descuentos').value = totalDescuentos.toFixed(2);
document.getElementById('liquido').value = liquido.toFixed(2);

}

function calcularAutomaticosEdit() {
// Se asume que las funciones calcularAntiguedad, calcularAporteLaboral, calcularANS
// están disponibles globalmente desde public/js/nomina.js
const smn = parseFloat(document.getElementById('smn').value) || 0;
const empleadoSelect = document.getElementById('empleado_id');
const fechaIngreso = empleadoSelect.options[empleadoSelect.selectedIndex].getAttribute('data-fecha-ingreso');

// 1. Bono de antigüedad
if (fechaIngreso && smn > 0 && typeof calcularAntiguedad !== 'undefined') {
const bonoAntiguedad = calcularAntiguedad(fechaIngreso, smn);
document.getElementById('bono_antiguedad').value = bonoAntiguedad.toFixed(2);
}

// Recalcular Total Ganado Parcial para cálculos de descuentos (debe ser después de actualizar el bono)
const haberBasico = parseFloat(document.getElementById('haber_basico').value) || 0;
const bonoAntiguedadActual = parseFloat(document.getElementById('bono_antiguedad').value) || 0;
const trabajoExtraordinario = parseFloat(document.getElementById('trabajo_extraordinario').value) || 0;
const pagoDomingo = parseFloat(document.getElementById('pago_domingo').value) || 0;
const otrosBonos = parseFloat(document.getElementById('otros_bonos').value) || 0;

const totalGanadoParcial = haberBasico + bonoAntiguedadActual + trabajoExtraordinario + pagoDomingo + otrosBonos;

// 2. Aporte laboral
if (typeof calcularAporteLaboral !== 'undefined') {
const aporteLaboral = calcularAporteLaboral(totalGanadoParcial);
document.getElementById('aporte_laboral').value = aporteLaboral.toFixed(2);
}

// 3. ANS
if (typeof calcularANS !== 'undefined') {
const ans = calcularANS(totalGanadoParcial);
document.getElementById('aporte_nacional_solidario').value = ans.toFixed(2);
}

// Recalcular totales
calcularTotalesEdit();

}

function limpiarCalculosEdit() {
// Limpiar campos calculados automáticamente
document.getElementById('bono_antiguedad').value = '';
document.getElementById('aporte_laboral').value = '';
document.getElementById('aporte_nacional_solidario').value = '';
document.getElementById('rc_iva').value = '';
document.getElementById('anticipos').value = '';

// Recalcular totales
calcularTotalesEdit();

}

// Inicialización y validación decimal
document.addEventListener('DOMContentLoaded', function() {
// Inicializar las validaciones de decimales y enteros si están disponibles
// Estas funciones deben estar definidas en public/js/nomina.js
if (typeof setupDecimalValidation === 'function') {
setupDecimalValidation();
}
if (typeof setupIntegerValidation === 'function') {
setupIntegerValidation();
}

// Calcular totales iniciales
calcularTotalesEdit();

// Configurar evento para cuando cambie el empleado (para recalcular antigüedad)
document.getElementById('empleado_id').addEventListener('change', function() {
const smn = parseFloat(document.getElementById('smn').value) || 0;
if (smn > 0) {
calcularAutomaticosEdit();
}
});

});
</script>

@endpush
@endsection