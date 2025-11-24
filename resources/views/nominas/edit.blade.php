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

<h3>Información General</h3>
<div class="form-grid-3cols">

<div class="form-group">
<label for="empleado_id">Empleado</label>
<select name="empleado_id" class="form-control" required id="empleado_id" disabled>
@foreach($empleados as $empleado)
<option value="{{ $empleado->id }}"
{{ old('empleado_id', $nomina->empleado_id) == $empleado->id ? 'selected' : '' }}
data-fecha-ingreso="{{ $empleado->fecha_ingreso }}">
{{ $empleado->nombres }} {{ $empleado->primerapellido }}
</option>
@endforeach
</select>
<input type="hidden" name="empleado_id" value="{{ old('empleado_id', $nomina->empleado_id) }}">
@error('empleado_id') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="mes">Mes</label>
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
<input type="text" name="anio" class="form-control integer-input @error('anio') is-invalid @enderror"
value="{{ old('anio', $nomina->anio) }}" required id="anio" readonly style="background-color: #e9ecef;">
@error('anio') <small class="text-danger">{{ $message }}</small> @enderror
</div>

</div>

<h3>Base de Cálculo</h3>
<div class="form-grid-3cols">

<div class="form-group">
<label for="smn">Salario Mínimo Nacional (SMN)</label>
<input type="text" name="smn" class="form-control smn-input decimal-input @error('smn') is-invalid @enderror"
value="{{ old('smn', $nomina->smn) }}" placeholder="0.00" id="smn" oninput="calcularTotalesEdit()" readonly style="background-color: #e9ecef;">
@error('smn') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="horas_pagadas">Horas Pagadas</label>
<input type="text" name="horas_pagadas" class="form-control integer-input @error('horas_pagadas') is-invalid @enderror"
value="{{ old('horas_pagadas', $nomina->horas_pagadas) }}" required id="horas_pagadas" oninput="calcularTotalesEdit()">
@error('horas_pagadas') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="dias_pagados">Días Pagados</label>
<input type="text" name="dias_pagados" class="form-control integer-input @error('dias_pagados') is-invalid @enderror"
value="{{ old('dias_pagados', $nomina->dias_pagados) }}" required id="dias_pagados" oninput="calcularTotalesEdit()">
@error('dias_pagados') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="haber_basico">Haber Básico *</label>
<input type="text" name="haber_basico" class="form-control decimal-input @error('haber_basico') is-invalid @enderror"
value="{{ old('haber_basico', $nomina->haber_basico) }}" required id="haber_basico" oninput="calcularTotalesEdit()">
@error('haber_basico') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="horas_extras">Horas Extras (Cant.)</label>
<input type="text" name="horas_extras" class="form-control integer-input @error('horas_extras') is-invalid @enderror"
value="{{ old('horas_extras', $nomina->horas_extras) }}" id="horas_extras" oninput="calcularTotalesEdit()">
@error('horas_extras') <small class="text-danger">{{ $message }}</small> @enderror
</div>

</div>

<h3>Bonificaciones e Ingresos</h3>
<div class="form-grid-4cols">

<div class="form-group">
<label for="bono_antiguedad">Bono Antigüedad</label>
{{-- BLOQUEADO: Se calcula automáticamente --}}
<input type="text" name="bono_antiguedad" class="form-control bono-antiguedad-input decimal-input @error('bono_antiguedad') is-invalid @enderror"
value="{{ old('bono_antiguedad', $nomina->bono_antiguedad) }}" id="bono_antiguedad" readonly style="background-color: #e9ecef;">
@error('bono_antiguedad') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="trabajo_extraordinario">Trabajo Extraordinario (Bs)</label>
{{-- BLOQUEADO: Se calcula automáticamente --}}
<input type="text" name="trabajo_extraordinario" class="form-control trabajo-extraordinario-input decimal-input @error('trabajo_extraordinario') is-invalid @enderror"
value="{{ old('trabajo_extraordinario', $nomina->trabajo_extraordinario) }}" id="trabajo_extraordinario" readonly style="background-color: #e9ecef;">
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

<h3>Descuentos e Impuestos (RC-IVA)</h3>
<div class="form-grid-6cols">

