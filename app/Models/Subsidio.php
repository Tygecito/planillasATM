<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subsidio extends Model
{
    use HasFactory;

    protected $table = 'subsidios';

    // Usar las columnas personalizadas
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_modificacion';

    /**
     * Campos que se pueden llenar masivamente.
     */
    protected $fillable = [
        'empleado_id',
        'mes',
        'anio',
        'tipo_subsidio',
        'monto',
        'estado',
    ];

    /**
     * Relación inversa: Un subsidio pertenece a un empleado.
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}