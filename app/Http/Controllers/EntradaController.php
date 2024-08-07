<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class EntradaController extends Controller
{
    const Numero_de_items_pagina =50;
    public function index(Request $request)
    {
        $productos = Producto::all();
        $productoId = $request->query('producto_id');

        $query = Entrada::query()->with('producto');

        if ($productoId) {
            $query->where('producto_id', $productoId); 
        }

        $entradas = $query->paginate(self::Numero_de_items_pagina);

        return inertia('Entradas/Index', [
            'entradas' => $entradas,
            'selectedProductoId' => $productoId, // Pasamos el 'producto_id' seleccionado para poder hacer algo con él en la vista si es necesario
            'productos' => $productos,
        ]);
    }

    public function create()
    {
        $entradas = Entrada::all();
        $productos = Producto::all();
        return inertia('Entradas/Create', ['entradas' => $entradas, 'productos' => $productos]);
    }

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

        // $producto = Producto::findOrFail($request->producto_id);
        // $producto->fecha = $request->fecha;
        // $producto->save();
    
        return redirect()->route('entradas.index')
                         ->with('success', 'Entrada creada exitosamente.');
    }

    public function edit(Entrada $entrada)
    {
        $producto = Producto::all();
        return inertia('Entradas/Edit', ['entradas' => $entrada, 'productos' => $producto]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'producto_id' => 'required',
            'cantidad' => 'required|numeric',
            'fecha' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $entradaActual = DB::table('entradas')->where('id', $id)->first();

            if (!$entradaActual) {
                throw new \Exception("Entrada no encontrada.");
            }

            // Actualizar la entrada
            DB::table('entradas')->where('id', $id)->update([
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad,
                'fecha' => $request->fecha,
            ]);

            DB::commit();

            return redirect()->route('entradas.index')->with('success', 'Entrada actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Error al procesar la actualización de la entrada: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        // Encuentra la entrada a eliminar
        $entrada = Entrada::findOrFail($id);
    
        // Elimina la entrada
        $entrada->delete();
    
        return redirect()->route('entradas.index')
                         ->with('success', 'Entrada eliminada exitosamente.');
    }
}
