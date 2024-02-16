<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function entrada()
    {
        return $this->belongsTo(Entrada::class);
    }

    protected $fillable = [
        'cantidad',
        'fecha',
        'producto_id',
    ];
}
