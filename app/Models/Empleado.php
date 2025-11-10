<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permiso;
use App\Models\Subsidio;
use Carbon\Carbon; // <<< ¡IMPORTANTE! Asegúrate de incluir Carbon

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

    // =================================================================
    //                                 ACCESSORS / MUTATORS
    // =================================================================

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


    public function setCreatedAt($value)
    {
        $this->fecha_creacion = $value;
    }

    public function setUpdatedAt($value)
    {
        $this->fecha_modificacion = $value;
    }

    // =================================================================
    //                                 RELACIONES
    // =================================================================

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'empleado_id');
    }

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'empleado_id');
    }

    public function subsidios()
    {
        return $this->hasMany(Subsidio::class, 'empleado_id');
    }

    // =================================================================
    //                                LÓGICA DE VACACIONES
    // =================================================================

    /**
     * Calcula la antigüedad del empleado en AÑOS completos al día de hoy.
     * @return int
     */
    public function getAntiguedadEnAnios(): int
    {
        $fechaIngreso = Carbon::parse($this->fecha_ingreso);
        return $fechaIngreso->diffInYears(Carbon::now());
    }

    /**
     * Devuelve el derecho a días de vacación para el período anual ACTUAL
     * según la antigüedad cumplida en la ley boliviana.
     * @return float Días de vacaciones base anuales.
     */
    public function calcularDerechoAnual(): float
    {
        $antiguedad = $this->getAntiguedadEnAnios();

        if ($antiguedad >= 10) {
            return 30.0; // Más de 10 años
        } elseif ($antiguedad >= 5) {
            return 20.0; // Entre 5 y 10 años
        } elseif ($antiguedad >= 1) {
            return 15.0; // Entre 1 y 5 años
        }
        return 0.0; // Menos de 1 año
    }

    /**
     * **FUNCIÓN CLAVE: CALCULA EL SALDO TOTAL DE DÍAS DISPONIBLES.**
     * Suma el derecho ganado por cada año completo y resta lo ya usado (APROBADO).
     * @param int|null $excludePermisoId ID del permiso a ignorar en la deducción (usado en el UPDATE).
     * @return float Saldo de vacaciones actual en días.
     */
    public function getSaldoVacaciones(int $excludePermisoId = null): float
    {
        $aniosCompletos = $this->getAntiguedadEnAnios();
        $diasGanadosBrutos = 0.0;

        // 1. CALCULAR DÍAS GANADOS (Acumulación Histórica por aniversario)
        for ($antiguedadCumplida = 1; $antiguedadCumplida <= $aniosCompletos; $antiguedadCumplida++) {
            
            // Lógica basada en la antigüedad CUMPLIDA
            if ($antiguedadCumplida >= 10) {
                $diasGanadosBrutos += 30.0;
            } elseif ($antiguedadCumplida >= 5) {
                $diasGanadosBrutos += 20.0;
            } elseif ($antiguedadCumplida >= 1) {
                $diasGanadosBrutos += 15.0;
            }
        }
        
        // 2. DEDUCCIÓN (Días de VACACIÓN APROBADOS)
        $diasTomadosAprobados = $this->permisos()
            ->where('tipo_permiso', 'VACACION')
            ->where('estado', 'APROBADO')
            ->when($excludePermisoId, fn($query) => $query->where('id', '!=', $excludePermisoId))
            ->sum('dias_solicitados');
            
        // 3. SALDO FINAL
        $saldoFinal = $diasGanadosBrutos - $diasTomadosAprobados;

        return max(0.0, $saldoFinal); 
    }
}