<div class="form-group">
<label for="aporte_laboral">Aporte Laboral</label>
{{-- BLOQUEADO: Calculado --}}
<input type="text" name="aporte_laboral" class="form-control aporte-laboral-input decimal-input @error('aporte_laboral') is-invalid @enderror"
value="{{ old('aporte_laboral', $nomina->aporte_laboral) }}" id="aporte_laboral" readonly style="background-color: #e9ecef;">
@error('aporte_laboral') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="aporte_nacional_solidario">Aporte Nacional Solidario</label>
{{-- BLOQUEADO: Calculado --}}
<input type="text" name="aporte_nacional_solidario" class="form-control ans-input decimal-input @error('aporte_nacional_solidario') is-invalid @enderror"
value="{{ old('aporte_nacional_solidario', $nomina->aporte_nacional_solidario) }}" id="aporte_nacional_solidario" readonly style="background-color: #e9ecef;">
@error('aporte_nacional_solidario') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- NUEVOS CAMPOS RC-IVA: INPUTS --}}
<div class="form-group">
<label for="rc_iva_f110_monto">Total Facturas F-110</label>
<input type="text" name="rc_iva_f110_monto" class="form-control decimal-input @error('rc_iva_f110_monto') is-invalid @enderror"
value="{{ old('rc_iva_f110_monto', $nomina->rc_iva_f110_monto ?? 0) }}" id="rc_iva_f110_monto" oninput="calcularTotalesEdit()">
@error('rc_iva_f110_monto') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="rc_iva_saldo_anterior">Saldo RC-IVA Ant.</label>
<input type="text" name="rc_iva_saldo_anterior" class="form-control decimal-input @error('rc_iva_saldo_anterior') is-invalid @enderror"
value="{{ old('rc_iva_saldo_anterior', $nomina->rc_iva_saldo_anterior ?? 0) }}" id="rc_iva_saldo_anterior" oninput="calcularTotalesEdit()">
@error('rc_iva_saldo_anterior') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- NUEVOS CAMPOS RC-IVA: OUTPUTS --}}
<div class="form-group">
<label for="rc_iva">RC IVA a Retener</label>
{{-- BLOQUEADO: Calculado --}}
<input type="text" name="rc_iva" class="form-control rc-iva-input decimal-input @error('rc_iva') is-invalid @enderror"
value="{{ old('rc_iva', $nomina->rc_iva) }}" id="rc_iva" readonly style="background-color: #e9ecef;">
@error('rc_iva') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="rc_iva_saldo_siguiente">Saldo Próx. Mes</label>
{{-- BLOQUEADO: Calculado --}}
<input type="text" name="rc_iva_saldo_siguiente" class="form-control rc-iva-saldo-siguiente-input decimal-input @error('rc_iva_saldo_siguiente') is-invalid @enderror"
value="{{ old('rc_iva_saldo_siguiente', $nomina->rc_iva_saldo_siguiente ?? 0) }}" id="rc_iva_saldo_siguiente" readonly style="background-color: #e9ecef;">
@error('rc_iva_saldo_siguiente') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
<label for="anticipos">Anticipos</label>
<input type="text" name="anticipos" class="form-control decimal-input @error('anticipos') is-invalid @enderror"
value="{{ old('anticipos', $nomina->anticipos) }}" id="anticipos" oninput="calcularTotalesEdit()">
@error('anticipos') <small class="text-danger">{{ $message }}</small> @enderror
</div>

</div>

<h3>Resumen</h3>
<div class="form-grid-3cols">

<div class="form-group">
<label for="total_ganado">Total Ganado</label>
{{-- BLOQUEADO: Campo calculado --}}
<input type="text" name="total_ganado" class="form-control total-ganado-display"
value="{{ old('total_ganado', $nomina->total_ganado) }}" readonly style="background-color: #e9ecef;" id="total_ganado">
</div>

<div class="form-group">
<label for="total_descuentos">Total Descuentos</label>
{{-- BLOQUEADO: Campo calculado --}}
<input type="text" name="total_descuentos" class="form-control total-descuentos-display"
value="{{ old('total_descuentos', $nomina->total_descuentos) }}" readonly style="background-color: #e9ecef;" id="total_descuentos">
</div>

