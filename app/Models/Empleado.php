<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permiso; 

class Empleado extends Model
{
    use HasFactory;

    // --- ¡NUEVO! ---
    // Definimos la lista de cargos aquí, basada en tu ENUM.
    public const CARGOS_LABORALES = [
        'ENCARGADO ALMACENES',
        'GERENTE GRAL',
        'VENTAS',
        'GERENTE FINANCIERO',
        'CAJERO(A)',
        'MENSAJERO/CHOFER',
        'AUXILIAR ALMACENES',
        'CONTABILIDAD',
        'REGENTE FARMACEUTICO'
    ];
    // --- ---

    protected $fillable = [
        'nombres', 'primerapellido', 'segundoapellido', 'sucursal', 
        'fecha_ingreso', 'caja_de_salud', 'tipo_de_contrato', 'modalidad_contrato', 
        'cargo_laboral', 'fecha_de_nacimiento', 'genero', 'estado_civil', 
        'documento_identidad', 
        'complemento', 'nit_dependiente', 
        'telefono', 'direccion', 'email', 'cua', 'estado', 
        'fecha_creacion', 'fecha_modificacion'
    ];

    public $timestamps = false;

    protected $casts = [
        'estado' => 'boolean',
    ];

    // ... (El resto de tus mutators 'setNombresAttribute', etc. no cambian) ...

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
    
    public function setComplementoAttribute($value)
    {
        $this->attributes['complemento'] = $value ? mb_strtoupper($value, 'UTF-8') : null;
    }

    public function setDireccionAttribute($value)
    {
        $this->attributes['direccion'] = mb_strtoupper($value, 'UTF-8');
    }

    public function setCuaAttribute($value)
    {
        $this->attributes['cua'] = $value ? (string) $value : null;
    }

    public function setNitDependienteAttribute($value)
    {
        $this->attributes['nit_dependiente'] = $value ? (int) $value : null;
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