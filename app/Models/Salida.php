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
        return $this->belongsTo(Producto::class);
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
        'salida_id',
    ];
}
