<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Dato;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DatoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cliente_id = $request->input('cliente_id');
   
        $datos = Dato::where('cliente_id', $cliente_id)->get();
   
        $clientes = Cliente::all();
   
        return Inertia::render('Datos/Index', [
            'datos' => $datos,
            'clientes' => $clientes
        ]);
    }
   
 
 
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string',
                'cargo' => 'required|string',
                'telefono' => 'required|string',
                'correo' => 'required|string',
                'tarjeta' => 'nullable|string',
                'cliente_id' => [
                    'required',
                    Rule::exists('clientes', 'id'),
                ],
            ]);
   
            $dato = Dato::create($validatedData);
   
            return redirect()->back()->with('success', 'Dato creado exitosamente.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el dato: ' . $e->getMessage());
        }
    }
 
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string',
                'cargo' => 'required|string',
                'telefono' => 'required|string',
                'correo' => 'required|string',
                'tarjeta' => 'nullable|string',
                'cliente_id' => [
                    'required',
                    Rule::exists('clientes', 'id'),
                ],
            ]);
 
            $dato = Dato::findOrFail($id);
 
            $dato->update($validatedData);
 
            return redirect()->back()->with('success', 'Dato actualizado exitosamente.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el dato: ' . $e->getMessage());
        }
    }
 
    public function destroy($id)
    {
        try {
            $dato = Dato::findOrFail($id);
            $dato->delete();
           
            return redirect()->back()->with('success', 'Dato eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el dato: ' . $e->getMessage());
        }
    }
}
