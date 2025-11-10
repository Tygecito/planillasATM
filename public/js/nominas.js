// ==============================================
// FUNCIÓN PARA APLICAR SMN GENERAL
// ==============================================

/**
 * Aplica el valor del SMN General (Salario Mínimo Nacional) 
 * a todas las filas de empleados y recalcula sus totales.
 */
function aplicarSMNGeneral() {
    // 1. Obtener el input del SMN General
    const smnGeneralInput = document.getElementById('smn_comun');
    let smnValor = smnGeneralInput.value;

    // 2. Formatear el valor (similar a la validación 'onblur')
    const num = parseFloat(smnValor);
    if (!isNaN(num)) {
        smnValor = num.toFixed(2);
    } else {
        smnValor = '0.00';
    }
    
    // Actualiza el campo general para que muestre el formato 0.00
    smnGeneralInput.value = smnValor;

    // 3. Encontrar todos los inputs de SMN individuales en la tabla
    const smnInputsIndividuales = document.querySelectorAll('.smn-input');

    // 4. Recorrer cada input, asignarle el valor y recalcular la fila
    smnInputsIndividuales.forEach(input => {
        input.value = smnValor;
        calcularTotal(input); 
    });
}

// ==============================================
// CONSTANTES Y CONFIGURACIÓN
// ==============================================

const PORCENTAJE_APORTE_LABORAL = 12.71; // 12.71% para AFP
const UMBRALES_ANS = {
    umbral1: 13000,
    umbral2: 25000,
    umbral3: 35000
};
const PORCENTAJES_ANS = {
    tramo1: 1.15,
    tramo2: 5.74,
    tramo3: 11.48
};

// Tabla de porcentajes de bono de antigüedad
const PORCENTAJES_ANTIGUEDAD = [
    {min: 2, max: 4, porcentaje: 5},
    {min: 5, max: 7, porcentaje: 11},
    {min: 8, max: 10, porcentaje: 18},
    {min: 11, max: 14, porcentaje: 26},
    {min: 15, max: 19, porcentaje: 34},
    {min: 20, max: 24, porcentaje: 42},
    {min: 25, max: 999, porcentaje: 50}
];

// ==============================================
// FUNCIONES DE CÁLCULO AUTOMÁTICO
// ==============================================

