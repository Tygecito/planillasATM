<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    /**
     * CORRECCIÓN: Deshabilitamos las timestamps automáticas.
     * Esto soluciona el error "Unknown column 'updated_at'" porque tu tabla
     * usa 'fecha_creacion' y 'fecha_modificacion' y las gestiona la base de datos.
     */
    public $timestamps = false;

    protected $fillable = [
        'empleado_id',
        'tipo_permiso',
        'fecha_solicitud',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'duracion_horas',
        'dias_solicitados',
        'motivo',
        'estado',
        'aprobado_por',
        // 'fecha_creacion' y 'fecha_modificacion' se gestionan por la DB (dado que $timestamps = false)
    ];

    /**
     * Define la relación inversa con el Empleado.
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
    
    /**
     * Define la relación con el Usuario que aprobó el permiso.
     */
    public function aprobador()
    {
        // Asumiendo que el modelo Usuario está en App\Models
        return $this->belongsTo(Usuario::class, 'aprobado_por');
    }
}
