<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformesClientes extends Model
{
    use HasFactory;
    
    protected $table = 'tb_informes_clientes';

    protected $fillable = [
        'n_servicio',
        'hmarca_id',
        'lugar_servicio',
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
        'fecha_final',
        'requiere',
        'diagnostico',
        'trabajos',
        'tecnico',
        'encargado',
        'fotoInforme1',
        'fotoInforme2',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($tb_informes_clientes) {
            $tb_informes_clientes->created_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
            $tb_informes_clientes->updated_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        });

        static::updating(function ($tb_informes_clientes) {
            $tb_informes_clientes->updated_at = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        });
    }
}
