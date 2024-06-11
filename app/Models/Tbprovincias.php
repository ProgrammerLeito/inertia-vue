<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tbprovincias extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
 
    protected $primaryKey = 'id';
 
   
    protected $fillable = ['prov_nombre'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

}
