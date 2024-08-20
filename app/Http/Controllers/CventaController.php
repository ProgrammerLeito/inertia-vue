<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cventa;
use App\Models\Tbcategoria;
use App\Models\Tbmarca;
use App\Models\Tbproducto;
use App\Models\Tbsubcategoria;
use App\Models\TbproductosAgregados;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CventaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Verifica si el usuario tiene los roles 'Area de TI' o 'Gerencia'
        if ($user->roles->contains('name', 'Area de TI') || $user->roles->contains('name', 'Gerencia')) {
            // Si el usuario tiene alguno de estos roles, muestra todas las cotizaciones
            $cventas = Cventa::with('cliente')
                ->orderBy('id', 'DESC')
                ->paginate(30);
        } else {
            // De lo contrario, muestra solo las cotizaciones donde el técnico sea el usuario autenticado
            $nombreCompleto = $user->name . ' ' . $user->apellidopat . ' ' . $user->apellidomat;
            $cventas = Cventa::with('cliente')
                ->where('tecnico', $nombreCompleto)
                ->orderBy('id', 'DESC')
                ->paginate(30);
        }

        return Inertia::render('Cotizas/Index', compact('cventas'));
    }

    public function create()
    {
        $ultimaCotizacion = Cventa::latest()->first();

        // Verificar si hay algún registro
        if ($ultimaCotizacion) {
            // Obtener el valor de n_cotizacion del último registro
            $nCotizacion = str_pad($ultimaCotizacion->n_cotizacion + 1, 10, '0', STR_PAD_LEFT);
        } else {
            // En caso de que no haya registros, asignar un valor predeterminado
            $nCotizacion = '0000000001'; // Puedes asignar cualquier valor aquí
        }

        $clientes = Cliente::select('id', 'razonSocial', 'direccion', 'numeroDocumento')->get();
        $tbproductos = Tbproducto::with('tbcategoria', 'tbsubcategoria', 'tbmarca')->get();
        $tbcategorias = Tbcategoria::with('tbsubcategorias')->get();
        $tbsubcategorias = Tbsubcategoria::select('id', 'tbcategoria_id', 'nombre')->get();
        $tbmarcas = Tbmarca::select('id', 'nombre', 'tbsubcategoria_id')->get();
        return Inertia::render('Cotizas/Create', compact('clientes', 'tbproductos', 'tbcategorias', 'tbsubcategorias', 'tbmarcas', 'nCotizacion'));
    }

    public function guardarCotizacion(Request $request){
        $cliente_id = $request->input('cliente_id');
        $numeroDocumento = $request->input('numeroDocumento');
        $direccion = $request->input('direccion');
        $tenor_id = $request->input('tenor_id');
        $fecha = $request->input('fecha');
        $moneda = $request->input('moneda');
        $validez_cot = $request->input('validez_cot');
        $forma_pago = $request->input('forma_pago');
        $dias_entrega = $request->input('dias_entrega');
        $tipoCambio = $request->input('tipoCambio');
        $subtotal = $request->input('subtotal');
        $igv = $request->input('igv');
        $total = $request->input('total');
        $usuarioAutenticado = Auth::user();
        $tecnico = $usuarioAutenticado->name . ' ' . $usuarioAutenticado->apellidopat . ' ' . $usuarioAutenticado->apellidomat;

        $agregarCotizacion = new Cventa;
        $agregarCotizacion->cliente_id = $cliente_id;
        $agregarCotizacion->numeroDocumento = $numeroDocumento;
        $agregarCotizacion->direccion = $direccion;
        $agregarCotizacion->tenor_id = $tenor_id;
        $agregarCotizacion->fecha = $fecha;
        $agregarCotizacion->moneda = $moneda;
        $agregarCotizacion->validez_cot = $validez_cot;
        $agregarCotizacion->forma_pago = $forma_pago;
        $agregarCotizacion->dias_entrega = $dias_entrega;
        $agregarCotizacion->tipoCambio = $tipoCambio;
        $agregarCotizacion->subtotal = $subtotal;
        $agregarCotizacion->igv = $igv;
        $agregarCotizacion->total = $total;
        $agregarCotizacion->tecnico = $tecnico;
        $agregarCotizacion->estado = 'Por Enviar';
        $agregarCotizacion->save();

        return response()->json(['success' => true], 200);
    }

    public function validarIdCot()
    {
        $productos = DB::table('cventas')
                        ->select(DB::raw('MAX(id) as id'))
                        ->first();

        return response()->json($productos);
    }

    public function guardarProductosCotizacion(Request $request)
    {
        $productos = $request->input('productos');

        foreach ($productos as $producto) {
            TbproductosAgregados::create([
                'idCotizacion' => $producto['idCotizacion'], // Asegúrate de pasar este valor
                'subcategoria_id' => $producto['subcategoria_id'],
                'modelo' => $producto['modelo'],
                'especificaciones' => $producto['especificaciones'], // Asegúrate de pasar las especificaciones correctamente
                'marca' => $producto['marca'],
                'capacidades' => $producto['capacidades'], // Asegúrate de pasar las capacidades correctamente
                'precio_list' => $producto['precio_list'],
                'precio_min' => $producto['precio_min'],
                'precio_max' => $producto['precio_max'],
                'cantidad' => $producto['cantidad'],
                'importe' => $producto['importe'],
                'garantia' => $producto['garantia'],
                'dias_entrega' => $producto['dias_entrega'],
                'forma_pago' => $producto['forma_pago'],
                'moneda' => $producto['moneda'],
                'foto' => $producto['foto'],
                'requerimientos' => $producto['requerimientos']
            ]);
        }
    }

    public function consultarDatosCot(Request $request)
    {
        $cventasId = $request->input('id');

        if (!$cventasId) {
            return response()->json(['error' => 'ID is required'], 400);
        }

        $cventas = DB::select(
            'SELECT 
                cventas.id,
                cventas.n_cotizacion,
                cventas.codesunat,
                cventas.estado,
                cventas.tecnico,
                cventas.cliente_id,
                clientes.numeroDocumento,
                clientes.direccion,
                cventas.fecha,
                cventas.moneda,
                cventas.tipoCambio,
                cventas.forma_pago,
                cventas.dias_entrega,
                cventas.validez_cot,
                cventas.subtotal,
                cventas.igv,
                cventas.total,
                clientes.razonSocial
            FROM cventas
            JOIN clientes ON cventas.cliente_id = clientes.id
            WHERE cventas.id = :id',
            ['id' => $cventasId]
        );

        if (!$cventas) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        return response()->json([
            'cventas' => $cventas[0], // Access the first item in the result array
            'cventasId' => $cventasId,
        ]);
    }

    public function consultarDatosProductosCot(Request $request)
    {
        $tbagregadoId = $request->input('id');

        $tbproductos_agregados = DB::select("
            SELECT
                id,
                idCotizacion,
                subcategoria_id,
                modelo,
                especificaciones,
                marca,
                capacidades,
                precio_list,
                precio_min,
                precio_max,
                cantidad,
                importe,
                garantia,
                dias_entrega,
                forma_pago,
                moneda,
                foto,
                requerimientos
            FROM tbproductos_agregados
            WHERE idCotizacion = ?
        ", [$tbagregadoId]);

        return response()->json([
            'tbproductos_agregados' => $tbproductos_agregados,
            'tbagregadoId' => $tbagregadoId,
        ]);
    }
    
    public function edit(Cventa $cventa)
    {
        $clientes = Cliente::all();
        $tbproductos = Tbproducto::with('tbcategoria', 'tbsubcategoria', 'tbmarca')->get();
        $tbmarcas = Tbmarca::all();
        $tbcategorias = Tbcategoria::with('tbsubcategorias')->get();
        $idCotizacion = $cventa->id; // Obtener el id de la cotización desde el modelo
        $tbproductosEditAgregados = TbproductosAgregados::where('idCotizacion', $idCotizacion)->get();

        return Inertia::render('Cotizas/Edit', compact('cventa', 'clientes', 'tbproductos', 'tbcategorias', 'tbmarcas', 'tbproductosEditAgregados'));
    }

    public function update(Request $request, Cventa $cventa)
    {
        // Actualiza el objeto Cventa con los datos del formulario
        $cventa->update($request->all());

        // Redirige al usuario a la lista de cotizaciones
        return redirect()->route('cventas.index');
    }

    public function destroy($id)
    {
        $cventa = Cventa::findOrFail($id);
        $cventa->delete();
        return redirect()->back();
    }

    public function cambiarEstado(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'cventa_id' => 'required|exists:cventas,id',
            'estado' => 'required|in:Por Enviar,Enviado,Aceptado,Rechazado,Finalizado',
        ]);

        // Obtener el servicio
        $cventa = Cventa::find($request->cventa_id);

        // Asignar el nuevo estado al servicio
        $cventa->estado = $request->estado;
        $cventa->save();

        // return response()->json(['message' => 'Estado del servicio cambiado con éxito'], 200);
    }

    public function show()
    {
        $cventa = Cventa::all();
        return Inertia::render('Cotizas/Index', compact('cventa'));
    }
}
