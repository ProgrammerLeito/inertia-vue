<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Tbmarca extends Model
{
    use HasFactory;
    
    protected $fillable = ['nombre','tbcategoria_id'];
 
    public function tbcategoria()
    {
        return $this->belongsTo(Tbcategoria::class);
    }
}