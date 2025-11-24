// ==============================================
// FUNCIÓN PARA APLICAR SMN GENERAL Y RC-IVA GENERAL
// ==============================================

const ALICUOTA_IVA = 0.13; // 13% para RC-IVA
const CREDITOS_SMN_FACTOR = 1; // 1 SMN para Crédito Fijo (D.S. 5383)

/**
 * Función central para calcular RC-IVA, Saldo Anterior y Saldo Siguiente.
 */
function calcularRCIVA(totalGanado, smn, saldoAnterior, f110Monto) {
    const tg = parseFloat(totalGanado) || 0;
    const smnVal = parseFloat(smn) || 0;
    const saldoAnt = parseFloat(saldoAnterior) || 0;
    const f110 = parseFloat(f110Monto) || 0;
    
    let rcIvaPagar = 0;
    let saldoSiguiente = 0;

    // 1. CÁLCULO DEL SUELDO NETO (Base Imponible)
    const aporteLaboral = calcularAporteLaboral(tg);
    const ans = calcularANS(tg);
    const totalDescuentosLey = aporteLaboral + ans;
    const sueldoNeto = tg - totalDescuentosLey;

    // 2. DETERMINAR BASE GRAVADA (Restar 2 SMN)
    const minNoImponible = 2 * smnVal;
    let baseGravada = sueldoNeto - minNoImponible;

    // LÓGICA DE COMPENSACIÓN
    if (baseGravada > 0) {
        const impuestoBruto = baseGravada * ALICUOTA_IVA;
        const creditoFijo = (CREDITOS_SMN_FACTOR * smnVal) * ALICUOTA_IVA;
        const creditoF110 = f110 * ALICUOTA_IVA;
        const totalCreditos = creditoFijo + creditoF110;
        
        let impuestoNeto = impuestoBruto - totalCreditos;

        if (impuestoNeto > 0) {
            if (saldoAnt >= impuestoNeto) {
                saldoSiguiente = saldoAnt - impuestoNeto;
                rcIvaPagar = 0;
            } else {
                rcIvaPagar = impuestoNeto - saldoAnt;
                saldoSiguiente = 0;
            }
        } else {
            saldoSiguiente = saldoAnt + Math.abs(impuestoNeto);
            rcIvaPagar = 0;
        }
    } else {
        saldoSiguiente = saldoAnt + Math.abs(baseGravada) * ALICUOTA_IVA;
        rcIvaPagar = 0;
    }
    
    return {
        rc_iva_pagar: Math.max(0, rcIvaPagar),
        rc_iva_saldo_siguiente: Math.max(0, saldoSiguiente)
    };
}

/**
 * Aplica el SMN General y dispara el recálculo en todas las filas.
 */
function aplicarSMNGeneral() {
    const smnGeneralInput = document.getElementById('smn_comun');
    
    let smnValor = parseFloat(smnGeneralInput.value) || 0;
    smnGeneralInput.value = smnValor.toFixed(2);

    const tabla = document.querySelector('.nominas-table');
    
    // Aplicar SMN y Recalcular toda la tabla
    tabla.querySelectorAll('.smn-input').forEach(input => {
        input.value = smnValor.toFixed(2);
        calcularTotal(input); 
    });
}

// ==============================================
// CONSTANTES Y CÁLCULOS BASE (EXISTENTES)
// ==============================================

const PORCENTAJES_ANTIGUEDAD = [
    {min: 2, max: 4, porcentaje: 5}, {min: 5, max: 7, porcentaje: 11},
    {min: 8, max: 10, porcentaje: 18}, {min: 11, max: 14, porcentaje: 26},
    {min: 15, max: 19, porcentaje: 34}, {min: 20, max: 24, porcentaje: 42},
    {min: 25, max: 999, porcentaje: 50}
];

