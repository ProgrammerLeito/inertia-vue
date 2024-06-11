<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tbcategoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre'
    ];

    public function tbsubcategorias()
    {
        return $this->hasMany(Tbsubcategoria::class);
    }

}
