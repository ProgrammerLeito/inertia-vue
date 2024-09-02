<?php

namespace App\Models;

use Carbon\Carbon;
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
        'hora',
        'lugar',
        'contacto',
        'nro_contacto',
        'usuario'
    ];

    protected $casts = [
        'hora_del_servicio' => 'datetime:H:i',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($hojaservicio) {
            $hojaservicio->created_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
            $hojaservicio->updated_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        });

        static::updating(function ($hojaservicio) {
            $hojaservicio->updated_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        });
    }
}