function calcularAntiguedad(fechaIngreso, smn) { /* ... (Lógica de Antigüedad) ... */
    const hoy = new Date();
    const ingreso = new Date(fechaIngreso);
    
    let añosAntiguedad = hoy.getFullYear() - ingreso.getFullYear();
    const mesHoy = hoy.getMonth();
    const mesIngreso = ingreso.getMonth();
    
    if (mesHoy < mesIngreso || (mesHoy === mesIngreso && hoy.getDate() < ingreso.getDate())) {
        añosAntiguedad--;
    }
    
    if (añosAntiguedad < 2) return 0;
    
    const porcentaje = PORCENTAJES_ANTIGUEDAD.find(rango => 
        añosAntiguedad >= rango.min && añosAntiguedad <= rango.max
    );
    
    if (!porcentaje) return 0;
    
    const baseCalculo = parseFloat(smn) * 3;
    return (baseCalculo * porcentaje.porcentaje) / 100;
}

const PORCENTAJE_APORTE_LABORAL = 12.71;
function calcularAporteLaboral(totalGanado) {
    return (parseFloat(totalGanado) * PORCENTAJE_APORTE_LABORAL) / 100;
}

const UMBRALES_ANS = {umbral1: 13000, umbral2: 25000, umbral3: 35000};
const PORCENTAJES_ANS = {tramo1: 1.15, tramo2: 5.74, tramo3: 11.48};
function calcularANS(totalGanado) {
    const tg = parseFloat(totalGanado);
    let ansTotal = 0;

    if (tg > UMBRALES_ANS.umbral3) {
        ansTotal += (tg - UMBRALES_ANS.umbral3) * (PORCENTAJES_ANS.tramo3 / 100);
        ansTotal += (UMBRALES_ANS.umbral3 - UMBRALES_ANS.umbral2) * (PORCENTAJES_ANS.tramo2 / 100);
        ansTotal += (UMBRALES_ANS.umbral2 - UMBRALES_ANS.umbral1) * (PORCENTAJES_ANS.tramo1 / 100);
    } else if (tg > UMBRALES_ANS.umbral2) {
        ansTotal += (tg - UMBRALES_ANS.umbral2) * (PORCENTAJES_ANS.tramo2 / 100);
        ansTotal += (UMBRALES_ANS.umbral2 - UMBRALES_ANS.umbral1) * (PORCENTAJES_ANS.tramo1 / 100);
    } else if (tg > UMBRALES_ANS.umbral1) {
        ansTotal += (tg - UMBRALES_ANS.umbral1) * (PORCENTAJES_ANS.tramo1 / 100);
    }
    
    return ansTotal;
}


// ==============================================
// FUNCIÓN PRINCIPAL DE CÁLCULO (INCLUYE TODA LA LÓGICA)
// ==============================================

