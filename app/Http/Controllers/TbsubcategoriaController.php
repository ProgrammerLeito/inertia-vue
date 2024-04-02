<?php

namespace App\Http\Controllers;

use App\Models\Tbsubcategoria;
use Illuminate\Http\Request;

class TbsubcategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tbcategoria_id' => 'required',
            'nombre' => 'required',
        ]);
   
        $tbsubcategoria = new Tbsubcategoria();
        $tbsubcategoria->tbcategoria_id = $request->tbcategoria_id;
        $tbsubcategoria->nombre = $request->nombre;
        $tbsubcategoria->save();
   
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Tbsubcategoria $tbsubcategoria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tbsubcategoria $tbsubcategoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tbsubcategoria $tbsubcategoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tbsubcategoria $tbsubcategoria)
    {
        //
    }
}
