<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
    use HasFactory;
    protected $table = 'plantillas';

    protected $fillable = ['descripcion'   ];

    public function cServicios()
    {
        return $this->hasMany(C_Servicio::class);
    }
}
