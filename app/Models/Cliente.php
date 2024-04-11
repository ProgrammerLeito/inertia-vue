<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Cliente extends Model
{
    use HasFactory;
 
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
        'prov_clientes'
        // Agrega aquí los demás campos que quieras que sean asignables en masa
    ];

    public function tbprovincias()
    {
        return $this->belongsTo(Tbprovincias::class);
    }
}