function calcularAntiguedad(fechaIngreso, smn) {
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

function calcularAporteLaboral(totalGanado) {
    return (parseFloat(totalGanado) * PORCENTAJE_APORTE_LABORAL) / 100;
}

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
// FUNCIÓN PRINCIPAL DE CÁLCULO (ACTUALIZADA)
// ==============================================

function calcularTotal(input) {
    const row = input.closest('tr');
    
    // --- 1. OBTENER VALORES GLOBALES ---
    // Usamos el valor general de "Días Pagados"
    const diasPagadosInput = document.getElementById('dias_pagados');
    const diasPagados = parseInt(diasPagadosInput.value) || 30; // 30 por defecto si está vacío

    // --- 2. OBTENER VALORES DE LA FILA ---
    const smnInput = row.querySelector('.smn-input');
    const haberBasicoInput = row.querySelector('.haber-basico-input');
    const horasExtrasInput = row.querySelector('[name*="[horas_extras]"]'); // Campo de cantidad
    
    const smn = parseFloat(smnInput.value) || 0;
    const haberBasico = parseFloat(haberBasicoInput.value) || 0;
    const horasExtras = parseInt(horasExtrasInput.value) || 0;

    // --- 3. VALIDACIÓN SMN vs HABER BÁSICO ---
    if (smn > 0 && haberBasico > 0 && haberBasico < smn) {
        haberBasicoInput.classList.add('is-invalid');
        haberBasicoInput.title = `El haber básico (${haberBasico}) no puede ser menor al SMN (${smn}).`;
    } else {
        haberBasicoInput.classList.remove('is-invalid');
        haberBasicoInput.title = '';
    }

    // --- 4. OBTENER INPUTS DE RESULTADOS ---
    const bonoAntiguedadInput = row.querySelector('.bono-antiguedad-input');
    const aporteLaboralInput = row.querySelector('.aporte-laboral-input');
    const ansInput = row.querySelector('.ans-input');
    const trabajoExtraordinarioInput = row.querySelector('[name*="[trabajo_extraordinario]"]'); // Campo de monto (Bs)
    
    let totalGanado = 0;
    let totalDescuentos = 0;
    
    // --- 5. CALCULAR INGRESOS (TOTAL GANADO) ---
    
    // 5a. Calcular Bono Antigüedad
    let bonoAntiguedadCalculado = 0;
    const empleadoInfo = row.querySelector('.empleado-info');
    const fechaIngreso = empleadoInfo.getAttribute('data-fecha-ingreso');

    if (fechaIngreso && smn > 0) {
        bonoAntiguedadCalculado = calcularAntiguedad(fechaIngreso, smn);
        bonoAntiguedadInput.value = bonoAntiguedadCalculado.toFixed(2);
    } else {
        bonoAntiguedadCalculado = (parseFloat(bonoAntiguedadInput.value) || 0);
    }

    // 5b. Calcular Horas Extras (¡NUEVO!)
    let pagoExtraCalculado = 0;
    if (haberBasico > 0 && diasPagados > 0 && horasExtras > 0) {
        const jornalDiario = haberBasico / diasPagados;
        const valorHoraNormal = jornalDiario / 8; // Basado en 8 horas
        const valorHoraExtra = valorHoraNormal * 2; // Pago doble
        pagoExtraCalculado = valorHoraExtra * horasExtras;
    }
    // Escribir el resultado en el campo readonly
    trabajoExtraordinarioInput.value = pagoExtraCalculado.toFixed(2);

    
    // 5c. Sumar Total Ganado
    totalGanado = haberBasico + 
                  bonoAntiguedadCalculado +
                  pagoExtraCalculado + // <-- Se usa el valor calculado
                  (parseFloat(row.querySelector('[name*="[pago_domingo]"]').value) || 0) + 
                  (parseFloat(row.querySelector('[name*="[otros_bonos]"]').value) || 0);
    
    const totalGanadoDisplay = row.querySelector('.total-ganado-display');
    if (totalGanadoDisplay) {
        totalGanadoDisplay.value = totalGanado.toFixed(2);
    }

    // --- 6. CALCULAR DESCUENTOS ---
    
    const aporteLaboralCalculado = calcularAporteLaboral(totalGanado);
    aporteLaboralInput.value = aporteLaboralCalculado.toFixed(2);
    
    const ansCalculado = calcularANS(totalGanado);
    ansInput.value = ansCalculado.toFixed(2);
    
    totalDescuentos = aporteLaboralCalculado + 
                      ansCalculado + 
                      (parseFloat(row.querySelector('[name*="[rc_iva]"]').value) || 0) + 
                      (parseFloat(row.querySelector('[name*="[anticipos]"]').value) || 0);

    const totalDescuentoDisplay = row.querySelector('.total-descuento-display');
    if (totalDescuentoDisplay) {
        totalDescuentoDisplay.value = totalDescuentos.toFixed(2);
    }

    // --- 7. CÁLCULO FINAL (Líquido Pagable) ---
    const total = totalGanado - totalDescuentos;
    const totalElement = row.querySelector('.total-value');
    totalElement.textContent = total.toFixed(2);
    
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
            calcularTotal(this); // Esto disparará el recálculo
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
    // AÑADIMOS 'dias_pagados' a la lista de campos que disparan el recálculo
    const commonFields = ['dias_pagados', 'horas_pagadas']; 
    
    commonFields.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            input.addEventListener('change', function() {
                // (No necesitamos copiar estos valores a las filas, pero sí recalcular)
                
                // Recalcular todos los totales
                document.querySelectorAll('.smn-input').forEach(inputFila => {
                    if (inputFila.closest('tr')) {
                        calcularTotal(inputFila); // Recalcula cada fila
                    }
                });
            });
        }
    });
}

// ==============================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// ==============================================

document.addEventListener('DOMContentLoaded', function() {
    setupIntegerValidation();
    setupDecimalValidation();
    setupFormSubmission();
    setupCommonFieldsCopy();
    
    // Calcular totales iniciales para todas las filas
    document.querySelectorAll('.smn-input').forEach(input => {
        calcularTotal(input);
    });
});