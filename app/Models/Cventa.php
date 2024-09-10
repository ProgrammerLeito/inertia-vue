<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cventa extends Model
{
    use HasFactory;

    protected $fillable = [
        'n_cotizacion',
        'codesunat',
        'estado',
        'tecnico',
        'cliente_id',
        'numeroDocumento',
        'direccion',
        'fecha',
        'moneda',
        'tipoCambio',
        'forma_pago',
        'dias_entrega',
        'validez_cot',
        'subtotal',
        'igv',
        'total'
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

}
