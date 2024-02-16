<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class SalidasController extends Controller
{
    const Numero_de_items_pagina =25;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productoId = $request->query('producto_id');

        $query = Salida::query()->with('producto');

        if ($productoId) {
            $query->where('producto_id', $productoId); // Asegúrate de que 'producto_id' es el nombre correcto del campo en tu base de datos
        }

        $salidas = $query->paginate(self::Numero_de_items_pagina);

        return inertia('Salidas/Index', [
            'salidas' => $salidas,
            'selectedProductoId' => $productoId, // Pasamos el 'producto_id' seleccionado para poder hacer algo con él en la vista si es necesario
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $salidas = Salida::all();
        $productos = Producto::all();
        return inertia('Salidas/Create', ['salidas' => $salidas, 'productos' => $productos]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa' => 'required|string',
            'unidad_salida' => 'required|numeric',
            'comentario_salida' => 'required|string',
            'tecnico' => 'required|string',
            'fecha' => 'required',
            'producto_id' => 'required',
        ]);

        // Inicia una transacción para asegurar la integridad de los datos
        DB::beginTransaction();
        try {
            // Registra la salida
            $salida = Salida::create($validated);

            // Encuentra el producto y resta la cantidad
            $producto = Producto::findOrFail($validated['producto_id']);
            $producto->stock -= $validated['unidad_salida']; // Asegúrate de que 'cantidad' es el campo correcto para la cantidad en stock

            if ($producto->stock < 0) {
                // Opcional: Manejar casos donde la salida es mayor que el stock disponible
                throw new \Exception("La cantidad de salida no puede ser mayor que el stock disponible.");
            }

            $producto->save();

            // Confirma los cambios en la base de datos
            DB::commit();

            return redirect()->route('salidas.index')->with('success', 'Salida registrada y stock actualizado correctamente.');
        } catch (\Exception $e) {
            // Revierte los cambios en caso de error
            DB::rollBack();
            return redirect()->route('salidas.create')->withErrors('Error al registrar la salida: ' . $e->getMessage());
        }
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
    public function edit(Salida $salida)
    {
        $producto = Producto::all();
        return inertia('Salidas/Edit', ['salidas' => $salida, 'productos' => $producto]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salida $salida)
    {
        // Valida los datos recibidos en la solicitud
        $validated = $request->validate([
            'empresa' => 'required|string',
            'unidad_salida' => 'required|numeric',
            'comentario_salida' => 'required|string',
            'tecnico' => 'required|string',
            'fecha' => 'required',
            'producto_id' => 'required',
        ]);

        // Inicia una transacción para asegurar la integridad de los datos
        DB::beginTransaction();
        try {
            // Obtiene la cantidad original de la salida antes de la actualización
            $cantidad_original = $salida->unidad_salida;

            // Actualiza los datos de la salida
            $salida->update($validated);

            // Encuentra el producto asociado a la salida
            $producto = Producto::findOrFail($validated['producto_id']);

            // Calcula la diferencia entre la cantidad original y la nueva cantidad
            $diferencia_cantidad = $cantidad_original - $validated['unidad_salida'];

            // Actualiza la cantidad disponible del producto
            $producto->stock += $diferencia_cantidad;

            if ($producto->stock < 0) {
                // Opcional: Manejar casos donde la cantidad disponible del producto sea negativa
                throw new \Exception("La cantidad disponible del producto no puede ser negativa.");
            }

            $producto->save();

            // Confirma los cambios en la base de datos
            DB::commit();

            return redirect()->route('salidas.index')->with('success', 'Salida actualizada y stock actualizado correctamente.');
        } catch (\Exception $e) {
            // Revierte los cambios en caso de error
            DB::rollBack();
            return redirect()->route('salidas.edit', $salida)->withErrors('Error al actualizar la salida: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salida $salida)
    {
        // Inicia una transacción para asegurar la integridad de los datos
        DB::beginTransaction();
        try {
            // Encuentra el producto asociado a la salida
            $producto = Producto::findOrFail($salida->producto_id);
    
            // Restaura la cantidad de la salida al stock del producto
            $producto->stock += $salida->unidad_salida; // Asegúrate de que 'cantidad' es el campo correcto para la cantidad en stock
    
            // Guarda el producto
            $producto->save();
    
            // Elimina la salida
            $salida->delete();
    
            // Confirma los cambios en la base de datos
            DB::commit();
    
            return redirect()->route('salidas.index')->with('success', 'Salida eliminada y stock restaurado correctamente.');
        } catch (\Exception $e) {
            // Revierte los cambios en caso de error
            DB::rollBack();
            return redirect()->route('salidas.index')->withErrors('Error al eliminar la salida: ' . $e->getMessage());
        }
    }
}
