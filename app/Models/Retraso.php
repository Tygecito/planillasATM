<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retraso extends Model
{
    use HasFactory;

    // --- Timestamps en Español ---
    public const CREATED_AT = 'fecha_creacion';
    public const UPDATED_AT = 'fecha_modificacion';
    
    /**
     * La tabla asociada con el modelo.
     */
    protected $table = 'retrasos';
    
    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'empleado_id',
        'fecha',
        'tipo',
        'minutos_retraso',
        'hora_limite',
        'hora_marcacion',
    ];

    /**
     * Relación: Un retraso pertenece a un Empleado.
     */
    public function empleado()
    {
        // Asume que tu modelo de Empleado está en App\Models\Empleado
        // ¡Esta relación es la que usa la vista (index.blade.php)!
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}