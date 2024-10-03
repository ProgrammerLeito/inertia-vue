<?php

namespace App\Http\Controllers;

use App\Models\Observacione;
use Illuminate\Http\Request;

class ObservacioneController extends Controller
{

    public function store(Request $request){
        $data=$request->validate([
            'descripcion'=>'required',
            'categoria'=>'required'
        ]);
        $observacione=Observacione::create($data);
        return redirect()->back();
    }

    public function update(Request $request, Observacione $observacione)
    {
        $validatedData = $request->validate([
            'categoria' => '',
            'descripcion' => ''
        ]);

        $observacione->update($validatedData);

        return redirect()->back();
    }

    public function destroy(Observacione $observacione){
        $observacione->delete();
        return redirect()->back();
    }
}
