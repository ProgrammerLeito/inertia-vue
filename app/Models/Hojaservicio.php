<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hojaservicio extends Model
{
    use HasFactory;
    protected $fillable=[
        'n_informe',
        'servicio_a_realizar',
        'razon_social',
        'direccion',
        'contacto',
        'area_de_contacto',
        'telefono_de_contacto',
        'asesor_encargado',
        'tecnico_encargado',
        'hora_del_servicio',
        'cantidad_de_instrumentos',
        'datos_del_instrumento',
        'trabajos_a_realizar',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hojaservicio) {
            $lastService = self::orderBy('id', 'desc')->first();
            $nextNumber = $lastService ? str_pad((int) $lastService->n_informe + 1, 9, '0', STR_PAD_LEFT) : '000000001';
            $hojaservicio->n_informe = $nextNumber;
        });
    }
    protected $casts = [
        'hora_del_servicio' => 'datetime:H:i',
    ];
}
