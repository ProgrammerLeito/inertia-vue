<?php

namespace App\Http\Controllers;

use App\Models\Tbsubcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TbsubcategoriaController extends Controller
{
  
    public function store(Request $request)
    {
        $request->validate([
            'tbcategoria_id' => 'required',
            'nombre' => 'required',
        ]);
        $tbsubcategoria = new Tbsubcategoria();
        
        $tbsubcategoria->tbcategoria_id = $request->tbcategoria_id;
        $tbsubcategoria->nombre = $request->nombre;
        $tbsubcategoria->save();
   
        return redirect()->back();
    }

    public function obtenerSubcategoriasRegistradas (){
        $subcategorias = DB::table('tbsubcategorias')
            ->select(
                'id',
                'tbcategoria_id',
                'nombre'
            )
            ->orderBy("tbsubcategorias.id","ASC")
            ->get();

        return response()->json($subcategorias);
    }
}
