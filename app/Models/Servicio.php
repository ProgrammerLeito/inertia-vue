<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Servicio extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'cliente_id',
        'direccion',
        'dato_id',
        'user_id',
        'n_guia',
        'fecha',
        'hora',
        'tecnico',
        'estado',
        'descripcion',
    ];
    
    protected static function boot()
    {
        parent::boot();
 
        static::creating(function ($servicio) {
            $lastService = self::orderBy('id', 'desc')->first();
            $nextNumber = $lastService ? str_pad((int) $lastService->n_informe + 1, 6, '0', STR_PAD_LEFT) : '000001';
            $servicio->n_informe = $nextNumber;
        });
    }
 
    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
 
    public function dato(){
        return $this->belongsTo(Dato::class);
    }
 
    public function user(){
        return $this->belongsTo(User::class);
    }
 
}
