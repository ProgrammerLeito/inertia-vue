<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    const Numero_de_items_pagina =25;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = DB::table('clientes')
                        ->select('idCliente', 'cli_razonSocial', 'cli_ruc', 'cli_direccionlegal', 'cli_observacion') // Selecciona los campos que desees
                        ->where('estado', '=', 1)
                        ->paginate(self::Numero_de_items_pagina);

        return inertia('Clientes/Index', ['clientes' => $clientes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::all();
        return inertia('Clientes/Create', ['clientes' => $clientes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData  = $request->validate([
            'cli_razonSocial' => 'required',
            'cli_ruc' => 'required',
            'cli_ciudad' => 'required',
            'cli_direccionlegal' => 'required',
            'cli_direccion1' => 'required',
            'cli_direccion2' => 'required',
            'cli_observacion' => 'required'
        ]);

        Cliente::create($validatedData);
        return redirect()->route('clientes.index');
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
        $cliente = Cliente::findOrFail($id);
        return inertia('Clientes/Edit', ['cliente' => $cliente]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validar los datos del formulario de edición
        $validatedData = $request->validate([
            'cli_razonSocial' => 'required',
            'cli_ruc' => 'required',
            'cli_ciudad' => 'required',
            'cli_direccionlegal' => 'required',
            'cli_direccion1' => 'required',
            'cli_direccion2' => 'required',
            'cli_observacion' => 'required'
        ]);

        $cliente = Cliente::findOrFail($id);

        $cliente->update($validatedData);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('clientes')
        ->where('idCliente', $id)
        ->update(['estado' => 0]);

        return redirect()->route('clientes.index')->with('success', 'Estado del cliente actualizado correctamente.');
    }
}
