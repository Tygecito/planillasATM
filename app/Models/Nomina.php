<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    use HasFactory;

    protected $table = 'nominas'; // Nombre de la tabla en la BD
    protected $primaryKey = 'id';

    protected $fillable = [
        'empleado_id',
        'mes',
        'anio',
        'salario_bruto',
        'descuentos',
        'salario_neto',
    ];

    // Relación con Empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}
