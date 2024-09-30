<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tbproducto extends Model
{
    use HasFactory;

    protected $fillable = [
        'tbcategoria_id',
        'tbsubcategoria_id',
        'tbmarca_id',
        'modelo',
        'medida',
        'moneda',
        'precio_min',
        'precio_max',
        'precio_list',
        'stock',
        'codigo',
        'capacidades',
        'especificaciones',
        'foto',
        'garantia',
        'link_producto'
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
