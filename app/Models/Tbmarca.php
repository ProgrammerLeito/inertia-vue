<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tbmarca extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tbsubcategoria_id'
    ];

    public function tbsubcategoria()
    {
        return $this->belongsTo(Tbsubcategoria::class);
    }
}
