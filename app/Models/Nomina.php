<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    use HasFactory;

    protected $table = 'nominas';
    protected $primaryKey = 'id';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_modificacion';

    protected $fillable = [
        'empleado_id',
        'mes',
        'anio',
        'smn',
        'haber_basico',
        'horas_pagadas',
        'horas_extras',
        'dias_pagados',
        'bono_antiguedad',
        'trabajo_extraordinario',
        'pago_domingo',
        'otros_bonos',
        'total_ganado',
        'aporte_laboral',
        'aporte_nacional_solidario',
        
        // --- NUEVOS CAMPOS RC-IVA ---
        'rc_iva_f110_monto',
        'rc_iva_saldo_anterior',
        'rc_iva_saldo_siguiente',
        // ---------------------------
        
        'rc_iva',
        'anticipos',
        'total_descuentos',
        'liquido',
        'fecha_creacion',
        'fecha_modificacion'
    ];

    protected $casts = [
        'haber_basico' => 'decimal:2',
        'smn' => 'decimal:2',
        'bono_antiguedad' => 'decimal:2',
        'trabajo_extraordinario' => 'decimal:2',
        'pago_domingo' => 'decimal:2',
        'otros_bonos' => 'decimal:2',
        'total_ganado' => 'decimal:2',
        'aporte_laboral' => 'decimal:2',
        'aporte_nacional_solidario' => 'decimal:2',

        // --- NUEVOS CASTS RC-IVA ---
        'rc_iva_f110_monto' => 'decimal:2',
        'rc_iva_saldo_anterior' => 'decimal:2',
        'rc_iva_saldo_siguiente' => 'decimal:2',
        // ---------------------------

        'rc_iva' => 'decimal:2',
        'anticipos' => 'decimal:2',
        'total_descuentos' => 'decimal:2',
        'liquido' => 'decimal:2',
        'anio' => 'integer',
        'horas_pagadas' => 'integer',
        'horas_extras' => 'integer',
        'dias_pagados' => 'integer'
    ];

    // Relación con Empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    /**
     * Calcular el total de descuentos automáticamente.
     * --- CAMBIO: Se incluye el Saldo Anterior como una COMPENSACIÓN de descuento ---
     */
    public function calcularTotalDescuentos()
    {
        $descuentosObligatorios = ($this->aporte_laboral ?? 0) + 
                                  ($this->aporte_nacional_solidario ?? 0) + 
                                  ($this->rc_iva ?? 0) + 
                                  ($this->anticipos ?? 0);
        
        // El Saldo Anterior a favor del empleado COMPENSA el impuesto RC-IVA
        // Por eso, se resta del total de descuentos.
        $compensacion = $this->rc_iva_saldo_anterior ?? 0;

        return max(0, $descuentosObligatorios - $compensacion);
    }
    
    // El resto de los métodos se quedan igual
    
    public function scopePorMes($query, $mes)
    {
        if ($mes) {
            return $query->where('mes', $mes);
        }
        return $query;
    }

    public function scopePorAnio($query, $anio)
    {
        if ($anio) {
            return $query->where('anio', $anio);
        }
        return $query;
    }

    public function scopeDeEmpleado($query, $empleado_id)
    {
        if ($empleado_id) {
            return $query->where('empleado_id', $empleado_id);
        }
        return $query;
    }

    public function getPeriodoCompletoAttribute()
    {
        return $this->mes . ' ' . $this->anio;
    }

    public function getNombreEmpleadoAttribute()
    {
        return $this->empleado ? 
                     $this->empleado->nombres . ' ' . $this->empleado->primerapellido . ' ' . $this->empleado->segundoapellido : 
                     'Empleado no encontrado';
    }

    public function setMesAttribute($value)
    {
        $this->attributes['mes'] = ucfirst(strtolower($value));
    }

    public function calcularTotalGanado()
    {
        return $this->haber_basico + 
               ($this->bono_antiguedad ?? 0) + 
               ($this->trabajo_extraordinario ?? 0) + 
               ($this->pago_domingo ?? 0) + 
               ($this->otros_bonos ?? 0);
    }

    public function calcularLiquido()
    {
        return $this->calcularTotalGanado() - $this->calcularTotalDescuentos();
    }

    public function getSalarioGanadoAttribute()
    {
        return $this->haber_basico;
    }

    public function getTotalGanadoCalculadoAttribute()
    {
        return $this->calcularTotalGanado();
    }

    public function getTotalDescuentosCalculadoAttribute()
    {
        return $this->calcularTotalDescuentos();
    }

    public function getLiquidoCalculadoAttribute()
    {
        return $this->calcularLiquido();
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Calcular total_ganado
            if (empty($model->total_ganado) || $model->isDirty(['haber_basico', 'bono_antiguedad', 'trabajo_extraordinario', 'pago_domingo', 'otros_bonos'])) {
                $model->total_ganado = $model->calcularTotalGanado();
            }
            
            // Calcular total_descuentos (Ahora incluye la lógica de compensación de saldo anterior)
            if (empty($model->total_descuentos) || $model->isDirty([
                'aporte_laboral', 'aporte_nacional_solidario', 'rc_iva', 'anticipos', 'rc_iva_saldo_anterior' // <-- Añadido saldo anterior
            ])) {
                $model->total_descuentos = $model->calcularTotalDescuentos();
            }
            
            // Calcular líquido
            if (empty($model->liquido) || $model->isDirty(['total_ganado', 'total_descuentos'])) {
                $model->liquido = $model->calcularLiquido();
            }
        });
    }
}