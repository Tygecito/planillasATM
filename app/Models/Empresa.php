<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas'; // Especificar la tabla si no es plural

    // Indicar que no se utilizarán las columnas created_at y updated_at
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'nit',
        'ciudad',
        'numero_patronal',
        'gestion',
        'nro_empleador_min_trab',
        'tipo_empresa',
        'representante_legal',
        'direccion',
        'ci_representante_legal',
        'mes',
        'salario_minimo_nacional',
        'email',
        'telefono',
        'fecha_creacion',
        'fecha_modificacion',
    ];
}