<?php

namespace App\Http\Controllers;

use App\Http\Requests\hojaservicioRequest;
use App\Models\Cuadropendientes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PendientesController extends Controller
{
    public function index(Request $request) {
        $fecha = $request->input('fecha', Carbon::now()->setTimezone('America/Lima')->toDateString()); // Ajusta la fecha a la zona horaria de Lima
        $tb_pendientes = Cuadropendientes::whereDate('fecha', $fecha) // Filtra por la fecha seleccionada
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        return Inertia::render('CuadroPendientes/Index', compact('tb_pendientes', 'fecha'));
    }  

    public function store(hojaservicioRequest $request){
        $usuario = Auth::user()->name;
        $data=$request->validated();
        $data['usuario'] = $usuario;
        $data['created_at'] = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        $data['updated_at'] = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        Cuadropendientes::create($data);
        return redirect()->back();
    }

    public function update(hojaservicioRequest $request , $id){
        $data=$request->validated();
        $cuadropendientes=Cuadropendientes::find($id);
        $cuadropendientes->update($data);
    }

    public function destroy($id)
    {
        $cuadropendientes = Cuadropendientes::find($id);
        $cuadropendientes->delete();
        return redirect()->back();
    }
}
