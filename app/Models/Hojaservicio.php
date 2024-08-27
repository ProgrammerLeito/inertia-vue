<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hojaservicio extends Model
{
    use HasFactory;
    protected $fillable=[
        'razon_social',
        'cantidad',
        'descripcion',
        'fecha',
        'lugar',
        'contacto',
        'usuario'
    ];

    protected $casts = [
        'hora_del_servicio' => 'datetime:H:i',
    ];
}
