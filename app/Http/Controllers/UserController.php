<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreateRequest $request)
    {
        // Obtener los datos validados del formulario
        $userData = $request->validated();

        // Hashear la contraseña si se proporciona
        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }

        // Crear el usuario en la base de datos
        User::create($userData);
 
        return Redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $validatedData = $request->validated();

        // Verificar si se cambió el correo electrónico
        if ($validatedData['email'] !== $user->email) {
            // Si el correo electrónico cambiado ya está en uso, redirige con un mensaje de error
            if (User::where('email', $validatedData['email'])->exists()) {
                return redirect()->back()->with('error', 'La dirección de correo electrónico ya está en uso.');
            }
        }

        // Si se proporcionó una nueva contraseña, hashearla antes de actualizar
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        // Actualizar los datos del usuario con los datos validados
        $user->fill($validatedData);
        $user->save();

        // Redireccionar de vuelta con un mensaje de éxito
        return redirect()->back()->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
 
        return Redirect()->back();
    }
}
