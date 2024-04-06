<?php
 
namespace App\Http\Controllers;
 
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
 
class ClienteController extends Controller
{
    const Numero_de_items_pagina =25;
    public function index()
    {
        $clientes = DB::table('clientes')
                        ->select('id', 'numeroDocumento', 'razonSocial', 'direccion', 'provincia') // Selecciona los campos que desees
                        ->paginate(self::Numero_de_items_pagina);

        return Inertia::render('Clientes/Index', [
            'clientes' => Cliente::paginate(10)
        ]);
    }
 
    public function create()
    {
 
        return inertia::render('Clientes/Create');
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
        $cliente = Cliente::findOrFail($id);
        return inertia('Clientes/Edit', ['cliente' => $cliente]);
    }
 
 
    public function update(Request $request, string $id)
    {
        // Validar los datos del formulario de edición
        $validatedData = $request->validate([
            'cli_razonSocial' => 'required',
            'cli_ruc' => 'required',
            'cli_ciudad' => 'required',
            'cli_direccionlegal' => 'required',
            'cli_direccion1' => 'required',
            'cli_direccion2' => 'required',
            'cli_observacion' => 'required'
        ]);
 
        $cliente = Cliente::findOrFail($id);
 
        $cliente->update($validatedData);
 
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