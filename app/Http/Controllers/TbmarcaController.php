<?php

namespace App\Http\Controllers;

use App\Models\Tbmarca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TbmarcaController extends Controller
{

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'tbsubcategoria_id' => 'required|exists:tbsubcategorias,id',
        ]);
        Tbmarca::create($validatedData);
        $tbmarcas=Tbmarca::all();

        return redirect()->back()->with('success', 'Marca creada correctamente.', $tbmarcas);
    }

    public function obtenerMarcasRegistradas (){
        $subcategorias = DB::table('tbmarcas')
            ->select(
                'id',
                'nombre'
            )
            ->orderBy("tbmarcas.id","ASC")
            ->get();

        return response()->json($subcategorias);
    }
}
