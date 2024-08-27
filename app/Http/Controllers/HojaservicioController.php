<?php

namespace App\Http\Controllers;

use App\Http\Requests\hojaservicioRequest;
use App\Models\Hojaservicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HojaservicioController extends Controller
{
    public function index(Request $request) {
        $fecha = $request->input('fecha', now()->toDateString()); // Obtiene la fecha de la solicitud o la fecha actual por defecto
        $hojaservicios = Hojaservicio::whereDate('fecha', $fecha) // Filtra por la fecha seleccionada
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        return Inertia::render('Hojasservicios/Index', compact('hojaservicios', 'fecha'));
    }    

    public function store(hojaservicioRequest $request){
        $usuario = Auth::user()->name;
        $data=$request->validated();
        $data['usuario'] = $usuario;
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
