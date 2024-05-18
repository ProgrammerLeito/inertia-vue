<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;
 
    protected $guarded = [];
 
    protected $primaryKey = 'id';
 
    protected $fillable = [
        'numeroDocumento',
        'razonSocial',
        'direccion',
        'distrito',
        'provincia',
        'departamento',
        'estado',
        'cli_direccion2',
        'cli_observacion',
        'tbprovincia_id',
        'ctg',
        'asesor',
    ];
 
 
 
    public function tbprovincia()
    {
        return $this->belongsTo(Tbprovincias::class);
    }
    public function datos()
    {
        return $this->hasMany(Dato::class);
    }
}