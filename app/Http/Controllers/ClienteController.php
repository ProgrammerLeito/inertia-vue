<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use App\Models\Tbprovincias;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{

    public function index()
    {
        $clientes = Cliente::with('tbprovincia')->orderBy('id', 'DESC')->select('id', 'numeroDocumento', 'razonSocial', 'direccion','tbprovincia_id','ctg','asesor')->paginate(25);
         $tbprovincias = Tbprovincias::all();
         $totalClientes = Cliente::count();
          return Inertia::render('Clientes/Index', compact('clientes','tbprovincias','totalClientes'));
    }

    public function create()
    {
        $tbprovincias = Tbprovincias::all();
        return inertia::render('Clientes/Create', compact('tbprovincias'));
    }

    public function store(ClienteRequest $request)
    {
        $asesor = Auth::user()->name;

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
            'asesor' => $asesor,
            'ctg' => 'Sin Informacion',
        ]);

        return redirect()->route('clientes.index');
    }


    public function edit(Cliente $cliente)
    {
        $tbprovincias = Tbprovincias::all();
        return inertia('Clientes/Edit',compact('cliente','tbprovincias'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($request->all());
        return redirect()->route('clientes.index');
    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        $cliente->delete();
        return redirect()->back();
    }


    public function updateCtg(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'ctg' => 'required|in:Vip,Potencial,Regular,Sin Informacion', // Sin espacios en los valores 'Cliente Potencial' y 'Cliente Regular'
        ]);

        // Obtener el cliente
        $cliente = Cliente::find($request->cliente_id);

        // Asignar el nuevo estado al cliente
        $cliente->ctg = $request->ctg;
        $cliente->save();

        // return response()->json(['message' => 'Estado del cliente cambiado con éxito'], 200);
    }


}
