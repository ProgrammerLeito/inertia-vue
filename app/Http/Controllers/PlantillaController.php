<?php

namespace App\Http\Controllers;

use App\Models\Plantilla;
use Illuminate\Http\Request;

class PlantillaController extends Controller
{
    public function store(Request $request){

        $validateData=$request->validate([
            'descripcion'=>'required',
        ]);
        $plantilla=Plantilla::create($validateData);
        return redirect()->back();
    }

    public function destroy(Plantilla $plantilla){
        $plantilla->delete();
        return redirect()->back();
    }
}
