<?php

use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\JwtLoginController;

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::get('/chorizo', function(){ return view('chorizo');});

/* 2025_04_16 - Se agrupan rutas asociadas a controlador Producto. */
Route::controller(ProductsController::class)->group(function(){
    Route::post('/agregar_producto', [ProductsController::class, 'addProduct'])->middleware('jwt.auth');

    Route::get('/eliminar_producto/{id}', [ProductsController::class, 'removeProduct'])->middleware('jwt.auth');

    Route::post('/modificar_producto', [ProductsController::class, 'updateProduct'])->middleware('jwt.auth');

    Route::get('/obtener_producto', [ProductsController::class, 'getProduct'])->middleware('jwt.auth');

    Route::get('/listar_productos', [ProductsController::class, 'getListProducts'])->middleware('jwt.auth');

    Route::get('/productos/imagen/{id}', [ProductsController::class, 'getImage']);
});

/* 2025_04_16 - Se agrupan rutas asociadas a controlador Categoría. */
Route::controller(CategoriesController::class)->group(function(){
    Route::get('/user/{id}', [CategoriesController::class, 'addProduct']);

    Route::get('/user/{id}', [CategoriesController::class, 'removeProduct']);

    Route::get('/user/{id}', [CategoriesController::class, 'updateProduct']);

    Route::get('/user/{id}', [CategoriesController::class, 'getProduct']);

    Route::get('/get_categorias', [CategoriesController::class, 'getListCategories'])->middleware('jwt.auth');;
});


Route::controller(JwtLoginController::class)->group(function(){
    Route::post('/ingresar', [JwtLoginController::class, 'login']);
});



