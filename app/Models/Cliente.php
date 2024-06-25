<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $fillable = [
        'numeroDocumento',
        'razonSocial',
        'direccion',
        'distrito',
        'provincia',
        'departamento',
        'estado',
        'ctg',
        'asesor',
        'fechafactura',
        'codigofactura',
        'cli_direccion2',
        'cli_observacion',
        'tbprovincia_id',
    ];
    public function tbprovincia()
    {
        return $this->belongsTo(Tbprovincias::class);
    }
    public function datos()
    {
        return $this->hasMany(Dato::class);
    }
    public function servicios(){
        return $this->hasMany(Servicio::class);
    }

}
