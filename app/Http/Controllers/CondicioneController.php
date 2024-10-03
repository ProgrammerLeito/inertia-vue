<?php

namespace App\Http\Controllers;

use App\Models\Condicione;
use Illuminate\Http\Request;

class CondicioneController extends Controller
{
    public function store(Request $request){
        $data=$request->validate([
            'descripcion'=>'required',
            'categoria'=>'required'
        ]);
        $condicione=Condicione::create($data);
        return redirect()->back();
    }


    public function update(Request $request, Condicione $condicione)
    {
        $validatedData = $request->validate([
            'categoria' => '',
            'descripcion' => ''
        ]);

        $condicione->update($validatedData);

        return redirect()->back();
    }

    public function destroy(Condicione $condicione){
        $condicione->delete();
        return redirect()->back();
    }
}
