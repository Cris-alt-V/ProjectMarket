<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('/productos', 'productos');
Route::view('/detalle-producto', 'detalle-producto');
Route::view('/carrito', 'carrito');
Route::view('/registro', 'registro');
Route::view('/micuenta', 'micuenta');
Route::view('/mis-comercios', 'mis-comercios');
Route::view('/comercios', 'comercios');
Route::view('/tienda', 'tienda');
Route::view('/acerca-de', 'acerca-de');
