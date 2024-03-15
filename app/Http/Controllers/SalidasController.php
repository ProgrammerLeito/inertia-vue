<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

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
            $query->where('producto_id', $productoId);
        }

        $salidas = DB::table('salidas')
                        ->join('users', 'users.id', '=', 'salidas.tecnico')
                        ->join('productos', 'productos.id', '=', 'salidas.producto_id')
                        ->select(
                            'salidas.id',
                            'salidas.empresa',
                            'salidas.unidad_salida',
                            'salidas.comentario_salida',
                            'users.name',
                            'salidas.fecha',
                            'productos.insumo'
                        )
                        ->where('salidas.producto_id', '=', $productoId)
                        ->paginate(self::Numero_de_items_pagina);

        $salidas->appends(['producto_id' => $productoId]);

        return inertia('Salidas/Index', [
            'salidas' => $salidas,
            'selectedProductoId' => $productoId,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $salidas = Salida::all();
        $productos = Producto::all();

        $tecnico_salidas = DB::table('users')
            ->select('id','name','password')
            ->get();
            
        return inertia('Salidas/Create', ['salidas' => $salidas, 'tecnico_salidas' => $tecnico_salidas, 'productos' => $productos]);
    }

    public function comprobarSalida(Request $request)
    {
        $idTec = $request->input('id');
        $passwordconfirmacion = $request->input('passwordconfirmacion');

        // Obtener el usuario por su ID
        $usuario = User::find($idTec);

        if (!$usuario) {
            return response()->json(['siexisteusuario' => false]);
        }

        // Verificar la contraseña utilizando Hash::check()
        $contraseñaCorrecta = Hash::check($passwordconfirmacion, $usuario->password);

        return response()->json(['siexisteusuario' => $contraseñaCorrecta]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData  = $request->validate([
            'empresa' => 'required',
            'unidad_salida' => 'required',
            'comentario_salida' => 'required',
            'tecnico' => 'required',
            'fecha' => 'required',
            'producto_id' => 'required',
        ]);

        // Encuentra el producto asociado a la salida
        $producto = Producto::findOrFail($validatedData['producto_id']);

        Salida::create($validatedData);
        return redirect()->route('salidas.index', ['producto_id' => $producto->id]);
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

        $tecnico_salidas = DB::table('users')
            ->select('id','name','password')
            ->get();

        return inertia('Salidas/Edit', ['salidas' => $salida, 'productos' => $producto, 'tecnico_salidas' => $tecnico_salidas]);
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
            'tecnico' => 'required|integer',
            'fecha' => 'required',
            'producto_id' => 'required',
        ]);

        // Inicia una transacción para asegurar la integridad de los datos
        DB::beginTransaction();
        try {

            // dd($validated);
            // Actualiza los datos de la salida
            $salida->update($validated);

            // Encuentra el producto asociado a la salida
            $producto = Producto::findOrFail($validated['producto_id']);

            // Confirma los cambios en la base de datos
            DB::commit();

            return redirect()->route('salidas.index', ['producto_id' => $producto->id])->with('success', 'Salida actualizada y stock actualizado correctamente.');
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
    
            // Elimina la salida
            $salida->delete();
    
            // Confirma los cambios en la base de datos
            DB::commit();
    
            return redirect()->route('salidas.index', ['producto_id' => $producto->id])->with('success', 'Salida eliminada y stock restaurado correctamente.');
        } catch (\Exception $e) {
            // Revierte los cambios en caso de error
            DB::rollBack();
            return redirect()->route('salidas.index')->withErrors('Error al eliminar la salida: ' . $e->getMessage());
        }
    }
}