<div class="form-group">
<label for="liquido">Líquido Pagable</label>
{{-- BLOQUEADO: Campo calculado --}}
<input type="text" name="liquido" class="form-control liquido-display"
value="{{ old('liquido', $nomina->liquido) }}" readonly style="background-color: #e9ecef;" id="liquido">
</div>

</div>

<div class="form-actions">
<div class="form-actions-left">
<button type="button" class="btn btn-info" onclick="calcularAutomaticosEdit()">
<i class="fas fa-calculator"></i> Calcular Automáticos
</button>
<button type="button" class="btn btn-warning" onclick="limpiarCalculosEdit()">
<i class="fas fa-eraser"></i> Limpiar Cálculos
</button>
</div>
<div>
<a href="{{ route('nominas.index') }}" class="btn btn-secondary">
<i class="fas fa-arrow-left"></i> Cancelar
</a>
<button type="button" class="btn btn-primary" onclick="showCustomConfirm()">
<i class="fas fa-save"></i> Actualizar Nómina
</button>
</div>
</div>

</form>

</div>

<div id="custom-confirm-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
<div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 400px; border-radius: 8px;">
<p><strong>¿Estás seguro de actualizar esta nómina?</strong></p>
<button onclick="submitForm()" class="btn btn-primary" style="margin-right: 10px;">Sí, Actualizar</button>
<button onclick="hideCustomConfirm()" class="btn btn-secondary">Cancelar</button>
</div>
</div>

@push('scripts')
{{-- CARGA EL ARCHIVO GLOBAL DONDE ESTÁN LAS FUNCIONES calcularAntiguedad, calcularANS, etc. --}}
<script src="{{ asset('js/nominas.js') }}"></script> 

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
// FUNCIÓN DE CÁLCULO PRINCIPAL (DISPARADA POR oninput)
// Se llama por cada cambio manual y por los botones
// ==============================================

