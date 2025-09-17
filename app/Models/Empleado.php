<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nombres', 'primerapellido', 'segundoapellido', 'sucursal', 
        'fecha_ingreso', 'caja_de_salud', 'tipo_de_contrato', 'modalidad_contrato', 
        'cargo_laboral', 'fecha_de_nacimiento', 'genero', 'estado_civil', 
        'documento_identidad', 'telefono', 'direccion', 'email', 'foto', 'estado',
        'fecha_creacion', 'fecha_modificacion' // Asegúrate de incluir estos campos
    ];

    public $timestamps = false; // Desactiva el uso de created_at y updated_at

    // Si deseas manejar la creación y actualización manualmente
    public function setCreatedAt($value)
    {
        $this->fecha_creacion = $value;
    }

    public function setUpdatedAt($value)
    {
        $this->fecha_modificacion = $value;
    }
}