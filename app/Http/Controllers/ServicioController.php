<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServicioRequest;
use App\Models\Cliente;
use App\Models\Dato;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{
    public function index(){
        $servicios = Servicio::with('cliente','user')->orderBy('id','DESC')->paginate(10);
        $users=User::all();
        $datos=Dato::all();
        return Inertia::render('Servicios/Index',compact('servicios','users','datos'));
    }


    public function create()
    {
        $ultimoServicio = Servicio::latest()->first();

        // Verificar si hay algún registro
        if ($ultimoServicio) {
            // Obtener el valor de n_cotizacion del último registro
            $nInforme = str_pad($ultimoServicio->n_informe + 1, 9, '0', STR_PAD_LEFT);
        } else {
            // En caso de que no haya registros, asignar un valor predeterminado
            $nInforme = '000000001'; // Puedes asignar cualquier valor aquí
        }
        $clientes = Cliente::all();
        $datos = Dato::all();
        $users=User::all();
        if (request()->has('cliente_id')) {
            $datos = Dato::where('cliente_id', request()->cliente_id)->get();
        }

        return Inertia::render('Servicios/Create', compact('clientes','datos','users','nInforme'));
    }

    public function store(CreateServicioRequest $request){
        $tecnico = Auth::user()->name;
        $validatedData= $request->validated();
        $validatedData['tecnico'] = $tecnico;
        $validatedData['estado'] = 'no atendido';
        $servicios= Servicio::create($validatedData);
        $servicios->save();
        return redirect()->route('servicios.index');
    }

    public function edit($id){
        $clientes=Cliente::all();
        $users=User::all();
        $servicio=Servicio::find($id);
        $datos= Dato::all();

        // Si hay un cliente seleccionado, filtra los datos relacionados con ese cliente
        if ($servicio->cliente_id) {
            $datos = Dato::where('cliente_id', $servicio->cliente_id)->get();
        }
        return Inertia::render('Servicios/Edit', compact('clientes','datos','users','servicio'));

    }

    public function update(CreateServicioRequest $request,$id){
        $datos=$request->validated();
        $servicio=Servicio::find($id);
        $servicio->update($datos);
        return redirect()->route('servicios.index');
    }

    public function destroy(Servicio $servicio){
        $servicio->delete();
        return redirect()->back();
    }


    public function cambiarEstado(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'estado' => 'required|in:atendido,no atendido',
        ]);

        // Obtener el servicio
        $servicio = Servicio::find($request->servicio_id);

        // Asignar el nuevo estado al servicio
        $servicio->estado = $request->estado;
        $servicio->save();

        // return response()->json(['message' => 'Estado del servicio cambiado con éxito'], 200);
    }

    public function show()
    {
        $servicio = Servicio::all();
        return Inertia::render('Servicios/Index', compact('servicio'));
    }

}
