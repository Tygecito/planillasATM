<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres', 'primerapellido', 'segundoapellido', 'sucursal', 
        'fecha_ingreso', 'caja_de_salud', 'tipo_de_contrato', 'modalidad_contrato', 
        'cargo_laboral', 'fecha_de_nacimiento', 'genero', 'estado_civil', 
        'documento_identidad', 'telefono', 'direccion', 'email', 'foto', 'estado',
        'fecha_creacion', 'fecha_modificacion'
    ];

    public $timestamps = false;

    // Relación con el modelo Usuario
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'empleado_id');
    }

    public function setCreatedAt($value)
    {
        $this->fecha_creacion = $value;
    }

    public function setUpdatedAt($value)
    {
        $this->fecha_modificacion = $value;
    }
}