<?php
 
namespace App\Http\Controllers;

use App\Http\Requests\CreateCventaRequest;
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
        return Inertia::render('Cotizas/Index',compact('cventas'));
    }
 
    public function create(){
        $clientes = Cliente::all();
        $tenors = Tenor::all();
   
        $tbproductos = Tbproducto::with('tbmarca')->get();
       
        $tbmarcas = Tbmarca::all();
       
        return Inertia::render('Cotizas/Create',compact('clientes', 'tenors', 'tbproductos', 'tbmarcas'));
    }

    public function store(CreateCventaRequest $request)
    {
        $validatedData = $request->validated();
        $cventa = Cventa::create($validatedData);
        $cventa -> save ();
        return redirect()->route('cotizas.index');
    }

    public function edit(Cventa $cventa)
    {
        $clientes = Cliente::all();
        $tenors = Tenor::all();
        $tbproductos = Tbproducto::with('tbmarca')->get();
        $tbmarcas = Tbmarca::all();
       
        // Aquí obtén los productos agregados y pásalos a la vista
        $productosAgregados = [];
   
        return Inertia::render('Cotizas/Edit', compact('cventa', 'clientes', 'tenors', 'tbproductos', 'tbmarcas', 'productosAgregados'));
    }

    public function update(Request $request, Cventa $cventa)
    {
        $cventa->update($request->all());
        return redirect()->route('cventa.index');
    }
 
    public function destroy($id)
    {
        $cventa = Cventa::findOrFail($id);
        $cventa->delete();
        return redirect()->back();
    }
}