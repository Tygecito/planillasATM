<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marcacion extends Model
{
    use HasFactory;

    // --- Timestamps en Español ---
    public const CREATED_AT = 'fecha_creacion';
    public const UPDATED_AT = 'fecha_modificacion';

    /**
     * La tabla asociada con el modelo.
     */
    protected $table = 'marcaciones'; 
    
    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'empleado_id',
        'ac_no',
        'fecha_hora',
    ];

    /**
     * Conversión automática de tipos.
     */
    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    /**
     * Relación: Una marcación pertenece a un Empleado.
     */
    public function empleado()
    {
        // Asume que tu modelo de Empleado está en App\Models\Empleado
        return $this->belongsTo(Empleado::class);
    }
}