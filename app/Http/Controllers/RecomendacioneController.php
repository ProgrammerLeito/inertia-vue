<?php

namespace App\Http\Controllers;

use App\Models\Recomendacione;
use Illuminate\Http\Request;

class RecomendacioneController extends Controller
{
    public function store(Request $request){
        $data=$request->validate([
            'descripcion'=>'required',
        ]);
        $recomendacione=Recomendacione::create($data);
        return redirect()->back();
    }
    public function update(Request $request, Recomendacione $recomendacione)
    {
        $validatedData = $request->validate([
            'descripcion' => ''
        ]);

        $recomendacione->update($validatedData);

        return redirect()->back();
    }

    public function destroy(Recomendacione $recomendacione){
        $recomendacione->delete();
        return redirect()->back();
    }
}
