<?php

namespace App\Http\Controllers;

use App\Http\Requests\HservicioRequest;
use App\Http\Requests\UpdateHservicioRequest;
use App\Models\Cliente;
use App\Models\Hservicio;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HservicioController extends Controller
{
    public function index(Request $request){
        $hservicios = DB::table('hservicios')
                        ->join('clientes', 'hservicios.cliente_id', '=', 'clientes.id')
                        ->select(
                            'clientes.razonSocial as razonSocial',
                            'hservicios.id',
                            'hservicios.fecha',
                        );
        return Inertia::render('Hojaservico/Index',compact('hservicios'));
    }

    public function obtenerHojasServicioDiarias(Request $request)
    {
        $servicios = DB::table('servicios')
            ->join('clientes', 'servicios.cliente_id', '=', 'clientes.id')
            ->join('hservicios', 'servicios.id', '=', 'hservicios.n_servicio')
            ->select(
                'clientes.razonSocial as razonSocial',
                'servicios.id',
                'servicios.fecha',
                DB::raw('SUM(CASE WHEN hservicios.instrumento = "1" THEN 1 ELSE 0 END) as balanzas'),
                DB::raw('SUM(CASE WHEN hservicios.instrumento = "2" THEN 1 ELSE 0 END) as termometros'),
                DB::raw('SUM(CASE WHEN hservicios.instrumento = "3" THEN 1 ELSE 0 END) as pesas'),
                'hservicios.n_servicio as n_informe'
            )
            ->groupBy('servicios.id', 'clientes.razonSocial', 'servicios.fecha', 'hservicios.n_servicio')
            ->orderBy("servicios.fecha", "DESC")
            ->get();

        return response()->json($servicios);
    }

    public function consultarDatosDiagnosticosServ(Request $request)
    {
        $tbservicioId = $request->input('id');

        $hservicios = DB::select("
            SELECT
                hservicios.id AS hservicio_id,
                hservicios.n_servicio AS hservicio_n_servicio,
                hservicios.hmarca_id,
                hservicios.instrumento,
                hservicios.rango,
                hservicios.medida_bastago,
                hservicios.codigo,
                hservicios.material,
                hservicios.modelo,
                hservicios.serie,
                hservicios.division,
                hservicios.capacidad,
                hservicios.cliente_id AS hservicio_cliente_id,
                hservicios.plataforma,
                hservicios.fecha AS hservicio_fecha,
                hservicios.requiere,
                hservicios.diagnostico,
                hservicios.trabajos,
                hservicios.tecnico,
                hservicios.foto,
                hservicios.foto2,
                hservicios.foto3,
                clientes.razonSocial,
                servicios.id AS servicio_id,
                servicios.fecha AS informetec_fecha
            FROM hservicios
            JOIN clientes ON hservicios.cliente_id = clientes.id
            JOIN servicios ON hservicios.n_servicio = servicios.id
            WHERE hservicios.id = ?
        ", [$tbservicioId]);

        return response()->json([
            'hservicios' => $hservicios,
            'tbservicioId' => $tbservicioId,
        ]);
    }

    public function obtenerFechaHojaServicio(Request $request)
    {
        $tbIdClientaso = $request->input('cliente_id');
        $tbHojaServicioId = $request->input('fecha');
        
        // Busca el registro por fecha
        $servicio = DB::table('servicios')
            ->where('fecha', '=', $tbHojaServicioId)
            ->where('cliente_id', '=', $tbIdClientaso)
            ->first();

        // Si no existe, crearlo
        if (!$servicio) {
            $id = DB::table('servicios')->insertGetId([
                'fecha' => $tbHojaServicioId,
                'cliente_id' => $tbIdClientaso,
            ]);

            // Retornar el nuevo registro creado
            $servicio = DB::table('servicios')->where('id', $id)->first();
        }

        return response()->json($servicio);
    }

    public function create(){
        $clientes = Cliente::select('id', 'razonSocial', 'direccion', 'numeroDocumento')->get();
        $hservicios = Hservicio::orderBy('id', 'desc')->get();
        return Inertia::render('Hojaservico/Create',compact('clientes', 'hservicios'));
    }

    public function store(HservicioRequest $request)
    {
        $tecnico = Auth::user()->name;

        $validatedData = $request->except(['foto', 'foto2', 'foto3']);

        $validatedData['tecnico'] = $tecnico;

        foreach (['foto', 'foto2', 'foto3'] as $foto) {
            if ($request->hasFile($foto)) {
                $file = $request->file($foto);
                $routeName = $file->store('', ['disk' => 'hservicio_img']);
                $validatedData[$foto] = $routeName;
            }
        }

        Hservicio::create($validatedData);
        return redirect()->back();
    }

    public function obtenerHojasServicioAnteriores(Request $request){
        $servicio = $request->input('servicio');
        $fecha = $request->input('fecha');

        $obtenerdatos = DB::select('
            SELECT 
                hservicios.id,
                hservicios.hmarca_id,
                hservicios.instrumento,
                hservicios.rango,
                hservicios.medida_bastago,
                hservicios.codigo,
                hservicios.material,
                hservicios.modelo,
                hservicios.serie,
                hservicios.division,
                hservicios.capacidad,
                hservicios.cliente_id,
                hservicios.n_servicio,
                hservicios.plataforma,
                hservicios.fecha,
                hservicios.requiere,
                hservicios.diagnostico,
                hservicios.trabajos,
                hservicios.tecnico,
                hservicios.foto,
                hservicios.foto2,
                hservicios.foto3,
                clientes.razonSocial AS cliente_razonSocial
            FROM hservicios
            INNER JOIN clientes ON hservicios.cliente_id = clientes.id
            INNER JOIN servicios ON hservicios.n_servicio = servicios.id
            WHERE hservicios.n_servicio = ?
            AND hservicios.fecha = ?',
            [$servicio ,$fecha]
        );

        return response()->json($obtenerdatos);
    }

    public function obtenerHojasServicio(Request $request){
        $cliente_id = $request->input('cliente_id');
        $fecha = $request->input('fecha');

        $obtenerdatos = DB::select('
            SELECT 
                hservicios.id,
                hservicios.hmarca_id,
                hservicios.instrumento,
                hservicios.rango,
                hservicios.medida_bastago,
                hservicios.codigo,
                hservicios.material,
                hservicios.modelo,
                hservicios.serie,
                hservicios.division,
                hservicios.capacidad,
                hservicios.cliente_id,
                hservicios.plataforma,
                hservicios.fecha,
                hservicios.requiere,
                hservicios.diagnostico,
                hservicios.trabajos,
                hservicios.tecnico,
                hservicios.n_servicio,
                hservicios.foto,
                hservicios.foto2,
                hservicios.foto3,
                clientes.razonSocial AS cliente_razonSocial
            FROM hservicios
            INNER JOIN clientes ON hservicios.cliente_id = clientes.id
            WHERE hservicios.cliente_id = ?
            AND hservicios.fecha = ?',
            [$cliente_id,$fecha]
        );

        return response()->json($obtenerdatos);
    }

    public function verificarFechayDatos(Request $request)
    {
        $cliente_id = $request->input('cliente_id');
        $fecha = $request->input('fecha');

        $obtenerdatos = DB::select('
            SELECT 
                hservicios.id,
                hservicios.hmarca_id,
                hservicios.instrumento,
                hservicios.rango,
                hservicios.medida_bastago,
                hservicios.codigo,
                hservicios.material,
                hservicios.modelo,
                hservicios.serie,
                hservicios.division,
                hservicios.capacidad,
                hservicios.cliente_id,
                hservicios.plataforma,
                hservicios.fecha,
                hservicios.requiere,
                hservicios.diagnostico,
                hservicios.trabajos,
                hservicios.tecnico,
                hservicios.n_servicio,
                hservicios.foto,
                hservicios.foto2,
                hservicios.foto3,
                clientes.razonSocial AS cliente_razonSocial
            FROM hservicios
            INNER JOIN clientes ON hservicios.cliente_id = clientes.id
            WHERE hservicios.cliente_id = ?
            AND hservicios.fecha = ?',
            [$cliente_id, $fecha]
        );

        return response()->json($obtenerdatos);
    }

    public function update(HservicioRequest $request, Hservicio $hservicio)
    {
        // Datos validados del request
        $validatedData = $request->except('foto', 'foto2', 'foto3');

        // Manejo de archivo 'foto'
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $routeName = $file->store('', ['disk' => 'hservicio_img']);
            $validatedData['foto'] = $routeName;

            if ($hservicio->foto) {
                Storage::disk('hservicio_img')->delete($hservicio->foto);
            }
        }

        // Manejo de archivo 'foto2'
        if ($request->hasFile('foto2')) {
            $file = $request->file('foto2');
            $routeName = $file->store('', ['disk' => 'hservicio_img']);
            $validatedData['foto2'] = $routeName;

            if ($hservicio->foto2) {
                Storage::disk('hservicio_img')->delete($hservicio->foto2);
            }
        }

        // Manejo de archivo 'foto3'
        if ($request->hasFile('foto3')) {
            $file = $request->file('foto3');
            $routeName = $file->store('', ['disk' => 'hservicio_img']);
            $validatedData['foto3'] = $routeName;

            if ($hservicio->foto3) {
                Storage::disk('hservicio_img')->delete($hservicio->foto3);
            }
        }

        // Actualización del modelo
        $hservicio->update($validatedData);
    }


    public function destroy($id)
    {
        Hservicio::destroy($id);
        return redirect()->back();
    }
}
