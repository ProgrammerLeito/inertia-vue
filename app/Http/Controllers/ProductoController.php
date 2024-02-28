<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Inertia\Response;
use App\Models\Category;
use App\Http\Requests\ProductoRequest;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    const Numero_de_items_pagina =25;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');

        $productos = DB::table('categories')
                        ->join('productos', 'categories.id', '=', 'productos.category_id')
                        ->select(
                            'categories.id',
                            'categories.name',
                            'productos.id as producto_id', // Asegúrate de incluir el id del producto si es necesario
                            'productos.insumo',
                            'productos.marca',
                            'productos.modelo',
                            'productos.cantidad',
                            'productos.unidad_medida',
                            'productos.fecha',
                            'productos.empresa',
                            'productos.comentario',
                            'productos.stock',
                            'productos.ultima_entrada',
                            'productos.category_id',
                            DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM entradas WHERE entradas.producto_id = productos.id) as total_entradas'),
                            DB::raw('(SELECT COALESCE(SUM(unidad_salida), 0) FROM salidas WHERE salidas.producto_id = productos.id) as total_salidas'),
                            DB::raw('(SELECT COALESCE((SELECT cantidad FROM entradas WHERE entradas.producto_id = productos.id ORDER BY id DESC LIMIT 1), 0)) as ultima_cantidad_entrada')
                        )
                        ->where('categories.id', '=', $categoryId)
                        ->paginate(self::Numero_de_items_pagina);

        $productos->appends(['category_id' => $categoryId]);

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
    public function store(ProductoRequest $request)
    {
        $producto = Producto::create($request->validated());

        return redirect()->route('productos.index', ['category_id' => $producto->category_id]);
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
    public function edit(Producto $producto)
    {
        $category = Category::all();
        return inertia('Productos/Edit', ['productos' => $producto, 'categories' => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $producto->update($request->all());

        return redirect()->route('productos.index', ['category_id' => $request->category_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $category_id = $producto->category_id; // Asumiendo que el producto tiene un 'category_id'
        $producto->delete();

        return redirect()->route('productos.index', ['category_id' => $category_id]);
    }
}