function calcularTotal(input) {
    const row = input.closest('tr');
    
    // --- 1. OBTENER VALORES GLOBALES Y LOCALES ---
    const diasPagadosInput = document.getElementById('dias_pagados');
    const diasPagados = parseInt(diasPagadosInput.value) || 30;

    const smnInput = row.querySelector('.smn-input');
    const haberBasicoInput = row.querySelector('.haber-basico-input');
    const horasExtrasInput = row.querySelector('[name*="[horas_extras]"]');
    
    // Inputs RC-IVA (LEER CAMPOS DE LA FILA)
    const rcIvaF110MontoInput = row.querySelector('.rc-iva-f110-monto-input');
    const rcIvaSaldoAnteriorInput = row.querySelector('.rc-iva-saldo-anterior-input');

    const smn = parseFloat(smnInput.value) || 0;
    const haberBasico = parseFloat(haberBasicoInput.value) || 0;
    const horasExtras = parseInt(horasExtrasInput.value) || 0;
    const f110Monto = parseFloat(rcIvaF110MontoInput.value) || 0;
    const saldoAnterior = parseFloat(rcIvaSaldoAnteriorInput.value) || 0;

    // --- 2. VALIDACIÓN SMN vs HABER BÁSICO (EXISTENTE) ---
    if (smn > 0 && haberBasico > 0 && haberBasico < smn) {
        haberBasicoInput.classList.add('is-invalid');
        haberBasicoInput.title = `El haber básico (${haberBasico}) no puede ser menor al SMN (${smn}).`;
    } else {
        haberBasicoInput.classList.remove('is-invalid');
        haberBasicoInput.title = '';
    }

    // --- 3. OBTENER INPUTS DE RESULTADOS Y VALORES MANUALES RESTANTES ---
    const bonoAntiguedadInput = row.querySelector('.bono-antiguedad-input');
    const aporteLaboralInput = row.querySelector('.aporte-laboral-input');
    const ansInput = row.querySelector('.ans-input');
    const trabajoExtraordinarioInput = row.querySelector('[name*="[trabajo_extraordinario]"]');
    const rcIvaInput = row.querySelector('.rc-iva-input');
    const rcIvaSaldoSiguienteInput = row.querySelector('.rc-iva-saldo-siguiente-input');
    
    // Obtenemos los valores de bonos MANUALES restantes
    const pagoDomingo = parseFloat(row.querySelector('[name*="[pago_domingo]"]').value) || 0;
    const otrosBonos = parseFloat(row.querySelector('[name*="[otros_bonos]"]').value) || 0;
    const anticipos = parseFloat(row.querySelector('[name*="[anticipos]"]').value) || 0;
    
    let totalGanado = 0;
    let totalDescuentos = 0;
    
    // --- 4. CÁLCULO DE INGRESOS ---
    
    // 4a. Calcular Bono Antigüedad (Fuerza el cálculo)
    let bonoAntiguedadCalculado = 0;
    const empleadoInfo = row.querySelector('.empleado-info');
    const fechaIngreso = empleadoInfo.getAttribute('data-fecha-ingreso');

    if (fechaIngreso && smn > 0) {
        bonoAntiguedadCalculado = calcularAntiguedad(fechaIngreso, smn);
        bonoAntiguedadInput.value = bonoAntiguedadCalculado.toFixed(2);
    } else {
        bonoAntiguedadCalculado = 0;
        bonoAntiguedadInput.value = '0.00';
    }

    // 4b. Calcular Horas Extras (Cálculo Contable)
    let pagoExtraCalculado = 0;
    if (haberBasico > 0 && diasPagados > 0 && horasExtras > 0) {
        const jornalDiario = haberBasico / diasPagados;
        const valorHoraNormal = jornalDiario / 8;
        const valorHoraExtra = valorHoraNormal * 2;
        pagoExtraCalculado = valorHoraExtra * horasExtras;
    }
    // Escribir el resultado en el campo Trabajo Extra (Bs)
    trabajoExtraordinarioInput.value = pagoExtraCalculado.toFixed(2); // <--- HACE EL CÁLCULO AUTOMÁTICO

    
    // 4c. Sumar Total Ganado
    totalGanado = haberBasico + 
                  bonoAntiguedadCalculado +
                  pagoExtraCalculado + // <--- Usa el valor CALCULADO de Horas Extra
                  pagoDomingo + 
                  otrosBonos;
    
    const totalGanadoDisplay = row.querySelector('.total-ganado-display');
    if (totalGanadoDisplay) {
        totalGanadoDisplay.value = totalGanado.toFixed(2);
    }

    // --- 5. CÁLCULO DE RC-IVA Y APORTES DE LEY ---
    
    const aporteLaboralCalculado = calcularAporteLaboral(totalGanado);
    aporteLaboralInput.value = aporteLaboralCalculado.toFixed(2);
    
    const ansCalculado = calcularANS(totalGanado);
    ansInput.value = ansCalculado.toFixed(2);
    
    // Calcular RC-IVA completo
    const resultadoRCIVA = calcularRCIVA(totalGanado, smn, saldoAnterior, f110Monto);
    
    // Escribir resultados RC-IVA (Output Fields)
    rcIvaInput.value = resultadoRCIVA.rc_iva_pagar.toFixed(2);
    rcIvaSaldoSiguienteInput.value = resultadoRCIVA.rc_iva_saldo_siguiente.toFixed(2);
    
    // --- 6. CÁLCULO FINAL DE DESCUENTOS Y LÍQUIDO ---
    
    // Sumamos los descuentos finales (RC-IVA a pagar, AFP, ANS y Anticipos)
    totalDescuentos = aporteLaboralCalculado + 
                      ansCalculado + 
                      resultadoRCIVA.rc_iva_pagar + 
                      anticipos;

    const totalDescuentoDisplay = row.querySelector('.total-descuento-display');
    if (totalDescuentoDisplay) {
        totalDescuentoDisplay.value = totalDescuentos.toFixed(2);
    }

    // Cálculo del líquido: Total Ganado - Total Descuentos
    const total = totalGanado - totalDescuentos;
    const totalElement = row.querySelector('.total-value');
    totalElement.textContent = total.toFixed(2); // <--- Líquido Pagable Calculado
    
    if (total < 0) {
        totalElement.style.color = '#dc3545';
    } else {
        totalElement.style.color = '#28a745';
    }
}


