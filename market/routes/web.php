<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\MarketController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', [MarketController::class, 'welcome']);
Route::get('/productos', [MarketController::class, 'productos']);
Route::get('/detalle-producto', [MarketController::class, 'detalleProducto']);
Route::get('/carrito', [MarketController::class, 'carrito']);
Route::get('/registro', [MarketController::class, 'registro']);
Route::get('/micuenta', [MarketController::class, 'micuenta']);
Route::get('/mis-comercios', [MarketController::class, 'misComercios']);
Route::get('/comercios', [MarketController::class, 'comercios']);
Route::get('/tienda', [MarketController::class, 'tienda']);
Route::view('/acerca-de', 'acerca-de');

Route::post('/api/auth/login', [AuthController::class, 'login']);
Route::post('/api/auth/register-buyer', [AuthController::class, 'registerBuyer']);
Route::post('/api/auth/register-store', [AuthController::class, 'registerStore']);
Route::get('/api/auth/user', [AuthController::class, 'user']);
Route::post('/api/auth/logout', [AuthController::class, 'logout']);
Route::post('/api/auth/profile', [AuthController::class, 'updateProfile']);

Route::get('/api/products', [ProductController::class, 'index']);
Route::get('/api/products/{id}', [ProductController::class, 'show']);

Route::get('/api/vendor/products', [StoreController::class, 'products']);
Route::post('/api/vendor/products', [StoreController::class, 'store']);
Route::put('/api/vendor/products/{id}', [StoreController::class, 'update']);
Route::delete('/api/vendor/products/{id}', [StoreController::class, 'destroy']);

Route::get('/db-check', function () {
	try {
		DB::connection()->getPdo();
		$status = 'success';
		$message = 'Conexión exitosa a la base de datos: ' . config('database.default') . ' (' . config("database.connections.".config('database.default').".database") . ')';
	} catch (\Exception $e) {
		$status = 'failed';
		$message = 'Error de conexión: ' . $e->getMessage();
	}

	return view('dbstatus', compact('status', 'message'));
});
