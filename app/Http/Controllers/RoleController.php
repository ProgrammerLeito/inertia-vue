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
        $roles = Role::select('id', 'name')->orderBy('id', 'DESC')->paginate(15);
        return Inertia::render('Roles/Index', [
            'roles' => $roles
        ]);
    }

    public function create(){
        $permission = Permission::get();
        return Inertia::render('Roles/Create',[
            'permission'=>$permission
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);
        $role = Role::create(['name' => $request->input('name')]);
        return redirect()->route('roles.index');
    }

    public function edit(Role $role)
    {
        $permisos = Permission::pluck('name', 'id')->all();
        $rolePermisos = $role->permissions->pluck('id')->toArray();

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permisos' => $permisos,
            'rolePermisos' => $rolePermisos
        ]);
    }

    public function update(Request $request,Role $role)
    {
        $permissions = Permission::whereIn('id', $request->input('permisos'))->get();
        $role->syncPermissions($permissions);

        return redirect()->route('roles.edit', $role);
    }

    public function destroy($id){
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->back();
    }
}
