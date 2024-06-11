<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tbproducto extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'tbcategoria_id',
        'tbsubcategoria_id',
        'tbmarca_id',
        'modelo',
        'medida',
        'moneda',
        'precio',
        'precio_max',
        'descuento',
        'stock',
        'codigo',
        'estado',
        'capacidades',
        'especificaciones',
        'foto'
    ];

    public function tbcategoria()
    {
        return $this->belongsTo(Tbcategoria::class);
    }

    public function tbsubcategoria()
    {
        return $this->belongsTo(Tbsubcategoria::class);
    }

    public function tbmarca()
    {
        return $this->belongsTo(Tbmarca::class);
    }
}
