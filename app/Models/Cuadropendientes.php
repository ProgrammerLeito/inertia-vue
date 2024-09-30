<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuadropendientes extends Model
{
    use HasFactory;

    protected $table = 'tb_pendientes';
    
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

        static::creating(function ($cuadropendientes) {
            $cuadropendientes->created_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
            $cuadropendientes->updated_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        });

        static::updating(function ($cuadropendientes) {
            $cuadropendientes->updated_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        });
    }
}
