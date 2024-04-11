<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SalidasController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\TbcategoriaController;
use App\Http\Controllers\TbmarcaController;
use App\Http\Controllers\TbproductoController;
use App\Http\Controllers\TbsubcategoriaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SidebarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

//Rutas no autenticadas
Route::redirect('/', '/login');

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {
    //rutas autenticadas
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('/categories', CategoryController::class);
    Route::resource('/clientes', ClienteController::class);
    Route::resource('/roles', RoleController::class);
    Route::resource('/productos', ProductoController::class);
    Route::resource('/salidas', SalidasController::class);
    Route::resource('/entradas', EntradaController::class);
    Route::post('/comprobarSalida', [SalidasController::class , 'comprobarSalida']);
    Route::post('/salidas.store', [SalidasController::class , 'store']);
    Route::put('/salidas.update', [SalidasController::class , 'update']);
    Route::resource('/users', UserController::class);
    Route::resource('/tbproductos', TbproductoController::class);
    Route::resource('/tbmarcas', TbmarcaController::class);
    Route::resource('/tbcategorias', TbcategoriaController::class);
    Route::resource('/tbsubcategorias', TbsubcategoriaController::class);
    Route::resource('/sidebar', SidebarController::class);
    Route::get('trashed_users',[UserController::class,'trashed_users'])->name('users.trashed');
    Route::get('restore_users/{id}',[UserController::class,'restore'])->name('users.restore');
    Route::delete('delete_users_deletePermanently/{id}',[UserController::class,'deletePermanently'])->name('users.deletePermanently');
    Route::resource('roles', RoleController::class);
    Route::resource('permisos', PermisoController::class);
    //Consulta para consumir la api de sunat
    Route::get('/consultar-reniec', function (Request $request) {
        $numeroDocumento = $request->query('numero');
        $token = 'apis-token-7907.K0qLm91OLHYP07iBLCqF4INtKqqtu0H6';
   
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('https://api.apis.net.pe/v2/sunat/ruc', [
            'numero' => $numeroDocumento
        ]);
   
        return $response->json();
    });
});

