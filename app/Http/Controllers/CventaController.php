<?php

namespace App\Http\Controllers;

use App\Http\Requests\CventaRequest;
use App\Models\Cliente;
use App\Models\Cventa;
use App\Models\Tbcategoria;
use App\Models\Tbmarca;
use App\Models\Tbproducto;
use App\Models\Tbsubcategoria;
use App\Models\TbproductosAgregados;
use App\Models\Tenor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CventaController extends Controller
{
    public function index()
    {
        $cventas = Cventa::with('cliente', 'tenor')->orderBy('id', 'DESC')->paginate(10);
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

        $clientes = Cliente::all();
        $tenors = Tenor::all();
        $tbproductos = Tbproducto::with('tbcategoria', 'tbsubcategoria', 'tbmarca')->get();
        $tbcategorias = Tbcategoria::with('tbsubcategorias')->get();
        $tbsubcategorias = Tbsubcategoria::all();
        $tbmarcas = Tbmarca::all();
        return Inertia::render('Cotizas/Create', compact('clientes', 'tenors', 'tbproductos', 'tbcategorias', 'tbsubcategorias', 'tbmarcas', 'nCotizacion'));
    }

    public function store(CventaRequest $request)
    {
        $tecnico = Auth::user()->name;
        $validatedData = $request->validated();
        $validatedData['tecnico'] = $tecnico;
        $validatedData['estado'] = 'Por Enviar';
        
        $cventa = Cventa::create($validatedData);
    }

    public function validarIdCot()
    {
        $productos = DB::table('cventas')
                        ->select(DB::raw('MAX(id) as id'))
                        ->first(); // Usa `first()` para obtener un único resultado en lugar de `get()`

        return response()->json($productos);
    }

    public function guardarProductosCotizacion(Request $request)
    {
        $productos = $request->input('productos');

        foreach ($productos as $producto) {
            TbproductosAgregados::create([
                'idCotizacion' => $producto['idCotizacion'], // Asegúrate de pasar este valor
                'categoria_id' => $producto['categoria_id'],
                'modelo' => $producto['modelo'],
                'especificaciones' => $producto['especificaciones'],
                'marca' => $producto['marca'],
                'capacidades' => implode('\n', $producto['capacidades']),
                'precio_list' => $producto['precio_list'],
                'precio_min' => $producto['precio_min'],
                'precio_max' => $producto['precio_max'],
                'cantidad' => $producto['cantidad'],
                'importe' => $producto['importe'],
                'garantia' => $producto['garantia'],
                'dias_entrega' => $producto['dias_entrega'],
                'forma_pago' => $producto['forma_pago'],
                'moneda' => $producto['moneda'],
                'foto' => $producto['foto']
            ]);
        }
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
