<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas públicas de la API
Route::get('/clima', [ApiController::class, 'weather']);
Route::get('/productos/destacados', [ApiController::class, 'featuredProducts']);
Route::get('/puestos/cercanos', [ApiController::class, 'nearbyStands']);
Route::get('/productos/buscar', [ApiController::class, 'searchProducts']);
Route::get('/mercado/estadisticas', [ApiController::class, 'marketStats']);

// Rutas protegidas de la API
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/usuario/perfil', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/usuario/pedidos', [ApiController::class, 'userOrders']);
});
