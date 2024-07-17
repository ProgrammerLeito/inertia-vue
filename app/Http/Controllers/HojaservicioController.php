<?php

namespace App\Http\Controllers;

use App\Http\Requests\hojaservicioRequest;
use App\Models\Hojaservicio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HojaservicioController extends Controller
{
    public function index (){
        $hojaservicios = Hojaservicio::orderBy('created_at', 'desc')->get();
        return Inertia::render('Hojasservicios/Index',compact('hojaservicios'));
    }

    public function store(hojaservicioRequest $request){
        $data=$request->validated();
        Hojaservicio::create($data);
        return redirect()->back();
    }

    public function update(hojaservicioRequest $request , $id){
        $data=$request->validated();
        $hojaservicio=Hojaservicio::find($id);
        $hojaservicio->update($data);
    }

    public function destroy($id)
    {
        $hojaservicio = Hojaservicio::find($id);
        $hojaservicio->delete();
        return redirect()->back();
    }
}
