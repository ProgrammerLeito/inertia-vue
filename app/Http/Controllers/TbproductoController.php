<?php

namespace App\Http\Controllers;

use App\Models\Tbcategoria;
use App\Models\Tbmarca;
use App\Models\Tbproducto;
use App\Models\Tbsubcategoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TbproductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tbcategorias=Tbcategoria::with('tbsubcategorias')->get();
        $tbsubcategorias=Tbsubcategoria::all();
        $tbmarcas=Tbmarca::all();
        $tbproductos=Tbproducto::with('tbcategoria','tbsubcategoria','tbmarca')->paginate(8);
        // dd($tbproductos);
        return Inertia::render('Catalogo/Index', [
            'tbproductos' => $tbproductos,
            'tbcategorias'=>$tbcategorias,
            'tbsubcategorias'=>$tbsubcategorias,
            'tbmarcas'=>$tbmarcas
        ]);
    }
 
   
    public function create()
    {
        $tbcategorias = Tbcategoria::all();
        $tbsubcategorias = Tbsubcategoria::all();
        $tbmarcas = Tbmarca::all();
 
        return Inertia::render('Catalogo/Create', [
            'tbcategorias' => $tbcategorias,
            'tbsubcategorias' => $tbsubcategorias,
            'tbmarcas' => $tbmarcas,
        ]);
    }
 
    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'tbcategoria_id' => 'required',
            'tbsubcategoria_id' => 'required',
            'tbmarca_id' => 'required',
            'modelo' => 'required',
            'medida' => 'required',
            'moneda' => 'required',
            'precio' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'codigo' => 'required|string|max:255|unique:tbproductos',
            'estado' => 'required',
            'capacidades' => 'required',
            'especificaciones' => 'required',
            'foto' => 'required',
        ]);
        $tbproducto =Tbproducto::create($validatedData);
 
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nombreArchivo = hash('sha256', time() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path().'/img/catalogo', $nombreArchivo);
            $tbproducto->foto = $nombreArchivo;
        }
        $tbproducto->save();
        return redirect()->route('tbproductos.index')->with('success', 'Producto creado exitosamente.');
    }
 
    public function edit(Tbproducto $tbproducto)
    {
        $tbmarcas = Tbmarca::all();
        $tbcategorias = Tbcategoria::all();
        $tbsubcategorias = Tbsubcategoria::all();
       
        return Inertia::render('Catalogo/Edit', [
            'tbproducto' => $tbproducto,
            'tbmarcas' => $tbmarcas,
            'tbcategorias' => $tbcategorias,
            'tbsubcategorias' => $tbsubcategorias
        ]);
    }
   
 
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'tbcategoria_id' => 'required',
            'tbsubcategoria_id' => 'required',
            'tbmarca_id' => 'required',
            'modelo' => 'required',
            'medida' => 'required',
            'moneda' => 'required',
            'precio' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'codigo' => 'required|string|max:255|unique:tbproductos,codigo,'.$id, // Asegúrate de excluir el registro actual del chequeo único
            'estado' => 'required',
            'capacidades' => 'required',
            'especificaciones' => 'required',
            'foto' => 'nullable', // La foto no es requerida en la edición
        ]);
 
        $tbproducto = Tbproducto::findOrFail($id); // Buscar el producto por su ID
 
        // Actualizar los datos del producto
        $tbproducto->update($validatedData);
 
        // Manejar la subida de la foto si se ha enviado
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nombreArchivo = hash('sha256', time() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path().'/img/catalogo', $nombreArchivo);
            $tbproducto->foto = $nombreArchivo;
            $tbproducto->save();
        }
 
        return redirect()->route('tbproductos.index')->with('success', 'Producto actualizado exitosamente.');
    }
    public function destroy($id)
    {
        $tbproducto = Tbproducto::find($id);
        unlink(public_path('img/catalogo/'.$tbproducto->foto));
        $tbproducto->delete();
        return redirect()->back();
        //
    }
}
