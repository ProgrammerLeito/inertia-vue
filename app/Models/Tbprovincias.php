<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tbprovincias extends Model
{
    use HasFactory;

    protected $guarded = [];
 
    protected $primaryKey = 'id';
 
    protected $fillable = [
        'prov_nombre',
        'estado'
    ];
}
