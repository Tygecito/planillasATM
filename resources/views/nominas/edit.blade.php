@extends('layouts.app')

@section('title', 'Editar Nómina - Mi App')

@section('content')
<h1 class="welcome-message">Editar Nómina #{{ $nomina->id }}</h1>

<div class="card p-4">
    <form action="{{ route('nominas.update', $nomina->id) }}" method="POST" id="editNominaForm">
        @csrf
        @method('PUT')

        <!-- Fila 1: Empleado, Mes, Año -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="empleado_id">Empleado</label>
                <select name="empleado_id" class="form-control" required id="empleado_id">
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->id }}" 
                                {{ $nomina->empleado_id == $empleado->id ? 'selected' : '' }}
                                data-fecha-ingreso="{{ $empleado->fecha_ingreso }}">
                            {{ $empleado->nombres }} {{ $empleado->primerapellido }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="mes">Mes</label>
                <select name="mes" class="form-control" required id="mes">
                    @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $mes)
                        <option value="{{ $mes }}" {{ $nomina->mes == $mes ? 'selected' : '' }}>{{ $mes }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="anio">Año</label>
                <input type="number" name="anio" class="form-control" value="{{ $nomina->anio }}" required id="anio">
            </div>
        </div>

        <!-- Fila 2: SMN, Horas Pagadas, Días Pagados -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="smn">Salario Mínimo Nacional (SMN)</label>
                <input type="number" step="0.01" name="smn" class="form-control smn-input decimal-input" 
                       value="{{ $nomina->smn }}" placeholder="0.00" id="smn" oninput="calcularTotalesEdit()">
            </div>
            <div class="col-md-4">
                <label for="horas_pagadas">Horas Pagadas</label>
                <input type="number" name="horas_pagadas" class="form-control no-spinner" 
                       value="{{ $nomina->horas_pagadas }}" required id="horas_pagadas">
            </div>
            <div class="col-md-4">
                <label for="dias_pagados">Días Pagados</label>
                <input type="number" name="dias_pagados" class="form-control no-spinner" 
                       value="{{ $nomina->dias_pagados }}" required id="dias_pagados">
            </div>
        </div>

        <!-- Fila 3: Haber Básico, Horas Extras -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="haber_basico">Haber Básico *</label>
                <input type="number" step="0.01" name="haber_basico" class="form-control decimal-input" 
                       value="{{ $nomina->haber_basico }}" required id="haber_basico" oninput="calcularTotalesEdit()">
            </div>
            <div class="col-md-6">
                <label for="horas_extras">Horas Extras</label>
                <input type="number" name="horas_extras" class="form-control no-spinner" 
                       value="{{ $nomina->horas_extras }}" id="horas_extras">
            </div>
        </div>

        <!-- Fila 4: Bonificaciones -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="bono_antiguedad">Bono Antigüedad</label>
                <input type="number" step="0.01" name="bono_antiguedad" class="form-control bono-antiguedad-input decimal-input" 
                       value="{{ $nomina->bono_antiguedad }}" id="bono_antiguedad" oninput="calcularTotalesEdit()">
            </div>
            <div class="col-md-3">
                <label for="trabajo_extraordinario">Trabajo Extraordinario</label>
                <input type="number" step="0.01" name="trabajo_extraordinario" class="form-control decimal-input" 
                       value="{{ $nomina->trabajo_extraordinario }}" id="trabajo_extraordinario" oninput="calcularTotalesEdit()">
            </div>
            <div class="col-md-3">
                <label for="pago_domingo">Pago Domingo</label>
                <input type="number" step="0.01" name="pago_domingo" class="form-control decimal-input" 
                       value="{{ $nomina->pago_domingo }}" id="pago_domingo" oninput="calcularTotalesEdit()">
            </div>
            <div class="col-md-3">
                <label for="otros_bonos">Otros Bonos</label>
                <input type="number" step="0.01" name="otros_bonos" class="form-control decimal-input" 
                       value="{{ $nomina->otros_bonos }}" id="otros_bonos" oninput="calcularTotalesEdit()">
            </div>
        </div>

        <!-- Fila 5: Descuentos -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="aporte_laboral">Aporte Laboral</label>
                <input type="number" step="0.01" name="aporte_laboral" class="form-control aporte-laboral-input decimal-input" 
                       value="{{ $nomina->aporte_laboral }}" id="aporte_laboral" oninput="calcularTotalesEdit()">
            </div>
            <div class="col-md-3">
                <label for="aporte_nacional_solidario">Aporte Nacional Solidario</label>
                <input type="number" step="0.01" name="aporte_nacional_solidario" class="form-control ans-input decimal-input" 
                       value="{{ $nomina->aporte_nacional_solidario }}" id="aporte_nacional_solidario" oninput="calcularTotalesEdit()">
            </div>
            <div class="col-md-3">
                <label for="rc_iva">RC IVA</label>
                <input type="number" step="0.01" name="rc_iva" class="form-control decimal-input" 
                       value="{{ $nomina->rc_iva }}" id="rc_iva" oninput="calcularTotalesEdit()">
            </div>
            <div class="col-md-3">
                <label for="anticipos">Anticipos</label>
                <input type="number" step="0.01" name="anticipos" class="form-control decimal-input" 
                       value="{{ $nomina->anticipos }}" id="anticipos" oninput="calcularTotalesEdit()">
            </div>
        </div>

        <!-- Fila 6: Totales (solo lectura) -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="total_ganado">Total Ganado</label>
                <input type="number" step="0.01" name="total_ganado" class="form-control" 
                       value="{{ $nomina->total_ganado }}" readonly style="background-color: #e9ecef;" id="total_ganado">
            </div>
            <div class="col-md-4">
                <label for="total_descuentos">Total Descuentos</label>
                <input type="number" step="0.01" name="total_descuentos" class="form-control" 
                       value="{{ $nomina->total_descuentos }}" readonly style="background-color: #e9ecef;" id="total_descuentos">
            </div>
            <div class="col-md-4">
                <label for="liquido">Líquido Pagable</label>
                <input type="number" step="0.01" name="liquido" class="form-control" 
                       value="{{ $nomina->liquido }}" readonly style="background-color: #e9ecef;" id="liquido">
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="row mt-4">
            <div class="col-md-6">
                <button type="button" class="btn btn-info" onclick="calcularAutomaticosEdit()">
                    <i class="fas fa-calculator"></i> Calcular Automáticos
                </button>
                <button type="button" class="btn btn-warning" onclick="limpiarCalculosEdit()">
                    <i class="fas fa-eraser"></i> Limpiar Cálculos
                </button>
            </div>
            <div class="col-md-6 text-end">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar Nómina</button>
                <a href="{{ route('nominas.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    // ==============================================
    // FUNCIONES ESPECÍFICAS PARA EDITAR NÓMINA
    // ==============================================

    function calcularTotalesEdit() {
        // Obtener valores de los inputs
        const haberBasico = parseFloat(document.getElementById('haber_basico').value) || 0;
        const bonoAntiguedad = parseFloat(document.getElementById('bono_antiguedad').value) || 0;
        const trabajoExtraordinario = parseFloat(document.getElementById('trabajo_extraordinario').value) || 0;
        const pagoDomingo = parseFloat(document.getElementById('pago_domingo').value) || 0;
        const otrosBonos = parseFloat(document.getElementById('otros_bonos').value) || 0;
        
        const aporteLaboral = parseFloat(document.getElementById('aporte_laboral').value) || 0;
        const aporteSolidario = parseFloat(document.getElementById('aporte_nacional_solidario').value) || 0;
        const rcIva = parseFloat(document.getElementById('rc_iva').value) || 0;
        const anticipos = parseFloat(document.getElementById('anticipos').value) || 0;

        // Calcular total ganado (usando haber_basico directamente)
        const totalGanado = haberBasico + bonoAntiguedad + trabajoExtraordinario + pagoDomingo + otrosBonos;
        
        // Calcular total descuentos
        const totalDescuentos = aporteLaboral + aporteSolidario + rcIva + anticipos;
        
        // Calcular líquido
        const liquido = totalGanado - totalDescuentos;

        // Actualizar los campos de totales
        document.getElementById('total_ganado').value = totalGanado.toFixed(2);
        document.getElementById('total_descuentos').value = totalDescuentos.toFixed(2);
        document.getElementById('liquido').value = liquido.toFixed(2);
    }

    function calcularAutomaticosEdit() {
        const smn = parseFloat(document.getElementById('smn').value) || 0;
        const empleadoSelect = document.getElementById('empleado_id');
        const fechaIngreso = empleadoSelect.options[empleadoSelect.selectedIndex].getAttribute('data-fecha-ingreso');
        
        // Calcular bono de antigüedad automáticamente
        if (fechaIngreso && smn > 0) {
            const bonoAntiguedad = calcularAntiguedad(fechaIngreso, smn);
            document.getElementById('bono_antiguedad').value = bonoAntiguedad.toFixed(2);
        }
        
        // Calcular aporte laboral automáticamente
        const haberBasico = parseFloat(document.getElementById('haber_basico').value) || 0;
        const bonoAntiguedadActual = parseFloat(document.getElementById('bono_antiguedad').value) || 0;
        const trabajoExtraordinario = parseFloat(document.getElementById('trabajo_extraordinario').value) || 0;
        const pagoDomingo = parseFloat(document.getElementById('pago_domingo').value) || 0;
        const otrosBonos = parseFloat(document.getElementById('otros_bonos').value) || 0;
        
        const totalGanadoParcial = haberBasico + bonoAntiguedadActual + trabajoExtraordinario + pagoDomingo + otrosBonos;
        const aporteLaboral = calcularAporteLaboral(totalGanadoParcial);
        document.getElementById('aporte_laboral').value = aporteLaboral.toFixed(2);
        
        // Calcular ANS automáticamente
        const ans = calcularANS(totalGanadoParcial);
        document.getElementById('aporte_nacional_solidario').value = ans.toFixed(2);
        
        // Recalcular totales
        calcularTotalesEdit();
    }

    function limpiarCalculosEdit() {
        // Limpiar campos calculados automáticamente
        document.getElementById('bono_antiguedad').value = '';
        document.getElementById('aporte_laboral').value = '';
        document.getElementById('aporte_nacional_solidario').value = '';
        
        // Recalcular totales
        calcularTotalesEdit();
    }

    // ==============================================
    // INICIALIZACIÓN AL CARGAR LA PÁGINA
    // ==============================================

    document.addEventListener('DOMContentLoaded', function() {
        // Configurar validaciones para inputs decimales
        setupDecimalValidation();
        setupNumberValidation();
        
        // Calcular totales iniciales
        calcularTotalesEdit();
        
        // Configurar evento para cuando cambie el empleado
        document.getElementById('empleado_id').addEventListener('change', function() {
            // Si hay SMN, recalcular bono de antigüedad
            const smn = parseFloat(document.getElementById('smn').value) || 0;
            if (smn > 0) {
                calcularAutomaticosEdit();
            }
        });
    });
</script>
@endpush