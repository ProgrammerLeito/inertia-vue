<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Inertia\Response;

class ProductoController extends Controller
{
    const Numero_de_items_pagina =25;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::paginate(self::Numero_de_items_pagina);
        return inertia('Productos/Index', ['productos' => $productos]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Productos/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'insumo' => 'required|string',
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'cantidad' => 'required|numeric',
            'unidad_medida' => 'required|string',
            'fecha' => 'required',
            'empresa' => 'required|string',
            'comentario' => 'nullable|string',
        ]);
    
        Producto::create($validated);

        return redirect()->route('productos.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $productos)
    {
        return inertia('Productos/Edit', ['productos' => $productos]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $productos)
    {
        $productos->update($request->all());
        return redirect()->route('productos.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $productos)
    {
        $productos->delete();
        return redirect()->route('productos.index');
    }
}
