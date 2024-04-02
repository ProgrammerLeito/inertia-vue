<?php

namespace App\Http\Controllers;

use App\Models\Tbcategoria;
use Illuminate\Http\Request;

class TbcategoriaController extends Controller
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
            'nombre' => 'required|string|max:255|unique:tbcategorias',
            // Aquí puedes agregar más reglas de validación según tus necesidades
        ]);
 
        $categoria = Tbcategoria::create($validatedData);
 
        return redirect()->back()->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tbcategoria $tbcategoria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tbcategoria $tbcategoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tbcategoria $tbcategoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tbcategoria $tbcategoria)
    {
        //
    }
}
