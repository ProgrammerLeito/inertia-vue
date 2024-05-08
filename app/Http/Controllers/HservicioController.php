<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\HservicioRequest;
use App\Http\Requests\UpdateHservicioRequest;
use App\Models\Hmarca;
use App\Models\Hservicio;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
 
class HservicioController extends Controller
{
    public function index(Request $request){
        $servicio_id = $request->input('servicio_id');
        $hservicios = Hservicio::with('hmarca')-> where('servicio_id', $servicio_id)->orderBy('id', 'DESC')->get();
        $servicios=Servicio::all();
        $hmarcas=Hmarca::all();
        $totalHservicio = Hservicio::where('servicio_id', $servicio_id)->count();
        $countByRequiere = Hservicio::where('servicio_id', $servicio_id)
            ->selectRaw('requiere, count(*) as total')
            ->groupBy('requiere')
            ->pluck('total', 'requiere')
            ->toArray();
        return Inertia::render('Hojaservico/Index',compact('hservicios','countByRequiere','totalHservicio','servicios','hmarcas'));
    }
 
    public function create(){
        $servicios = Servicio::with('cliente')->get();
        $primerServicio = $servicios->first();
        $nInforme = $primerServicio->n_informe ?? '';
        $razonSocial = $primerServicio->cliente->razonSocial ?? '';
        $hmarcas=Hmarca::all();
        return Inertia::render('Hojaservico/Create',compact('servicios', 'nInforme', 'razonSocial','hmarcas'));
    }
 
    public function store(HservicioRequest $request)
    {
        $tecnico = Auth::user()->name;
        $validatedData = $request->except(['foto', 'foto2', 'foto3']);
        $validatedData['tecnico'] = $tecnico;
 
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $routeName = $foto->store('requerimiento', ['disk' => 'public']);
            $validatedData['foto'] = $routeName;
        }
        if ($request->hasFile('foto2')) {
            $foto2 = $request->file('foto2');
            $routeName2 = $foto2->store('requerimiento', ['disk' => 'public']);
            $validatedData['foto2'] = $routeName2;
        }
        if ($request->hasFile('foto3')) {
            $foto3 = $request->file('foto3');
            $routeName3 = $foto3->store('requerimiento', ['disk' => 'public']);
            $validatedData['foto3'] = $routeName3;
        }
        $hservicio = Hservicio::create($validatedData);
        return redirect()->route('hservicios.index', ['servicio_id' => $hservicio->servicio_id]);
    }
 
    public function edit(Hservicio $hservicio)
    {
        $servicios = Servicio::with('cliente')->get();
        $primerServicio = $servicios->first();
        $nInforme = $primerServicio->n_informe ?? '';
        $razonSocial = $primerServicio->cliente->razonSocial ?? '';
        $hmarcas=Hmarca::all();
        return Inertia::render('Hojaservico/Edit', compact('hservicio','nInforme','servicios','hmarcas'));
    }
 
 
    public function update(UpdateHservicioRequest $request, Hservicio $hservicio)
    {
        $validatedData = $request->except('foto', 'foto2', 'foto3');
 
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $routeName = $file->store('requerimiento', ['disk' => 'public']);
            $validatedData['foto'] = $routeName;
 
            if ($hservicio->foto) {
                Storage::disk('public')->delete($hservicio->foto);
            }
        }
 
        if ($request->hasFile('foto2')) {
            $file2 = $request->file('foto2');
            $routeName2 = $file2->store('requerimiento', ['disk' => 'public']);
            $validatedData['foto2'] = $routeName2;
            if ($hservicio->foto2) {
                Storage::disk('public')->delete($hservicio->foto2);
            }
        }
 
        if ($request->hasFile('foto3')) {
            $file3 = $request->file('foto3');
            $routeName3 = $file3->store('requerimiento', ['disk' => 'public']);
            $validatedData['foto3'] = $routeName3;
 
            if ($hservicio->foto3) {
                Storage::disk('public')->delete($hservicio->foto3);
            }
        }
 
        $hservicio->update($validatedData);
 
        return redirect()->route('hservicios.index', ['servicio_id' => $hservicio->servicio_id]);
    }
 
 
    public function destroy($id)
    {
        Hservicio::destroy($id);
        return redirect()->back();
    }
}