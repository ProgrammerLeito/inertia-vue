<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trealizado extends Model
{
    use HasFactory;
    protected $fillable=['descripcion','ctrabajo_id'];

    public function ctrabajo()
    {
        return $this->belongsTo(Ctrabajo::class);
    }
}
