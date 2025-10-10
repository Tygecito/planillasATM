<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// AÑADIDO: Necesario para la relación y el cálculo del saldo de vacaciones
use App\Models\Permiso; 

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

    /**
     * The attributes that should be cast.
     * Esto corrige la lectura de TINYINT (estado) desde MySQL como NULL, forzándolo a booleano.
     * @var array<string, string>
     */
    protected $casts = [
        'estado' => 'boolean', // Convierte 1/0 de TINYINT a true/false
    ];

    // Relación con el modelo Usuario
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'empleado_id');
    }

    // AÑADIDO: Relación con el modelo Permiso, necesaria para calcular el saldo de vacaciones.
    public function permisos()
    {
        // Asumiendo que la clave foránea en la tabla 'permisos' es 'empleado_id'
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
    
    // ====================================================================
    // MÉTODO AÑADIDO: getSaldoVacaciones()
    // Soluciona el error 'Call to undefined method' en PermisoController.
    // ====================================================================

    /**
     * Calcula y retorna el saldo de días de vacaciones disponibles del empleado.
     * @param int|null $exceptPermisoId Opcional. ID de un permiso a excluir del cálculo.
     * @return float
     */
    public function getSaldoVacaciones(int $exceptPermisoId = null): float
    {
        // 1. Días base de vacaciones:
        // *** IMPORTANTE: AJUSTA ESTA LÓGICA ***
        // Por defecto, asumimos 15 días base. Si la lógica es más compleja 
        // (por antigüedad, etc.), debe implementarse aquí.
        $dias_base_anuales = 15.0; 

        // 2. Query para días consumidos (solo VACACION en estado APROBADO)
        $query = $this->permisos()
            ->where('tipo_permiso', 'VACACION')
            ->where('estado', 'APROBADO'); 

        // Excluir un permiso específico (necesario para la edición en PermisoController)
        if ($exceptPermisoId) {
            $query->where('id', '!=', $exceptPermisoId);
        }

        // Sumar los días solicitados en los permisos
        // Asume que la tabla 'permisos' tiene una columna 'dias_solicitados' (float/decimal)
        $dias_consumidos = $query->sum('dias_solicitados');

        // 3. Calcular el saldo
        $saldo = $dias_base_anuales - $dias_consumidos;

        return max(0.0, $saldo);
    }
}
