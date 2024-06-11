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
        $permisos = Permission::select('id', 'name')->orderBy('id', 'DESC')->paginate(15);
        return Inertia::render('Permisos/Index',compact('permisos'));
    }


    public function create (){
        return Inertia::render('Permisos/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        $permission = Permission::create(['name'=>$request->input('name')]);
        return redirect()->route('permisos.index');
    }

    public function edit($id)
    {
        $permission = Permission::find($id);
        return Inertia::render('Permisos/Edit', [
            'permiso' => [
                'id' => $permission->id,
                'name' => $permission->name,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
        ]);

        $permission = Permission::find($id);
        $permission->name = $request->input('name');
        $permission->save();
        return redirect()->route('permisos.index');
    }

    public function destroy($id)
    {
    DB::table("permissions")->where('id', $id)->delete();
    return redirect()->route('permisos.index');
    }
}
