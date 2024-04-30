<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDatoRequest;
use App\Models\Cliente;
use App\Models\Dato;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DatoController extends Controller
{
    public function index(Request $request)
    {
        $cliente_id = $request->input('cliente_id');
   
        $datos = Dato::where('cliente_id', $cliente_id)->get();
   
        $clientes = Cliente::all();
   
        return Inertia::render('Datos/Index', compact('datos', 'clientes'));
    }

    public function store(CreateDatoRequest $request)
    {
        $validatedData = $request->validated();
        $dato = Dato::create($validatedData);
        return redirect()->back();
    }

    public function update(Request $request, Dato $dato)
    {
        $dato->update($request->all());
        return redirect()->back();
    }
 
    public function destroy($id)
    {
        $dato = Dato::find($id);
        $dato->delete();
        return redirect()->back();
    }
}
