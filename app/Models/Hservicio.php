<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hservicio extends Model
{
    use HasFactory;
    protected $fillable = [
        'servicio_id',
        'hmarca_id',
        'modelo',
        'tecnico',
        'serie',
        'div',
        'capacidad',
        'plataforma',
        'fecha',
        'requiere',
        'diagnostico',
        'trabajos',
        'foto',
        'foto2',
        'foto3',
        'n_informe',
        'razonSocial',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
    public function hmarca(){
        return $this->belongsTo(Hmarca::class);
    }
}
