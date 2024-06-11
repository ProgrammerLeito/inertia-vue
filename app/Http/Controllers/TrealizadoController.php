<?php

namespace App\Http\Controllers;

use App\Models\Trealizado;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrealizadoController extends Controller
{
    public function store(Request $request){
        $validateData= $request->validate([
            'ctrabajo_id'=>'required',
            'descripcion'=>'required'
        ]);
        $trealizado=Trealizado::create($validateData);
        return redirect()->back();
    }
    public function update(Request $request, Trealizado $trealizado)
    {
        $validatedData = $request->validate([
            'ctrabajo_id' => '',
            'descripcion' => ''
        ]);

        // Actualiza los campos del modelo con los datos validados
        $trealizado->update($validatedData);

        return redirect()->back();
    }

    public function destroy(Trealizado $trealizado){
        $trealizado->delete();
        return redirect()->back();
    }
}
