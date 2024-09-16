<?php

namespace App\Http\Controllers;

use App\Models\Tbprovincias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TbprovinciaController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'prov_nombre' => 'required|string|max:255|unique:tbprovincias',
        ]); 
        Tbprovincias::create($validatedData);

        return redirect()->back()->with('success', 'Marca creada correctamente.');
    }

    public function obtenerProvinciasRegistradas (){
        $provincias = DB::table('tbprovincias')
            ->select(
                'id',
                'prov_nombre'
            )
            ->orderBy("tbprovincias.id","ASC")
            ->get();

        return response()->json($provincias);
    }
}
