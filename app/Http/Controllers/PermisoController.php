<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
 
class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permission::select('id', 'name')->paginate(8);
       
        return Inertia::render('Permisos/Index', [
            'permisos' => $permisos
        ]);
    }
 
 
    public function create (){
        return Inertia::render('Permisos/Create');
    }
 
    public function store(Request $request)
    {
        // $this->validate($request,[
        //     'name'=>'required|unique:roles,name',
        // ]);
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);
 
        $permission = Permission::create(['name'=>$request->input('name')]);
 
        return redirect()->route('permisos.index')->with('success','permiso creado exitosamente');
    }
 
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return Inertia::render('Permisos/Edit', [
            'permiso' => [
                'id' => $permission->id,
                'name' => $permission->name,
                // Agrega otros campos aquí si es necesario
            ]
        ]);
    }
 
    public function update(Request $request, $id)
    {
        // $this->validate($request, [
        //     'name' => 'required|unique:permissions,name,' . $id,
        // ]);
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
        ]);
 
        $permission = Permission::findOrFail($id);
        $permission->name = $request->input('name');
        $permission->save();
 
        return redirect()->route('permisos.index')->with('success', 'Permiso actualizado exitosamente');
    }
 
    public function destroy($id)
    {
    DB::table("permissions")->where('id', $id)->delete();
    return redirect()->route('permisos.index')->with('success', 'Permiso eliminado correctamente');
    }
}
 