<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function store(Request $request){
        $data=$request->validate([
            'descripcion'=>'required',
        ]);
        $documento=Documento::create($data);
        return redirect()->back();
    }
    public function update(Request $request, Documento $documento)
    {
        $validatedData = $request->validate([
            'descripcion' => ''
        ]);

        $documento->update($validatedData);

        return redirect()->back();
    }

    public function destroy(Documento $documento){
        $documento->delete();
        return redirect()->back();
    }
}
