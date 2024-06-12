<?php

namespace App\Http\Controllers;

use App\Http\Requests\TbproductoRequest;
use App\Http\Requests\UpdateTbproductoRequest;
use App\Models\Tbcategoria;
use App\Models\Tbmarca;
use App\Models\Tbproducto;
use App\Models\Tbsubcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TbproductoController extends Controller
{

    public function index()
    {
        $tbproductos=Tbproducto::with('tbcategoria','tbsubcategoria','tbmarca')->orderBy('id', 'DESC')->paginate(8);
        $tbcategorias=Tbcategoria::with('tbsubcategorias')->get();
        $tbsubcategorias=Tbsubcategoria::all();
        $tbmarcas=Tbmarca::all();
        return Inertia::render('Catalogo/Index',compact('tbproductos','tbcategorias','tbsubcategorias','tbmarcas'));
    }

    public function create()
    {
        $tbcategorias = Tbcategoria::all();
        $tbsubcategorias = Tbsubcategoria::all();
        $tbmarcas = Tbmarca::all();

        return Inertia::render('Catalogo/Create', compact('tbcategorias','tbsubcategorias','tbmarcas'));
    }

    public function store(TbproductoRequest $request)
    {
        $data = $request->except('foto');

        if($request->hasFile('foto')){
            $file= $request->file('foto');
            $routeName= $file->store('fotos',['disk'=>'public']);
            $data['foto']=$routeName;
        }
        Tbproducto::create($data);
        return redirect()->route('tbproductos.index');
    }

    public function edit(Tbproducto $tbproducto)
    {
        $tbmarcas = Tbmarca::all();
        $tbcategorias = Tbcategoria::all();
        $tbsubcategorias = Tbsubcategoria::all();
        return Inertia::render('Catalogo/Edit', compact('tbproducto','tbmarcas','tbcategorias','tbsubcategorias'));
    }

    public function update(UpdateTbproductoRequest $request,Tbproducto $tbproducto)
    {
        $data = $request->except('foto');

        if($request->hasFile('foto')){
            $file= $request->file('foto');
            $routeName= $file->store('fotos',['disk'=>'public']);
            $data['foto']=$routeName;

            if($tbproducto->foto){
                Storage::disk('public')->delete($tbproducto->foto);
            }
        }
        $tbproducto->update($data);
        return redirect()->route('tbproductos.index');

    }


    public function destroy($id)
    {
        $tbproducto = Tbproducto::find($id);
        $tbproducto->delete();
        return redirect()->back();
    }

}
