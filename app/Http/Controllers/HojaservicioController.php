<?php

namespace App\Http\Controllers;

use App\Http\Requests\hojaservicioRequest;
use App\Models\Hojaservicio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HojaservicioController extends Controller
{
    public function index (){

        $hojaservicios=Hojaservicio::all();
        return Inertia::render('Hojasservicios/Index',compact('hojaservicios'));
        
    }

    public function store(hojaservicioRequest $request){

        $data=$request->validated();
        Hojaservicio::create($data);
        return redirect()->back();

    }
}
