<?php
 
namespace App\Http\Controllers;
 
use App\Models\Cliente;
use App\Models\Cventa;
use App\Models\Tbmarca;
use App\Models\Tbproducto;
use App\Models\Tenor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
 
 
class CventaController extends Controller
{
    public function index(){
        $cventas = Cventa::with('cliente', 'tenor')->get();
        return Inertia::render('Cotizas/Index',[
            'cventas'=>$cventas
        ]);
    }
 
    public function create(){
        $clientes = Cliente::all();
        $tenors = Tenor::all();
   
        $tbproductos = Tbproducto::with('tbmarca')->get();
       
        $tbmarcas = Tbmarca::all();
       
        return Inertia::render('Cotizas/Create',[
            'clientes' => $clientes,
            'tenors' => $tenors,
            'tbproductos' => $tbproductos,
            'tbmarcas' => $tbmarcas,
        ]);
    }
 
   
    public function store(Request $request)
    {
       
        $validatedData = $request->validate([
            'cliente_id' => 'required',
            'tenor_id' => 'required',
            'fecha' => 'required|date',
            'moneda' => 'required',
            'garantia' => 'required',
            'forma_pago' => 'required',
            'dias_entrega' => 'required',
            'subtotal' => 'required|numeric',
            'igv' => 'required|numeric',
            'total' => 'required|numeric',
        ]);
 
       
        $cventa = new Cventa();
        $cventa->cliente_id = $request->cliente_id;
        $cventa->tenor_id = $request->tenor_id;
        $cventa->fecha = $request->fecha;
        $cventa->moneda = $request->moneda;
        $cventa->garantia = $request->garantia;
        $cventa->forma_pago = $request->forma_pago;
        $cventa->dias_entrega = $request->dias_entrega;
        $cventa->subtotal = $request->subtotal;
        $cventa->igv = $request->igv;
        $cventa->total = $request->total;
        $cventa->save();
 
        return redirect()->route('cotizas.index')->with('success', 'Cotización guardada exitosamente.');
    }
 
 
    public function edit(Cventa $cventa)
    {
        $clientes = Cliente::all();
        $tenors = Tenor::all();
        $tbproductos = Tbproducto::with('tbmarca')->get();
        $tbmarcas = Tbmarca::all();
       
        // Aquí obtén los productos agregados y pásalos a la vista
        $productosAgregados = [];
   
        return Inertia::render('Cotizas/Edit', [
            'cventa' => $cventa,
            'clientes' => $clientes,
            'tenors' => $tenors,
            'tbproductos' => $tbproductos,
            'tbmarcas' => $tbmarcas,
            'productosAgregados' => $productosAgregados,
        ]);
    }
   
 
    public function update(Request $request, Cventa $cventa)
    {
        $validatedData = $request->validate([
            'cliente_id' => 'required',
            'tenor_id' => 'required',
            'fecha' => 'required|date',
            'moneda' => 'required',
            'garantia' => 'required',
            'forma_pago' => 'required',
            'dias_entrega' => 'required',
            'subtotal' => 'required|numeric',
            'igv' => 'required|numeric',
            'total' => 'required|numeric',
        ]);
       
 
        $cventa->update([
            'cliente_id' => $request->cliente_id,
            'tenor_id' => $request->tenor_id,
            'fecha' => $request->fecha,
            'moneda' => $request->moneda,
            'garantia' => $request->garantia,
            'forma_pago' => $request->forma_pago,
            'dias_entrega' => $request->dias_entrega,
            'subtotal' => $request->subtotal,
            'igv' => $request->igv,
            'total' => $request->total,
        ]);
 
        return redirect()->route('cotizas.index')->with('success', 'Cotización actualizada exitosamente.');
    }
 
 
    public function destroy($id)
    {
        $cventa = Cventa::findOrFail($id);
        $cventa->delete();
        return redirect()->back();
    }
 
   
   
}