// ==============================================
// FUNCIONES DE VALIDACIÓN DE ENTRADA EN CLIENTE
// ==============================================

function setupDecimalValidation() {
    document.querySelectorAll('.decimal-input:not(.integer-input)').forEach(input => {
        
        // Excluimos campos de salida (readOnly)
        if (input.readOnly) return; 

        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');
            
            if (this.value.includes('.')) {
                const parts = this.value.split('.');
                if (parts.length > 2) {
                    this.value = parts[0] + '.' + parts[1];
                }
                if (parts[1] && parts[1].length > 2) {
                    this.value = parts[0] + '.' + parts[1].substring(0, 2);
                }
            }
            
            if (!this.classList.contains('total-ganado-display') && !this.classList.contains('total-descuento-display')) {
                calcularTotal(this);
            }
        });
        
        input.addEventListener('blur', function() {
            if (this.value && this.value !== '0' && this.value !== '.') {
                const num = parseFloat(this.value);
                if (!isNaN(num)) {
                    this.value = num.toFixed(2);
                } else {
                    this.value = '0.00';
                }
            } else if (this.value === '' || this.value === '0' || this.value === '.') {
                this.value = '0.00'; 
            }
            
            if (!this.classList.contains('total-ganado-display') && !this.classList.contains('total-descuento-display')) {
                 calcularTotal(this);
            }
        });
    });
}

function setupIntegerValidation() {
    document.querySelectorAll('.integer-input').forEach(input => {
        
        input.addEventListener('keypress', function(event) {
            const charCode = event.charCode;
            if (charCode === 46 || charCode < 48 || charCode > 57) {
                event.preventDefault();
            }
        });

        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            calcularTotal(this);
        });
        
        input.addEventListener('blur', function() {
            if (this.value.includes('.')) {
                this.value = parseInt(this.value, 10).toString();
            }
            if (this.value === '' || this.value === '0') {
                this.value = '0'; 
            }
        });
    });
}

// ==============================================
// CONFIGURACIÓN DE ENVÍO DEL FORMULARIO
// ==============================================

function setupFormSubmission() {
    const form = document.getElementById('nominasForm');
    if (form) {
        form.addEventListener('submit', function() {
            this.querySelectorAll('.decimal-input').forEach(input => {
                if (input.value === '') {
                    input.value = '0.00';
                }
            });
            
            this.querySelectorAll('.integer-input').forEach(input => {
                if (input.value === '') {
                    input.value = '0';
                }
            });
        });
    }
}

// ==============================================
// FUNCIONALIDAD DE COPIADO DE VALORES COMUNES
// ==============================================

function setupCommonFieldsCopy() {
    // RC-IVA F110 y Saldo Ant NO son globales, deben copiarse individualmente
    const fieldsToCopy = ['smn_comun']; 
    const generalFields = ['dias_pagados', 'horas_pagadas']; 
    
    // Configurar el copiado del SMN y recálculo
    fieldsToCopy.forEach(fieldId => {
        const input = document.getElementById(fieldId);
        if (input) {
            input.addEventListener('change', aplicarSMNGeneral);
        }
    });

    // Configurar los campos generales que solo disparan recálculo 
    generalFields.forEach(fieldId => {
        const input = document.getElementById(fieldId);
        if (input) {
            input.addEventListener('change', function() {
                document.querySelectorAll('.smn-input').forEach(inputFila => {
                    if (inputFila.closest('tr')) {
                        calcularTotal(inputFila);
                    }
                });
            });
        }
    });
}

// ==============================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA (EXISTENTE)
// ==============================================

document.addEventListener('DOMContentLoaded', function() {
    setupIntegerValidation();
    setupDecimalValidation();
    setupFormSubmission();
    setupCommonFieldsCopy();
    
    document.querySelectorAll('.smn-input').forEach(input => {
        calcularTotal(input);
    });
});