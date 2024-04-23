<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cventa extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'cliente_id',
        'tenor_id',
        'fecha',
        'moneda',
        'garantia',
        'forma_pago',
        'dias_entrega',
        'subtotal',
        'igv',
        'total',
    ];
 
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
 
    public function tenor()
    {
        return $this->belongsTo(Tenor::class);
    }
 
}
