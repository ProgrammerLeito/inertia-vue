<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SalidasController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TbcategoriaController;
use App\Http\Controllers\TbmarcaController;
use App\Http\Controllers\TbproductoController;
use App\Http\Controllers\TbsubcategoriaController;
use App\Http\Controllers\UserController;

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
});

