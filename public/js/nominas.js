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
        // Aplica tramos
        let baseT3 = tg - UMBRALES_ANS.umbral3;
        ansTotal += (baseT3 * PORCENTAJES_ANS.tramo3) / 100;

        let baseT2 = UMBRALES_ANS.umbral3 - UMBRALES_ANS.umbral2;
        ansTotal += (baseT2 * PORCENTAJES_ANS.tramo2) / 100;

        let baseT1 = UMBRALES_ANS.umbral2 - UMBRALES_ANS.umbral1;
        ansTotal += (baseT1 * PORCENTAJES_ANS.tramo1) / 100;

    } else if (tg > UMBRALES_ANS.umbral2) {
        // Aplica tramo 1 y 2
        let baseT2 = tg - UMBRALES_ANS.umbral2;
        ansTotal += (baseT2 * PORCENTAJES_ANS.tramo2) / 100;

        let baseT1 = UMBRALES_ANS.umbral2 - UMBRALES_ANS.umbral1;
        ansTotal += (baseT1 * PORCENTAJES_ANS.tramo1) / 100;

    } else if (tg > UMBRALES_ANS.umbral1) {
        // Aplica solo tramo 1
        let baseT1 = tg - UMBRALES_ANS.umbral1;
        ansTotal += (baseT1 * PORCENTAJES_ANS.tramo1) / 100;
    }
    
    return ansTotal;
}

// ==============================================
// FUNCIÓN PRINCIPAL DE CÁLCULO
// ==============================================

