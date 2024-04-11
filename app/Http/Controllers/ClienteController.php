<?php
 
namespace App\Http\Controllers;
 
use App\Models\Cliente;
use App\Models\Tbprovincias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
 
class ClienteController extends Controller
{
    const Numero_de_items_pagina =25;
    public function index()
    {
        $tbprovincias = Tbprovincias::all();
        $clientes = DB::table('clientes')
                        ->join('tbprovincias', 'clientes.prov_clientes', '=', 'tbprovincias.id')
                        ->select(
                            'clientes.id',
                            'clientes.numeroDocumento',
                            'clientes.razonSocial',
                            'clientes.direccion',
                            'tbprovincias.prov_nombre as prov_cliente'
                        )
                        ->paginate(self::Numero_de_items_pagina);

        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'tbprovincias' => $tbprovincias
        ]);
    }
 
    public function create()
    {
        $tbprovincias = Tbprovincias::all();
        return inertia::render('Clientes/Create', ['tbprovincias' => $tbprovincias]);
    }
   
 
    public function store(Request $request)
    {
        $request->validate([
            'numeroDocumento' => 'required|string',
            'razonSocial' => 'required|string',
            'direccion' => 'required|string',
            'distrito' => 'required|string',
            'provincia' => 'required|string',
            'departamento' => 'required|string',
            'estado' => 'required|string',
            'cli_direccion2' => 'required',
            'cli_observacion' => 'required|string',
            'prov_clientes' => 'required',
        ]);
   
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
            'prov_clientes' => $request->input('prov_clientes'),
        ]);
   
        // Redireccionar a alguna vista después de crear la inscripción
        return redirect()->route('clientes.index')->with('success', 'clientes creada exitosamente.');
    }
 
 
   
    public function show(string $id)
    {
        //
    }
 
   
    public function edit(string $id)
    {
        $tbprovincias = Tbprovincias::all();
        $cliente = Cliente::findOrFail($id);
        return inertia('Clientes/Edit', ['cliente' => $cliente, 'tbprovincias' => $tbprovincias]);
    }
 
 
    public function update(Request $request, string $id)
    {
        // Validar los datos del formulario de edición
        $validatedData = $request->validate([
            'numeroDocumento' => 'required',
            'razonSocial' => 'required',
            'direccion' => 'required',
            'distrito' => 'required',
            'provincia' => 'required',
            'departamento' => 'required',
            'estado' => 'required',
            'cli_direccion2' => 'required',
            'cli_observacion' => 'required',
            'prov_clientes' => 'required',
        ]);

        // Buscar el cliente en la base de datos
        $cliente = Cliente::findOrFail($id);

        // Actualizar los datos del cliente con los datos validados del formulario
        $cliente->update($validatedData);

        // Redireccionar a la vista de la lista de clientes con un mensaje de éxito
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }
 
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('clientes')
        ->where('idCliente', $id)
        ->update(['estado' => 0]);
 
        return redirect()->route('clientes.index')->with('success', 'Estado del cliente actualizado correctamente.');
    }
   
}