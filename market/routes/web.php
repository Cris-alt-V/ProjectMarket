<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\MarketController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Rutas públicas
Route::get('/', [MarketController::class, 'welcome']);
Route::get('/productos', [ProductController::class, 'index']);
Route::get('/producto/{id}', [ProductController::class, 'show']);
// Reviews
Route::post('/producto/{id}/review', [\App\Http\Controllers\ReviewController::class, 'store'])->middleware('auth.session');

// Messages between users
Route::post('/messages/send', [\App\Http\Controllers\MessageController::class, 'send'])->middleware('auth.session');
Route::get('/messages/conversations', [\App\Http\Controllers\MessageController::class, 'conversations'])->middleware('auth.session');
Route::get('/messages/product/{id}', [\App\Http\Controllers\MessageController::class, 'productThread'])->middleware('auth.session');

// Shipping calculation API
Route::post('/shipping/calculate', [\App\Http\Controllers\ShippingController::class, 'calculate']);
Route::get('/carrito', [MarketController::class, 'carrito']);
Route::get('/comercios', [MarketController::class, 'comercios']);
Route::get('/tienda', [MarketController::class, 'tienda']);
Route::view('/acerca-de', 'acerca-de');

// Rutas de autenticación
Route::get('/registro', [MarketController::class, 'registro']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register-buyer', [AuthController::class, 'registerBuyer']);
Route::post('/auth/register-store', [AuthController::class, 'registerStore']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

// Rutas protegidas - Cuenta del usuario
Route::get('/micuenta', [MarketController::class, 'micuenta'])->middleware('auth.session');
Route::post('/auth/profile', [AuthController::class, 'updateProfile'])->middleware('auth.session');

// Rutas protegidas - Vendedor
Route::get('/mis-comercios', [StoreController::class, 'products'])->middleware('auth.session');
Route::post('/productos/crear', [StoreController::class, 'store'])->middleware('auth.session');
Route::put('/productos/{id}', [StoreController::class, 'update'])->middleware('auth.session');
Route::delete('/productos/{id}', [StoreController::class, 'destroy'])->middleware('auth.session');
Route::post('/productos/agregar', [AuthController::class, 'addproduct'])->middleware('auth.session');


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

// Notifications API
Route::get('/notifications', [\App\Http\Controllers\NotificationsController::class, 'index'])->middleware('auth.session');
Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationsController::class, 'markRead'])->middleware('auth.session');
