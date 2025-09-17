<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios'; // Nombre de tu tabla
    protected $primaryKey = 'id';
    public $timestamps = false; // Usamos fecha_creacion y fecha_modificacion

    protected $fillable = [
        'username',
        'password',
        'role',
        'empleado_id'
    ];

    protected $hidden = [
        'password' // Ocultar la contraseña en las respuestas JSON
    ];

    // Relación con el modelo Empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    // Método para obtener el nombre completo del empleado
    public function getFullNameAttribute()
    {
        if ($this->empleado) {
            return trim("{$this->empleado->nombres} {$this->empleado->primerapellido} {$this->empleado->segundoapellido}");
        }
        return 'Nombre no disponible';
    }
}