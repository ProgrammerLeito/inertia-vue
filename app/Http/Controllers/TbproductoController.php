<?php
 
namespace App\Http\Controllers;

use App\Http\Requests\CreateTbproductoRequest;
use App\Http\Requests\UpdateTbproductoRequest;
use App\Models\Tbcategoria;
use App\Models\Tbmarca;
use App\Models\Tbproducto;
use App\Models\Tbsubcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
 
class TbproductoController extends Controller
{
    public function index()
    {
        $tbproductos=Tbproducto::with('tbcategoria','tbsubcategoria','tbmarca')->paginate(8);
        $tbcategorias=Tbcategoria::with('tbsubcategorias')->get();
        $tbsubcategorias=Tbsubcategoria::all();
        $tbmarcas=Tbmarca::all();
        // dd($tbproductos);
        return Inertia::render('Catalogo/Index', [
            'tbproductos' => $tbproductos,
            'tbcategorias'=>$tbcategorias,
            'tbsubcategorias'=>$tbsubcategorias,
            'tbmarcas'=>$tbmarcas
        ]);
    }

    public function trashed_tbproducto(Request $request)
    {
        $tbcategorias=Tbcategoria::with('tbsubcategorias')->get();
        $tbsubcategorias=Tbsubcategoria::all();
        $tbmarcas=Tbmarca::all();
        $tbproductos=Tbproducto::onlyTrashed()->with('tbcategoria','tbsubcategoria','tbmarca')->paginate(8);
        // dd($tbproductos);
        return Inertia::render('Catalogo/Trash_list', [
            'tbproductos' => $tbproductos,
            'tbcategorias'=>$tbcategorias,
            'tbsubcategorias'=>$tbsubcategorias,
            'tbmarcas'=>$tbmarcas
        ]);
    }
 
    public function restore($id){
        $tbproducto= Tbproducto::withTrashed()->findOrFail($id);
        if(!empty($tbproducto)){
            $tbproducto->restore();
        }
        return redirect()->back();
    }
 
    public function deletePermanently($id){
        $tbproducto= Tbproducto::withTrashed()->findOrFail($id);
 
        // Eliminar la imagen si existe
        if (file_exists(public_path('img/catalogo/' . $tbproducto->foto))) {
            unlink(public_path('img/catalogo/' . $tbproducto->foto));
        }
 
        if(!empty($tbproducto)){
            $tbproducto->forceDelete();
        }
        return redirect()->back();
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
 
    public function store(CreateTbproductoRequest $request)
    {
        $validatedData = $request->validated();
        $tbproducto = Tbproducto::create($validatedData);
 
        if($request->hasFile('foto')){
            $file = $request->foto;
            $file->move(public_path(). '/img/catalogo', $file->getClientOriginalName());
            $tbproducto->foto = $file->getClientOriginalName();
        }
        $tbproducto->save();
        return redirect()->route('tbproductos.index');
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
 
    public function update(UpdateTbproductoRequest $request, $id)
    {
        // dd($request->all());
        $validatedData = $request->validated();
   
        $tbproducto = Tbproducto::find($id);
   
        if ($request->hasFile('foto')) {
            if ($tbproducto->foto) {
                Storage::delete('/img/catalogo/' . $tbproducto->foto);
            }
   
            $fileName = $request->file('foto')->getClientOriginalName();
            $request->file('foto')->storeAs('/img/catalogo', $fileName);
            $tbproducto->foto = $fileName;
        }
        $tbproducto->update($validatedData);
   
        return redirect()->route('tbproductos.index');
    }
    
    public function destroy($id)
    {
        $tbproducto = Tbproducto::find($id);
        $tbproducto->delete();
        return redirect()->back();
        //
    }
   
}