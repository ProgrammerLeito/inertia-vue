<?php

namespace App\Http\Controllers;

use App\Models\Tbmarca;
use Illuminate\Http\Request;

class TbmarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:tbmarcas',
            'tbcategoria_id' => 'required|exists:tbcategorias,id',
            // Aquí puedes agregar más reglas de validación según tus necesidades
        ]);
 
        $marca = Tbmarca::create($validatedData);
 
        return redirect()->back()->with('success', 'Marca creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tbmarca $tbmarca)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tbmarca $tbmarca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tbmarca $tbmarca)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tbmarca $tbmarca)
    {
        //
    }
}
