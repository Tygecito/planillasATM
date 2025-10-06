<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    use HasFactory;

    protected $table = 'nominas'; // Nombre de la tabla en la BD
    protected $primaryKey = 'id';

    // 👇 Usar tus columnas personalizadas como timestamps
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
        // 'salario_ganado' ELIMINADO
        'bono_antiguedad',
        'trabajo_extraordinario',
        'pago_domingo',
        'otros_bonos',
        'total_ganado',
        'aporte_laboral',
        'aporte_nacional_solidario',
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
        // 'salario_ganado' ELIMINADO
        'bono_antiguedad' => 'decimal:2',
        'trabajo_extraordinario' => 'decimal:2',
        'pago_domingo' => 'decimal:2',
        'otros_bonos' => 'decimal:2',
        'total_ganado' => 'decimal:2',
        'aporte_laboral' => 'decimal:2',
        'aporte_nacional_solidario' => 'decimal:2',
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
     * Scope para filtrar por mes
     */
    public function scopePorMes($query, $mes)
    {
        if ($mes) {
            return $query->where('mes', $mes);
        }
        return $query;
    }

    /**
     * Scope para filtrar por año
     */
    public function scopePorAnio($query, $anio)
    {
        if ($anio) {
            return $query->where('anio', $anio);
        }
        return $query;
    }

    /**
     * Scope para nóminas de un empleado específico
     */
    public function scopeDeEmpleado($query, $empleado_id)
    {
        if ($empleado_id) {
            return $query->where('empleado_id', $empleado_id);
        }
        return $query;
    }

    /**
     * Accesor para el periodo completo (Mes Año)
     */
    public function getPeriodoCompletoAttribute()
    {
        return $this->mes . ' ' . $this->anio;
    }

    /**
     * Accesor para el nombre completo del empleado
     */
    public function getNombreEmpleadoAttribute()
    {
        return $this->empleado ? 
               $this->empleado->nombres . ' ' . $this->empleado->primerapellido . ' ' . $this->empleado->segundoapellido : 
               'Empleado no encontrado';
    }

    /**
     * Mutator para asegurar que el mes tenga la primera letra en mayúscula
     */
    public function setMesAttribute($value)
    {
        $this->attributes['mes'] = ucfirst(strtolower($value));
    }

    /**
     * Calcular el total ganado automáticamente (USANDO HABER_BASICO)
     */
    public function calcularTotalGanado()
    {
        return $this->haber_basico + 
               ($this->bono_antiguedad ?? 0) + 
               ($this->trabajo_extraordinario ?? 0) + 
               ($this->pago_domingo ?? 0) + 
               ($this->otros_bonos ?? 0);
    }

    /**
     * Calcular el total de descuentos automáticamente
     */
    public function calcularTotalDescuentos()
    {
        return ($this->aporte_laboral ?? 0) + 
               ($this->aporte_nacional_solidario ?? 0) + 
               ($this->rc_iva ?? 0) + 
               ($this->anticipos ?? 0);
    }

    /**
     * Calcular el líquido pagable automáticamente
     */
    public function calcularLiquido()
    {
        return $this->calcularTotalGanado() - $this->calcularTotalDescuentos();
    }

    /**
     * Accesor para obtener el salario ganado (ahora es igual al haber básico)
     * Para mantener compatibilidad con vistas que aún usen salario_ganado
     */
    public function getSalarioGanadoAttribute()
    {
        return $this->haber_basico;
    }

    /**
     * Accesor para el total ganado calculado (atributo virtual)
     */
    public function getTotalGanadoCalculadoAttribute()
    {
        return $this->calcularTotalGanado();
    }

    /**
     * Accesor para el total descuentos calculado (atributo virtual)
     */
    public function getTotalDescuentosCalculadoAttribute()
    {
        return $this->calcularTotalDescuentos();
    }

    /**
     * Accesor para el líquido calculado (atributo virtual)
     */
    public function getLiquidoCalculadoAttribute()
    {
        return $this->calcularLiquido();
    }

    /**
     * Boot del modelo para calcular automáticamente los campos derivados
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Calcular total_ganado usando haber_basico directamente
            if (empty($model->total_ganado) || $model->isDirty(['haber_basico', 'bono_antiguedad', 'trabajo_extraordinario', 'pago_domingo', 'otros_bonos'])) {
                $model->total_ganado = $model->calcularTotalGanado();
            }
            
            // Calcular total_descuentos
            if (empty($model->total_descuentos) || $model->isDirty(['aporte_laboral', 'aporte_nacional_solidario', 'rc_iva', 'anticipos'])) {
                $model->total_descuentos = $model->calcularTotalDescuentos();
            }
            
            // Calcular líquido
            if (empty($model->liquido) || $model->isDirty(['total_ganado', 'total_descuentos'])) {
                $model->liquido = $model->calcularLiquido();
            }
        });
    }
}