<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsultarRucController extends Controller
{
    public function ComprobarRuc(Request $request)
    {
        $token = 'apis-token-7907.K0qLm91OLHYP07iBLCqF4INtKqqtu0H6';
        $ruc = $request->input('ruc');

        // Iniciar llamada a API
        $curl = curl_init();

        // Buscar ruc sunat
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.apis.net.pe/v2/sunat/ruc?numero=' . $ruc,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
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
        // Datos de empresas según padron reducido
        $empresa = json_decode($response, true);

        $razonSocial = $empresa['razonSocial'] ?? 'No Disponible';

        // Retornar a una vista con la razón social
        return view('ClientesForm', ['razonSocial' => $razonSocial]);
    }
}