function calcularTotalesEdit() {
    // --- OBTENER INPUTS ---
    const smn = parseFloat(document.getElementById('smn').value) || 0;
    const diasPagados = parseFloat(document.getElementById('dias_pagados').value) || 30; 
    const haberBasico = parseFloat(document.getElementById('haber_basico').value) || 0;
    const horasExtras = parseInt(document.getElementById('horas_extras').value) || 0;

    const pagoDomingo = parseFloat(document.getElementById('pago_domingo').value) || 0;
    const otrosBonos = parseFloat(document.getElementById('otros_bonos').value) || 0;
    const anticipos = parseFloat(document.getElementById('anticipos').value) || 0;

    // Campos RC-IVA (INPUTS)
    const f110Monto = parseFloat(document.getElementById('rc_iva_f110_monto').value) || 0;
    const saldoAnterior = parseFloat(document.getElementById('rc_iva_saldo_anterior').value) || 0;

    // --- 1. CÁLCULO DE INGRESOS AUTOMÁTICOS ---

    // A. Horas Extra (Contable: HB / Dias / 8 * 2 * Cantidad)
    let pagoExtraCalculado = 0;
    if (haberBasico > 0 && diasPagados > 0 && horasExtras > 0) {
        const jornalDiario = haberBasico / diasPagados;
        const valorHoraNormal = jornalDiario / 8;
        const valorHoraExtra = valorHoraNormal * 2;
        pagoExtraCalculado = valorHoraExtra * horasExtras;
    }
    // Escribir el resultado en el campo Trabajo Extra (Bs)
    document.getElementById('trabajo_extraordinario').value = pagoExtraCalculado.toFixed(2);
    const trabajoExtraordinario = pagoExtraCalculado;


    // B. Bono Antigüedad (Fuerza el cálculo)
    const empleadoSelect = document.getElementById('empleado_id');
    const fechaIngreso = empleadoSelect.options[empleadoSelect.selectedIndex].getAttribute('data-fecha-ingreso');
    let bonoAntiguedadCalculado = 0;

    if (fechaIngreso && smn > 0) {
        // Reutilizamos la función global calcularAntiguedad de nomina.js
        bonoAntiguedadCalculado = calcularAntiguedad(fechaIngreso, smn); 
    }
    document.getElementById('bono_antiguedad').value = bonoAntiguedadCalculado.toFixed(2);
    const bonoAntiguedad = bonoAntiguedadCalculado; // Usamos el valor calculado

    
    // C. Total Ganado
    const totalGanado = haberBasico + bonoAntiguedad + trabajoExtraordinario + pagoDomingo + otrosBonos;
    document.getElementById('total_ganado').value = totalGanado.toFixed(2);


    // --- 2. CÁLCULO DE DESCUENTOS AUTOMÁTICOS ---
    
    // A. AFP / ANS (Reutilizamos funciones de nomina.js)
    const aporteLaboralCalculado = calcularAporteLaboral(totalGanado);
    const ansCalculado = calcularANS(totalGanado);
    document.getElementById('aporte_laboral').value = aporteLaboralCalculado.toFixed(2);
    document.getElementById('aporte_nacional_solidario').value = ansCalculado.toFixed(2);
    
    // B. RC-IVA (Reutilizamos la función RC-IVA de nomina.js)
    const resultadoRCIVA = calcularRCIVA(totalGanado, smn, saldoAnterior, f110Monto);
    
    // Escribir resultados RC-IVA (Outputs)
    document.getElementById('rc_iva').value = resultadoRCIVA.rc_iva_pagar.toFixed(2);
    document.getElementById('rc_iva_saldo_siguiente').value = resultadoRCIVA.rc_iva_saldo_siguiente.toFixed(2);
    const rcIvaPagar = resultadoRCIVA.rc_iva_pagar;


    // --- 3. CÁLCULO FINAL DE LÍQUIDO ---
    
    const totalDescuentos = aporteLaboralCalculado + ansCalculado + rcIvaPagar + anticipos;
    const liquido = totalGanado - totalDescuentos;

    // --- 4. ACTUALIZAR PANTALLA Y ESTILOS ---
    document.getElementById('total_descuentos').value = totalDescuentos.toFixed(2);
    document.getElementById('liquido').value = liquido.toFixed(2);

    const liquidoDisplay = document.getElementById('liquido');
    if (liquido < 0) {
        liquidoDisplay.style.color = '#dc3545';
    } else {
        liquidoDisplay.style.color = '#28a745';
    }
}


function calcularAutomaticosEdit() {
    // Este botón ahora solo llama al cálculo principal, forzando todos los outputs
    calcularTotalesEdit();
}

function limpiarCalculosEdit() {
    // Limpiar campos calculados automáticamente (Bono, AFP, ANS, RC-IVA, Extra)
    document.getElementById('bono_antiguedad').value = '0.00';
    document.getElementById('aporte_laboral').value = '0.00';
    document.getElementById('aporte_nacional_solidario').value = '0.00';
    document.getElementById('rc_iva').value = '0.00';
    document.getElementById('rc_iva_saldo_siguiente').value = '0.00';
    document.getElementById('trabajo_extraordinario').value = '0.00';
    
    // Limpiar campos INPUT que afectan el RC-IVA/Cálculo
    document.getElementById('rc_iva_f110_monto').value = '0.00';
    document.getElementById('rc_iva_saldo_anterior').value = '0.00';
    document.getElementById('horas_extras').value = '0';
    document.getElementById('pago_domingo').value = '0.00';
    document.getElementById('otros_bonos').value = '0.00';
    document.getElementById('anticipos').value = '0.00';


    // Recalcular para que los outputs se pongan a cero y los totales se actualicen.
    calcularTotalesEdit();
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar las validaciones de decimales y enteros si están disponibles
    // Estas funciones son cargadas por nomina.js
    if (typeof setupDecimalValidation === 'function') {
        setupDecimalValidation();
    }
    if (typeof setupIntegerValidation === 'function') {
        setupIntegerValidation();
    }

    // Calcular totales iniciales
    calcularTotalesEdit();

    // Configurar evento para cuando cambie el empleado (para recalcular antigüedad)
    document.getElementById('empleado_id').addEventListener('change', calcularTotalesEdit);
});
</script>

@endpush
@endsection