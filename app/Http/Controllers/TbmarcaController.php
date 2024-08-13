<?php

namespace App\Http\Controllers;

use App\Models\Tbmarca;
use Illuminate\Http\Request;

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

}
