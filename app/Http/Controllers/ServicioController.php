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
        $clientes = Cliente::all();
        $datos = Dato::all();
        $users=User::all();
        if (request()->has('cliente_id')) {
            $datos = Dato::where('cliente_id', request()->cliente_id)->get();
        }
 
        return Inertia::render('Servicios/Create', compact('clientes','datos','users'));
    }
 
    public function store(CreateServicioRequest $request){
        $tecnico = Auth::user()->name;
        $validatedData= $request->validated();
        $validatedData['tecnico'] = $tecnico;
        $validatedData['estado'] = 'Pendiente';
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
 
    public function show(Servicio $servicio)
    {
        $servicio->load('user');
        $usuarioAutenticado = Auth::user();
 
        return Inertia::render('Servicios/Index', compact('servicio', 'usuarioAutenticado'));
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
            'estado' => 'required|in:Visitado,Cotizado,Pendiente,Finalizado',
        ]);
 
        // Obtener el servicio
        $servicio = Servicio::find($request->servicio_id);
 
        // Asignar el nuevo estado al servicio
        $servicio->estado = $request->estado;
        $servicio->save();
 
        // return response()->json(['message' => 'Estado del servicio cambiado con éxito'], 200);
    }
}