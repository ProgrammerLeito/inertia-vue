<?php

namespace App\Http\Controllers;

use App\Models\Tbprovincias;
use Illuminate\Http\Request;

class TbprovinciaController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'prov_nombre' => 'required|string|max:255|unique:tbprovincias',
        ]);
 
        $tbprovincia = Tbprovincias::create($validatedData);
 
        return redirect()->back()->with('success', 'Marca creada correctamente.');
    }
}
