<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDatoRequest;
use App\Http\Requests\updateDatoRequest;
use App\Models\Cliente;
use App\Models\Dato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $validatedData = $request->except(['tarjeta']);
        if ($request->hasFile('tarjeta')) {
            $tarjeta = $request->file('tarjeta');
            $routeName = $tarjeta->store('cliente', ['disk' => 'public']);
            $validatedData['tarjeta'] = $routeName;
        }
        $dato = Dato::create($validatedData);
        return redirect()->back();
    }
 
    public function edit(Dato $dato){
        $clientes=Cliente::all();
        return Inertia::render('Datos/Edit',compact('clientes','dato'));
    }

    public function update(updateDatoRequest $request, Dato $dato)
    {
        $validatedData = $request->except(['tarjeta']);
        if ($request->hasFile('tarjeta')) {
            $file = $request->file('tarjeta');
            $routeName = $file->store('cliente', ['disk' => 'public']);
            $validatedData['tarjeta'] = $routeName;

            if ($dato->tarjeta) {
                Storage::disk('public')->delete($dato->tarjeta);
            }
        }
        $dato->update($validatedData);
        return redirect()->route('datos.index', ['cliente_id' => $dato->cliente_id]);
    }
 
    public function destroy($id)
    {
        $dato = Dato::find($id);
        if ($dato->tarjeta) {
            Storage::disk('public')->delete($dato->tarjeta);
        }
        $dato->delete();
        return redirect()->back();
    }
}
