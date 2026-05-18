<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketController extends Controller
{
    public function welcome()
    {
        $productos = DB::table('Productos')->limit(6)->get();
        $comercios = DB::table('Vendedores')->limit(4)->get();

        return view('welcome', [
            'productos' => $productos,
            'comercios' => $comercios,
        ]);
    }

    public function productos(Request $request)
    {
        $productos = DB::table('Productos')->get();
        $comercios = DB::table('Vendedores')->get();

        return view('productos', [
            'productos' => $productos,
            'comercios' => $comercios,
        ]);
    }

    public function detalleProducto(Request $request)
    {
        $id = $request->query('id');
        $producto = DB::table('Productos')->where('id_producto', $id)->first();

        if (!$producto) {
            abort(404);
        }

        $comercio = DB::table('Vendedores')->where('id_vendedor', $producto->id_vendedor)->first();

        return view('detalle-producto', [
            'producto' => $producto,
            'comercio' => $comercio,
        ]);
    }

    public function tienda(Request $request)
    {
        $id = $request->query('id');
        $tienda = DB::table('Vendedores')->where('id_vendedor', $id)->first();
        if (! $tienda) {
            abort(404);
        }

        $productos = DB::table('Productos')->where('id_vendedor', $tienda->id_vendedor)->get();

        return view('tienda', [
            'comercio' => $tienda,
            'productos' => $productos,
        ]);
    }

    public function carrito()
    {
        return view('carrito');
    }

    public function registro()
    {
        return view('registro');
    }

    public function micuenta()
    {
        return view('micuenta', [
            'user' => session('user'),
        ]);
    }

    public function misComercios()
    {
        $user = session('user');
        $vendor = null;
        $productos = [];

        if ($user && $user['tipo_usuario'] === 'vendedor') {
            $vendor = DB::table('Vendedores')->where('id_vendedor', $user['id_usuario'])->first();
            $productos = DB::table('Productos')->where('id_vendedor', $user['id_usuario'])->get();
        }

        return view('mis-comercios', [
            'user' => $user,
            'vendor' => $vendor,
            'productos' => $productos,
        ]);
    }

    public function comercios()
    {
        $comercios = DB::table('Vendedores')->get();

        return view('comercios', [
            'comercios' => $comercios,
        ]);
    }
}
