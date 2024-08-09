<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbproductosAgregados extends Model
{
    use HasFactory;

    protected $fillable=[
        'idCotizacion',
        'subcategoria_id',
        'modelo',
        'especificaciones',
        'marca',
        'capacidades',
        'precio_list',
        'precio_min',
        'precio_max',
        'cantidad',
        'importe',
        'garantia',
        'dias_entrega',
        'forma_pago',
        'moneda',
        'foto',
        'requerimientos',
    ];
}