function calcularTotal(input) {
    const row = input.closest('tr');
    
    // Obtener valores actuales. Se usan 0 por defecto.
    const smn = parseFloat(row.querySelector('[name*="[smn]"]').value) || 0;
    const haberBasico = parseFloat(row.querySelector('[name*="[haber_basico]"]').value) || 0;
    
    const bonoAntiguedadInput = row.querySelector('[name*="[bono_antiguedad]"]');
    const aporteLaboralInput = row.querySelector('[name*="[aporte_laboral]"]');
    const ansInput = row.querySelector('[name*="[aporte_nacional_solidario]"]');
    
    let totalGanado = 0;
    let totalDescuentos = 0;
    
    // 1. CALCULAR INGRESOS
    
    totalGanado = haberBasico + 
                  (parseFloat(row.querySelector('[name*="[trabajo_extraordinario]"]').value) || 0) + 
                  (parseFloat(row.querySelector('[name*="[pago_domingo]"]').value) || 0) + 
                  (parseFloat(row.querySelector('[name*="[otros_bonos]"]').value) || 0);
    
    // Obtener datos del empleado para calcular antigüedad
    const empleadoInfo = row.querySelector('.empleado-info');
    const fechaIngreso = empleadoInfo.getAttribute('data-fecha-ingreso');
    
    // Bono de Antigüedad (automático vs manual)
    if (fechaIngreso && smn > 0) {
        const bonoAntiguedadCalculado = calcularAntiguedad(fechaIngreso, smn);
        bonoAntiguedadInput.value = bonoAntiguedadCalculado.toFixed(2);
        totalGanado += bonoAntiguedadCalculado;
    } else {
        // Usar valor manual si no hay datos automáticos
        totalGanado += (parseFloat(bonoAntiguedadInput.value) || 0);
    }
    
    // 2. CALCULAR DESCUENTOS
    
    // Calcular aporte laboral automáticamente
    const aporteLaboralCalculado = calcularAporteLaboral(totalGanado);
    aporteLaboralInput.value = aporteLaboralCalculado.toFixed(2);
    
    // Calcular ANS automáticamente
    const ansCalculado = calcularANS(totalGanado);
    ansInput.value = ansCalculado.toFixed(2);
    
    // Sumar todos los descuentos
    totalDescuentos = aporteLaboralCalculado + 
                      ansCalculado + 
                      (parseFloat(row.querySelector('[name*="[rc_iva]"]').value) || 0) + 
                      (parseFloat(row.querySelector('[name*="[anticipos]"]').value) || 0);
    
    // 3. CÁLCULO FINAL
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
// FUNCIONES DE VALIDACIÓN DE ENTRADA EN CLIENTE
// ==============================================

/**
 * Configura la validación para campos decimales (permite punto y dos decimales).
 * Ignora los campos con clase 'integer-input'.
 */
function setupDecimalValidation() {
    document.querySelectorAll('.decimal-input:not(.integer-input)').forEach(input => {
        
        // 1. Listener de input: Limpia el valor en tiempo real
        input.addEventListener('input', function() {
            // Permitir solo números y un punto decimal
            this.value = this.value.replace(/[^0-9.]/g, '');
            
            // Asegurar máximo un punto decimal y limitar a 2 decimales
            if (this.value.includes('.')) {
                const parts = this.value.split('.');
                if (parts.length > 2) {
                    this.value = parts[0] + '.' + parts[1]; // Quita puntos extra
                }
                if (parts[1] && parts[1].length > 2) {
                    this.value = parts[0] + '.' + parts[1].substring(0, 2); // Limita a 2
                }
            }
            
            // Calcular total cuando se modifica el valor
            calcularTotal(this);
        });
        
        // 2. Listener de blur: Formatea el valor al perder el foco
        input.addEventListener('blur', function() {
            if (this.value && this.value !== '0' && this.value !== '.') {
                const num = parseFloat(this.value);
                // Si es un número válido, forzar a dos decimales
                if (!isNaN(num)) {
                    this.value = num.toFixed(2);
                } else {
                    this.value = '0.00';
                }
            } else if (this.value === '' || this.value === '0' || this.value === '.') {
                // Limpiar valores vacíos o cero
                this.value = ''; 
            }
        });
    });
}

/**
 * Configura la validación para campos enteros (solo números, sin punto).
 */
function setupIntegerValidation() {
    document.querySelectorAll('.integer-input').forEach(input => {
        
        // 1. Listener de keypress: Bloquea el punto en tiempo real
        input.addEventListener('keypress', function(event) {
            const charCode = event.charCode;
            // Bloquea el punto (46) y asegura que solo sean números (48-57)
            if (charCode === 46 || charCode < 48 || charCode > 57) {
                event.preventDefault();
            }
        });

        // 2. Listener de input: Limpia cualquier caracter no numérico
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            calcularTotal(this);
        });
        
        // 3. Listener de blur: Quita los decimales si se ingresaron
        input.addEventListener('blur', function() {
            if (this.value.includes('.')) {
                this.value = parseInt(this.value).toString();
            }
            if (this.value === '0') {
                this.value = ''; // Opcional: dejar en blanco si es cero
            }
        });
    });
}

// La función setupNumberValidation ya no se necesita, se reemplaza por setupIntegerValidation

// ==============================================
// CONFIGURACIÓN DE ENVÍO DEL FORMULARIO
// ==============================================

function setupFormSubmission() {
    const form = document.getElementById('nominasForm');
    if (form) {
        form.addEventListener('submit', function() {
            // Asegura que los campos decimales se envíen como '0.00' si están vacíos
            this.querySelectorAll('.decimal-input').forEach(input => {
                if (input.value === '') {
                    input.value = '0.00';
                }
            });
            
            // Asegura que los campos enteros se envíen como '0' si están vacíos
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
                document.querySelectorAll('input').forEach(input => {
                    if (input.closest('tr')) { // Solo inputs dentro de una fila de empleado
                        calcularTotal(input);
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
    // Configurar validaciones (Enteros primero, para que el blur del decimal no afecte)
    setupIntegerValidation();
    setupDecimalValidation();
    
    // Configurar envío del formulario
    setupFormSubmission();
    
    // Configurar copiado de campos comunes
    setupCommonFieldsCopy();
    
    // Calcular totales iniciales
    document.querySelectorAll('.decimal-input, .integer-input').forEach(input => {
        if (input.value) { // Solo calcular si tiene valor inicial
            calcularTotal(input);
        }
    });
});
