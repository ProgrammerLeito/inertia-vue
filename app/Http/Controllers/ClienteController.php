<?php
 
namespace App\Http\Controllers;

use App\Http\Requests\CreateClientesRequest;
use App\Models\Cliente;
use App\Models\Tbprovincias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
 
class ClienteController extends Controller
{
 
    public function index()
    {
        $clientes = Cliente::with('tbprovincia')->select('id', 'numeroDocumento', 'razonSocial', 'direccion','tbprovincia_id')->paginate(10);
        $tbprovincias = Tbprovincias::all();
    
        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'tbprovincias' => $tbprovincias
        ]);
    }

    public function trashed_cliente(Request $request)
    {
       
        $clientes = Cliente::onlyTrashed()->with('tbprovincia')->select('id', 'numeroDocumento', 'razonSocial', 'direccion','tbprovincia_id')->paginate(10);
        $tbprovincias = Tbprovincias::all();
 
        return Inertia::render('Clientes/Trash_list', [
            'clientes' => $clientes,
            'tbprovincias' => $tbprovincias
        ]);
    }

    public function restore($id){
        $cliente= Cliente::withTrashed()->findOrFail($id);
        if(!empty($cliente)){
            $cliente->restore();
        }
        return redirect()->back();
    }

    public function deletePermanently($id){
        $cliente= Cliente::withTrashed()->findOrFail($id);
 
        if(!empty($cliente)){
            $cliente->forceDelete();
        }
        return redirect()->back();
    }

    public function create()
    {
        $tbprovincias = Tbprovincias::all();
        return inertia::render('Clientes/Create', ['tbprovincias' => $tbprovincias]);
    }

    public function store(CreateClientesRequest $request)
    {
        $token = 'apis-token-7907.K0qLm91OLHYP07iBLCqF4INtKqqtu0H6'; // Reemplaza con tu token
        $ruc = $request->input('numeroDocumento');
   
        // Iniciar llamada a API con cURL
        $curl = curl_init();
   
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.apis.net.pe/v2/sunat/ruc?numero=' . $ruc,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Referer: http://apis.net.pe/api-ruc',
                'Authorization: Bearer ' . $token
            ),
        ));
   
        $response = curl_exec($curl);
   
        curl_close($curl);
   
        // Decodificar respuesta JSON
        $empresa = json_decode($response);
   
        // Crear una nueva inscripción con los datos obtenidos de la API
        Cliente::create([
            'numeroDocumento' => $empresa->numeroDocumento,
            'razonSocial' => $empresa->razonSocial,
            'direccion' => $empresa->direccion,
            'distrito' => $empresa->distrito,
            'provincia' => $empresa->provincia,
            'departamento' => $empresa->departamento,
            'estado' => $empresa->estado,
            'cli_direccion2' => $request->input('cli_direccion2'),
            'cli_observacion' => $request->input('cli_observacion'),
            'tbprovincia_id' => $request->input('tbprovincia_id'),
        ]);
   
        return redirect()->route('clientes.index')->with('success', 'clientes creada exitosamente.');
    }
   
    public function edit(Cliente $cliente)
    {
        $tbprovincias = Tbprovincias::all();
        return inertia('Clientes/Edit', ['cliente' => $cliente, 'tbprovincias' => $tbprovincias]);
    }
 
    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($request->all());
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }
 
    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        $cliente->delete();
        return redirect()->back();
    }
   
}