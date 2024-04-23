<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
 
class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')->orderBy('id', 'DESC')->paginate(6);
 
        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }
 
 
    public function trashed_users(Request $request)
    {
        $users = User::onlyTrashed()->with('roles')->orderBy('id', 'DESC')->paginate(6);
 
        return Inertia::render('Users/Trash_list', [
            'users'=>$users
        ]);
    }
 
    public function restore($id){
        $user= User::withTrashed()->findOrFail($id);
        if(!empty($user)){
            $user->restore();
        }
        return redirect()->back();
    }
 
    public function deletePermanently($id){
        $user= User::withTrashed()->findOrFail($id);
        if(!empty($user)){
            $user->forceDelete();
        }
        return redirect()->back();
    }
 
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return Inertia::render('Users/Create', [
            'roles'=>$roles
        ]);
    }
 
 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'apellidopat' => 'required',
            'apellidomat' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'sexo' => 'required',
            'celular' => 'required',
            'roles' => 'required|array',
        ]);
 
        if ($validator->fails()) {
            return redirect()->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }
 
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
 
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
 
        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
    }
 
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRoles = $user->roles->pluck('name')->toArray();
 
        return Inertia::render('Users/Edit',[
            'user'=>$user,
            'roles'=>$roles,
            'userRoles'=>$userRoles
        ]);
    }
 
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'apellidopat' => 'required',
            'apellidomat' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|confirmed',
            'sexo' => 'required',
            'celular' => 'required',
            'roles' => 'required|array',
        ]);
 
        if ($validator->fails()) {
            return redirect()->route('users.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }
 
        $input = $request->all();
 
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
 
        $user = User::findOrFail($id);
        $user->update($input);
 
        // Obtén los roles sincronizados con el usuario
        $roles = Role::whereIn('name', $request->input('roles'))->get();
        $user->syncRoles($roles);
 
        // Obtén los permisos asociados a los roles sincronizados
        $permissions = [];
 
        foreach($roles as $role)
        {
 
            $permission = DB::table('role_has_permissions')
                        ->where('role_id', $role['id'])
                        ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                        ->pluck('permissions.name')
                        ->toArray();
            array_push($permissions, $permission);
        }
 
 
        // Sincroniza los permisos con el usuario
        $user->syncPermissions($permissions);
 
 
        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
    }
 
    public function destroy( $id)
    {
        $user =User::findOrFail($id);
        $user->delete();
 
        return redirect()->route('users.index')
        ->with('success', 'Usuario eliminado correctamente');
    }
}
 