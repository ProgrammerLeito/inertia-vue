<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hservicio extends Model
{
    use HasFactory;
    protected $fillable = [
        'n_servicio',
        'hmarca_id',
        'instrumento',
        'rango',
        'medida_bastago',
        'medida_bastago2',
        'codigo',
        'material',
        'modelo',
        'serie',
        'division',
        'medida_division',
        'capacidad',
        'medida_capacidad',
        'cliente_id',
        'plataforma',
        'medida_plataforma',
        'fecha',
        'requiere',
        'diagnostico',
        'trabajos',
        'tecnico',
        'encargado',
        'foto',
        'foto2',
        'foto3',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($hservicio) {
            $hservicio->created_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
            $hservicio->updated_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        });

        static::updating(function ($hservicio) {
            $hservicio->updated_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        });
    }
}
