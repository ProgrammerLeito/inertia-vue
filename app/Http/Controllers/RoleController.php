<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
 
class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::select('id', 'name')->get();
       
        return Inertia::render('Roles/Index', compact('roles'));
    }
 
    public function create(){
        $permission = Permission::get();
        return Inertia::render('Roles/Create', compact('permission'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);
 
        // Verifica si el usuario actual está autenticado utilizando el guard 'web'
        if (Auth::guard('web')->check()) {
            // Si el usuario está autenticado utilizando 'web', crea el rol utilizando 'web'
            $role = Role::create(['name' => $request->input('name')]);
 
            return redirect()->route('roles.index');
        } else {
            // Si el usuario no está autenticado utilizando 'web', redirige o maneja el caso según sea necesario
            return redirect()->back()->with('error', 'No autorizado para crear roles');
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permisos = Permission::pluck('name', 'id')->all();
   
        $rolePermisos = $role->permissions->pluck('id')->toArray();
   
        return Inertia::render('Roles/Edit', compact('role', 'permisos', 'rolePermisos'));
    }
 
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::whereIn('id', $request->input('permisos'))->get();
   
        $role->syncPermissions($permissions);
   
        return redirect()->route('roles.edit', $id);
    }
 
    public function destroy($id){
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->back();
    }
}
