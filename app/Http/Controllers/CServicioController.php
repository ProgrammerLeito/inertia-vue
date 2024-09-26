<?php

namespace App\Http\Controllers;

use App\Http\Requests\CserviciosRequest;
use App\Models\C_Servicio;
use App\Models\Condicione;
use App\Models\Ctrabajo;
use App\Models\Dato;
use App\Models\Documento;
use App\Models\Hmarca;
use App\Models\Hservicio;
use App\Models\Observacione;
use App\Models\Plantilla;
use App\Models\Recomendacione;
use App\Models\Servicio;
use App\Models\Trealizado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CServicioController extends Controller
{
    public function index()
    {
        $cservicios = C_Servicio::with('plantilla')->orderBy('id', 'DESC')->paginate(15);
        return Inertia::render('C_Servicio/Index', compact('cservicios'));
    }

    public function balanza(Request $request)
    {
        $servicios = Servicio::with('cliente', 'user')->orderBy('id', 'DESC')->paginate(10);
        $users = User::all();
        $datos = Dato::all();
        $totalHservicio = Hservicio::select('servicio_id', DB::raw('count(*) as total'))
            ->groupBy('servicio_id')
            ->pluck('total', 'servicio_id');

        return Inertia::render('C_Servicio/Balanzas', compact('servicios', 'users', 'datos', 'totalHservicio'));
    }

    public function cotiza(Request $request , $id)
    {
        $hServicios = DB::select("
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
        ", [$id]);

        return Inertia::render('C_Servicio/Servicio', [
            'hServicios' => $hServicios
        ]);
    }

    public function create()
    {
        $ultimoCservicio = C_Servicio::latest()->first();

        // Verificar si hay algún registro
        if ($ultimoCservicio) {
            // Obtener el valor de n_cotizacion del último registro
            $nCotizacion = str_pad($ultimoCservicio->n_cotizacion + 1, 10, '0', STR_PAD_LEFT);
        } else {
            // En caso de que no haya registros, asignar un valor predeterminado
            $nCotizacion = '0000000001'; // Puedes asignar cualquier valor aquí
        }
        $ctrabajos=Ctrabajo::all();
        $trealizados = Trealizado::all();
        $observaciones = Observacione::all();
        $documentos = Documento::all();
        $condiciones = Condicione::all();
        $recomendaciones = Recomendacione::all();
        $plantillas = Plantilla::all();
        return Inertia::render('C_Servicio/Create', compact('plantillas', 'trealizados', 'condiciones', 'observaciones', 'documentos', 'recomendaciones', 'nCotizacion','ctrabajos'));
    }

    public function store(CserviciosRequest $request)
    {
        // dd($request);
        $foto = Auth::user()->name;
        $validatedData = $request->validated();
        $validatedData['foto'] = $foto;
        $validatedData['descripcion'] = 'Por Enviar';

        if ($request->hasFile('foto1')) {
            $validated['foto1'] = $request->file('foto1')->store('fotos');
        }
        C_Servicio::create($validatedData);

        return redirect()->route('c_servicos.index');
    }

    public function edit($id)
    {
        $cservicio = C_Servicio::find($id);
        $plantillas = Plantilla::all();
        return Inertia::render('C_Servicio/Edit', compact('cservicio', 'plantillas'));
    }

    public function update(CserviciosRequest $request,  $id)
    {
        $validatedData = $request->validated();
        $cservicio = C_Servicio::find($id);
        $cservicio->update($validatedData);
        return redirect()->route('c_servicos.index');
    }



    public function destroy($id)
    {
        $cservicios = C_Servicio::find($id);
        $cservicios->delete();
        return redirect()->back();
    }

    public function cambiarEstado(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'cservicio_id' => 'required|exists:cservicios,id',
            'descripcion' => 'required|in:Por Enviar,Enviado,Aceptado,Rechazado,Finalizado',
        ]);

        // Obtener el servicio
        $cservicio = C_Servicio::find($request->cservicio_id);

        // Asignar el nuevo estado al servicio
        $cservicio->descripcion = $request->descripcion;
        $cservicio->save();

        // return response()->json(['message' => 'Estado del servicio cambiado con éxito'], 200);
    }

    public function codigoSunat(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'cservicio_id' => 'required|exists:cservicios,id',
            'codesunat' => 'required',
        ]);

        // Obtener el servicio
        $cservicio = C_Servicio::find($request->cservicio_id);

        // Asignar el nuevo estado al servicio
        $cservicio->codesunat = $request->codesunat;
        $cservicio->save();
    }

    public function show()
    {
        $cservicio = C_Servicio::all();
        return Inertia::render('C_Servicio/Index', compact('cservicio'));
    }
}
