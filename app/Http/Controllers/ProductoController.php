<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Inertia\Response;
use App\Models\Category;

class ProductoController extends Controller
{
    const Numero_de_items_pagina =25;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');

        $query = Producto::query()->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $productos = $query->paginate(self::Numero_de_items_pagina);

        return inertia('Productos/Index', [
            'productos' => $productos,
            'selectedCategoryId' => $categoryId,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $productos = Producto::all();
        return inertia('Productos/Create', ['categories' => $categories, 'productos' => $productos]);
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
            'category_id' => 'required',
        ]);
    
        Producto::create($validated);

        return redirect()->route('categories.index');
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
    public function destroy(Producto $productos, Category $categories)
    {
        $productos->delete();
        $categories->delete();
        return redirect()->route('productos.index');
    }
}
