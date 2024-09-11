<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Inertia\Inertia;

class ServicioController extends Controller
{
    public function index(){
        $servicios = Servicio::with('cliente','user')->orderBy('id','DESC')->paginate(25);
        return Inertia::render('Servicios/Index',compact('servicios'));
    }

    public function destroy(Servicio $servicio){
        $servicio->delete();
        return redirect()->back();
    }

}
