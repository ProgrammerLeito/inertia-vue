<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'idCliente';

    protected $fillable = [
        'cli_razonSocial',
        'cli_ruc',
        'cli_ciudad',
        'cli_direccionlegal',
        'cli_direccion1',
        'cli_direccion2',
        'cli_observacion'
    ];
}
