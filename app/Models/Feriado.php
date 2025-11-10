<?php // <<< ¡Aquí está la etiqueta faltante!

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feriado extends Model
{
    use HasFactory;
    
    // Deshabilitamos las timestamps automáticas ya que usas nombres personalizados
    public $timestamps = false;
    
    // Definimos los nombres de las columnas que gestionan las fechas
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_modificacion';


    protected $fillable = [
        'fecha',
        'descripcion',
        'tipo',
        'empleado_id',
    ];
    
    // Relación inversa con Empleado (si aplica)
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}