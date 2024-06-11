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

        'modelo',
        'foto',
        'especificaciones',
        'capacidades',
        'precio',
        'precio_max',
        'cantidad',
        'importe',
        'marca',
        'n_cotizacion',
        'codesunat',
        'estado',
        'tecnico',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cventas) {
            $lastService = self::orderBy('id', 'desc')->first();
            $nextNumber = $lastService ? str_pad((int) $lastService->n_cotizacion + 1, 10, '0', STR_PAD_LEFT) : '0000000001';
            $cventas->n_cotizacion = $nextNumber;
        });
    }
    protected $casts = [
        'productos' => 'array',
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
