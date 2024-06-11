<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tbsubcategoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'categoria_id'
    ];

    public function tbcategoria()
    {
        return $this->belongsTo(Tbcategoria::class);
    }
    public function tbmarcas()
    {
        return $this->hasMany(Tbmarca::class, 'tbcategoria_id');
    }
}
