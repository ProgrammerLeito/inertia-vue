<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class EntradaController extends Controller
{
    const Numero_de_items_pagina =25;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productoId = $request->query('producto_id');

        $query = Entrada::query()->with('producto');

        if ($productoId) {
            $query->where('producto_id', $productoId); 
        }

        $entradas = $query->paginate(self::Numero_de_items_pagina);

        return inertia('Entradas/Index', [
            'entradas' => $entradas,
            'selectedProductoId' => $productoId, // Pasamos el 'producto_id' seleccionado para poder hacer algo con él en la vista si es necesario
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $entradas = Entrada::all();
        $productos = Producto::all();
        return inertia('Entradas/Create', ['entradas' => $entradas, 'productos' => $productos]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required',
            'cantidad' => 'required|numeric',
            'fecha' => 'required|date',
        ]);
    
        $entrada = new Entrada();
        $entrada->producto_id = $request->producto_id;
        $entrada->cantidad = $request->cantidad;
        $entrada->fecha = $request->fecha;
        $entrada->save();
    
        // Actualizar la cantidad de productos reemplazando la cantidad existente
        $producto = Producto::find($request->producto_id);
        $producto->cantidad = $request->cantidad; // Reemplazar la cantidad
        $producto->stock = $producto->cantidad + $producto->stock; // Sumar al stock existente
        $producto->save();
    
        return redirect()->route('entradas.index')
                         ->with('success', 'Entrada creada exitosamente.');
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
    public function edit(Entrada $entrada)
    {
        $producto = Producto::all();
        return inertia('Entradas/Edit', ['entradas' => $entrada, 'productos' => $producto]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'cantidad' => 'required|numeric',
            'fecha' => 'required|date',
        ]);
    
        $entrada = Entrada::findOrFail($id);
        $oldCantidad = $entrada->cantidad; // Guarda la cantidad anterior antes de actualizar
    
        $entrada->cantidad = $request->cantidad;
        $entrada->fecha = $request->fecha;
        $entrada->save();
    
        // Actualizar la cantidad de productos y su stock
        $producto = Producto::find($entrada->producto_id);
        $producto->cantidad = $producto->cantidad - $oldCantidad + $request->cantidad; // Resta la cantidad anterior y suma la nueva cantidad
        $producto->stock = $producto->stock - $oldCantidad + $request->cantidad; // Actualiza el stock
        $producto->save();
    
        return redirect()->route('entradas.index')
                         ->with('success', 'Entrada actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Encuentra la entrada a eliminar
        $entrada = Entrada::findOrFail($id);
    
        // Guarda la cantidad de la entrada antes de eliminarla
        $cantidad = $entrada->cantidad;
    
        // Actualiza la cantidad de productos y su stock correspondiente
        $producto = Producto::find($entrada->producto_id);
        $producto->cantidad -= $cantidad;
        $producto->stock -= $cantidad;
        $producto->save();
    
        // Elimina la entrada
        $entrada->delete();
    
        return redirect()->route('entradas.index')
                         ->with('success', 'Entrada eliminada exitosamente.');
    }
}
