<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class C_Servicio extends Model
{
    use HasFactory;

    protected $table = 'cservicios';
    protected $fillable = [
        'descripcion',
        'fecha',
        'marca',
        'modelo',
        'capacidad',
        'requiere',
        'division',
        'plataforma',
        'serie',
        'tselecionado',
        'cselecionado',
        'oselecionado',
        'dselecionado',
        'rselecionado',
        'precio',
        'moneda',
        'fpago',
        'garantia',
        'dentrega',
        'foto',
        'foto1',
        'total',
        'subtotal',
        'igv',
        'plantilla_id',
        'condicione_id',
        'observacione_id',
        'documento_id',
        'recomendacione_id',
        'trealizado_id',
        'n_informe',
        'razonSocial',
        'codesunat',

    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cservicios) {
            $lastService = self::orderBy('id', 'desc')->first();
            $nextNumber = $lastService ? str_pad((int) $lastService->n_cotizacion + 1, 10, '0', STR_PAD_LEFT) : '0000000001';
            $cservicios->n_cotizacion = $nextNumber;
        });
    }
    public function plantilla()
    {
        return $this->belongsTo(Plantilla::class);
    }

}
