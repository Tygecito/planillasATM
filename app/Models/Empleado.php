<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permiso; 

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres', 'primerapellido', 'segundoapellido', 'sucursal', 
        'fecha_ingreso', 'caja_de_salud', 'tipo_de_contrato', 'modalidad_contrato', 
        'cargo_laboral', 'fecha_de_nacimiento', 'genero', 'estado_civil', 
        'documento_identidad', 'telefono', 'direccion', 'email', 'cua', 'estado', // CAMBIO: foto por cua
        'fecha_creacion', 'fecha_modificacion'
    ];

    public $timestamps = false;

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function setNombresAttribute($value)
    {
        $this->attributes['nombres'] = mb_strtoupper($value, 'UTF-8');
    }

    public function setPrimerApellidoAttribute($value)
    {
        $this->attributes['primerapellido'] = mb_strtoupper($value, 'UTF-8');
    }

    public function setSegundoApellidoAttribute($value)
    {
        $this->attributes['segundoapellido'] = mb_strtoupper($value, 'UTF-8');
    }

    public function setSucursalAttribute($value)
    {
        $this->attributes['sucursal'] = mb_strtoupper($value, 'UTF-8');
    }
    
    public function setCargoLaboralAttribute($value)
    {
        $this->attributes['cargo_laboral'] = mb_strtoupper($value, 'UTF-8');
    }

    public function setDocumentoIdentidadAttribute($value)
    {
        $this->attributes['documento_identidad'] = mb_strtoupper($value, 'UTF-8');
    }
    
    public function setDireccionAttribute($value)
    {
        $this->attributes['direccion'] = mb_strtoupper($value, 'UTF-8');
    }

    // CAMBIO: Mutator para CUA
    public function setCuaAttribute($value)
    {
        $this->attributes['cua'] = $value ? (string) $value : null;
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'empleado_id');
    }

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'empleado_id');
    }

    public function setCreatedAt($value)
    {
        $this->fecha_creacion = $value;
    }

    public function setUpdatedAt($value)
    {
        $this->fecha_modificacion = $value;
    }
    
    public function getSaldoVacaciones(int $exceptPermisoId = null): float
    {
        $dias_base_anuales = 15.0;

        $query = $this->permisos()
            ->where('tipo_permiso', 'VACACION')
            ->where('estado', 'APROBADO'); 

        if ($exceptPermisoId) {
            $query->where('id', '!=', $exceptPermisoId);
        }

        $dias_consumidos = $query->sum('dias_solicitados');

        $saldo = $dias_base_anuales - $dias_consumidos;

        return max(0.0, $saldo);
    }
}