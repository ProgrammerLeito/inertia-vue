<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    protected $fillable = [
        'insumo',
        'marca',
        'modelo',
        'cantidad',
        'unidad_medida',
        'fecha',
        'comprador',
        'comentario',
        'stock',
        'precio',
        'imagen_producto',
        'ultima_entrada',
        'category_id',
    ];
}
