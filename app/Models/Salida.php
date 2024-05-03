<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function salida()
    {
        return $this->belongsTo(Salida::class);
    }

    protected $fillable = [
        'empresa',
        'unidad_salida',
        'comentario_salida',
        'tecnico',
        'fecha',
        'hora_salida',
        'producto_id',
        'devolucion',
        'unidad_devolucion',
    ];
}
