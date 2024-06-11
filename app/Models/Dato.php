<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dato extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'cliente_id',
        'nombre',
        'cargo',
        'telefono',
        'correo',
        'tarjeta',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class,'cliente_id');
    }
    public function servicios(){
        return $this->hasMany(Servicio::class);
    }
 
}
