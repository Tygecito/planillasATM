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
    const añosAntiguedad = hoy.getFullYear() - ingreso.getFullYear();
    
    if (añosAntiguedad < 2) return 0;
    
    const porcentaje = PORCENTAJES_ANTIGUEDAD.find(rango => 
        añosAntiguedad >= rango.min && añosAntiguedad <= rango.max
    );
    
    if (!porcentaje) return 0;
    
    // Bono = (SMN * 3) * (porcentaje / 100)
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
        // Aplica los 3 tramos
        ansTotal += ((tg - UMBRALES_ANS.umbral1) * PORCENTAJES_ANS.tramo1) / 100;
        ansTotal += ((tg - UMBRALES_ANS.umbral2) * PORCENTAJES_ANS.tramo2) / 100;
        ansTotal += ((tg - UMBRALES_ANS.umbral3) * PORCENTAJES_ANS.tramo3) / 100;
    } else if (tg > UMBRALES_ANS.umbral2) {
        // Aplica tramo 1 y 2
        ansTotal += ((tg - UMBRALES_ANS.umbral1) * PORCENTAJES_ANS.tramo1) / 100;
        ansTotal += ((tg - UMBRALES_ANS.umbral2) * PORCENTAJES_ANS.tramo2) / 100;
    } else if (tg > UMBRALES_ANS.umbral1) {
        // Aplica solo tramo 1
        ansTotal += ((tg - UMBRALES_ANS.umbral1) * PORCENTAJES_ANS.tramo1) / 100;
    }
    
    return ansTotal;
}

// ==============================================
// FUNCIÓN PRINCIPAL DE CÁLCULO
// ==============================================

function calcularTotal(input) {
    const row = input.closest('tr');
    const inputs = row.querySelectorAll('.decimal-input, .no-spinner');
    
    let totalGanado = 0;
    let totalDescuentos = 0;
    
    // Obtener valores actuales
    const smn = parseFloat(row.querySelector('[name*="[smn]"]').value) || 0;
    const haberBasico = parseFloat(row.querySelector('[name*="[haber_basico]"]').value) || 0;
    const bonoAntiguedadInput = row.querySelector('[name*="[bono_antiguedad]"]');
    const aporteLaboralInput = row.querySelector('[name*="[aporte_laboral]"]');
    const ansInput = row.querySelector('[name*="[aporte_nacional_solidario]"]');
    
    // Calcular ingresos básicos
    totalGanado = haberBasico + 
                 (parseFloat(row.querySelector('[name*="[trabajo_extraordinario]"]').value) || 0) + 
                 (parseFloat(row.querySelector('[name*="[pago_domingo]"]').value) || 0) + 
                 (parseFloat(row.querySelector('[name*="[otros_bonos]"]').value) || 0);
    
    // Obtener datos del empleado para calcular antigüedad
    const empleadoInfo = row.querySelector('.empleado-info');
    const fechaIngreso = empleadoInfo.getAttribute('data-fecha-ingreso');
    
    // Calcular bono de antigüedad automáticamente si hay SMN y fecha de ingreso
    if (fechaIngreso && smn > 0) {
        const bonoAntiguedadCalculado = calcularAntiguedad(fechaIngreso, smn);
        bonoAntiguedadInput.value = bonoAntiguedadCalculado.toFixed(2);
        totalGanado += bonoAntiguedadCalculado;
    } else {
        // Si no se puede calcular automáticamente, usar valor manual
        totalGanado += (parseFloat(bonoAntiguedadInput.value) || 0);
    }
    
    // Calcular aporte laboral automáticamente
    const aporteLaboralCalculado = calcularAporteLaboral(totalGanado);
    aporteLaboralInput.value = aporteLaboralCalculado.toFixed(2);
    
    // Calcular ANS automáticamente
    const ansCalculado = calcularANS(totalGanado);
    ansInput.value = ansCalculado.toFixed(2);
    
    // Calcular total de descuentos
    totalDescuentos = aporteLaboralCalculado + 
                     ansCalculado + 
                     (parseFloat(row.querySelector('[name*="[rc_iva]"]').value) || 0) + 
                     (parseFloat(row.querySelector('[name*="[anticipos]"]').value) || 0);
    
    const total = totalGanado - totalDescuentos;
    const totalElement = row.querySelector('.total-value');
    totalElement.textContent = total.toFixed(2);
    
    // Colorear según el resultado
    if (total < 0) {
        totalElement.style.color = '#dc3545';
    } else {
        totalElement.style.color = '#28a745';
    }
}

// ==============================================
// FUNCIONES DE VALIDACIÓN DE ENTRADA
// ==============================================

function setupDecimalValidation() {
    document.querySelectorAll('.decimal-input').forEach(input => {
        input.addEventListener('input', function(e) {
            // Permitir solo números y un punto decimal
            this.value = this.value.replace(/[^0-9.]/g, '');
            
            // Asegurar máximo un punto decimal
            if ((this.value.match(/\./g) || []).length > 1) {
                this.value = this.value.substring(0, this.value.lastIndexOf('.'));
            }
            
            // Limitar a 2 decimales
            if (this.value.includes('.')) {
                const parts = this.value.split('.');
                if (parts[1].length > 2) {
                    parts[1] = parts[1].substring(0, 2);
                    this.value = parts.join('.');
                }
            }
            
            // Calcular total cuando se modifica el valor
            calcularTotal(this);
        });
        
        input.addEventListener('blur', function() {
            // Asegurar formato correcto al perder el foco
            if (this.value && this.value !== '0') {
                const num = parseFloat(this.value);
                this.value = num.toFixed(2);
            } else if (this.value === '' || this.value === '0') {
                this.value = '';
            }
        });
    });
}

function setupNumberValidation() {
    document.querySelectorAll('.no-spinner').forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            calcularTotal(this);
        });
    });
}

// ==============================================
// FUNCIÓN PARA APLICAR SMN GENERAL
// ==============================================

function aplicarSMNGeneral() {
    const smnGeneral = document.getElementById('smn_comun').value;
    if (smnGeneral) {
        document.querySelectorAll('.smn-input').forEach(input => {
            input.value = parseFloat(smnGeneral).toFixed(2);
            calcularTotal(input);
        });
    }
}

// ==============================================
// CONFIGURACIÓN DE ENVÍO DEL FORMULARIO
// ==============================================

function setupFormSubmission() {
    const form = document.getElementById('nominasForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const decimalInputs = this.querySelectorAll('.decimal-input');
            decimalInputs.forEach(input => {
                if (input.value === '' || input.value === '0') {
                    input.value = '0.00';
                }
            });
            
            const numberInputs = this.querySelectorAll('.no-spinner');
            numberInputs.forEach(input => {
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
    const commonFields = ['dias_pagados', 'horas_pagadas'];
    
    commonFields.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            input.addEventListener('change', function() {
                const value = this.value;
                document.querySelectorAll(`[name$="[${field}]"]`).forEach(el => {
                    el.value = value;
                });
                
                // Recalcular todos los totales después de copiar valores
                document.querySelectorAll('.decimal-input, .no-spinner').forEach(input => {
                    calcularTotal(input);
                });
            });
        }
    });
}

// ==============================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// ==============================================

document.addEventListener('DOMContentLoaded', function() {
    // Configurar validaciones
    setupDecimalValidation();
    setupNumberValidation();
    
    // Configurar envío del formulario
    setupFormSubmission();
    
    // Configurar copiado de campos comunes
    setupCommonFieldsCopy();
    
    // Calcular totales iniciales
    document.querySelectorAll('.decimal-input, .no-spinner').forEach(input => {
        calcularTotal(input);
    });
});