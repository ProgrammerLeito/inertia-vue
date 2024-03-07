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
    public function edit(Cliente $cliente)
    {
        return inertia('Clientes/Edit', ['cliente' => $cliente]);
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
