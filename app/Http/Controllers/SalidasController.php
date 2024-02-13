<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use Illuminate\Http\Request;
use App\Models\Producto;

class SalidasController extends Controller
{
    const Numero_de_items_pagina =25;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $salidaId = $request->query('salida_id');

        $query = Salida::query()->with('producto');

        if ($salidaId) {
            $query->where('salida_id', $salidaId);
        }

        $salidas = $query->paginate(self::Numero_de_items_pagina);

        return inertia('Salidas/Index', [
            'salidas' => $salidas,
            'selectedSalidaId' => $salidaId,
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
            'salida_id' => 'required',
        ]);
    
        Salida::create($validated);

        return redirect()->route('salidas.index');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
