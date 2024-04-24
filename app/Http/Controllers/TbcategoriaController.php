<?php

namespace App\Http\Controllers;

use App\Models\Tbcategoria;
use Illuminate\Http\Request;

class TbcategoriaController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:tbcategorias',
            // Aquí puedes agregar más reglas de validación según tus necesidades
        ]);
 
        $categoria = Tbcategoria::create($validatedData);
 
        return redirect()->back()->with('success', 'Categoría creada correctamente.');
    }
}
