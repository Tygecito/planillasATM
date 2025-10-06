<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'role',
        'empleado_id'
    ];

    protected $hidden = [
        'password'
    ];

    // Relación con el modelo Empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function getFullNameAttribute()
    {
        if ($this->empleado) {
            return trim("{$this->empleado->nombres} {$this->empleado->primerapellido} {$this->empleado->segundoapellido}");
        }
        return 'Nombre no disponible';
    }
}