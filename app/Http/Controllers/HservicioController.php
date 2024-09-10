<?php

namespace App\Http\Controllers;

use App\Http\Requests\HservicioRequest;
use App\Http\Requests\UpdateHservicioRequest;
use App\Models\Cliente;
use App\Models\Hservicio;
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
                        )
                        ->paginate(5000);
        return Inertia::render('Hojaservico/Index',compact('hservicios'));
    }

    public function obtenerHojasServicioDiarias(Request $request)
    {
        $hservicios = DB::table('hservicios')
            ->join('clientes', 'hservicios.cliente_id', '=', 'clientes.id')
            ->select(
                'clientes.razonSocial as razonSocial',
                'hservicios.id',
                'hservicios.fecha'
            )
            ->get();

        return response()->json($hservicios);
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
        
        // Manejo de las fotos
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

    public function obtenerHojasServicio(Request $request){
        $cliente_id = $request->input('cliente_id');

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
                hservicios.div,
                hservicios.capacidad,
                hservicios.cliente_id,
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
            WHERE hservicios.cliente_id = ?
            AND hservicios.fecha = CURDATE()',
            [$cliente_id]
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
