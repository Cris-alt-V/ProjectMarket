<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketController extends Controller
{
    public function welcome()
    {
        $productos = DB::table('productos')->limit(6)->get();
        $comercios = DB::table('vendedores')->limit(4)->get();

        return view('welcome', [
            'productos' => $productos,
            'comercios' => $comercios,
        ]);
    }

    public function productos(Request $request)
    {
        $query = DB::table('productos as p')
            ->join('vendedores as v', 'p.id_vendedor', '=', 'v.id_vendedor')
            ->select('p.*', 'v.nombre_negocio', 'v.ubicacion');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('p.nombre', 'like', "%$search%");
        }

        $productos = $query->get();
        $comercios = DB::table('vendedores')->get();

        return view('productos', [
            'productos' => $productos,
            'comercios' => $comercios,
        ]);
    }

    public function detalleProducto(Request $request)
    {
        $id = $request->query('id');
        $producto = DB::table('productos')->where('id_producto', $id)->first();

        if (!$producto) {
            abort(404);
        }

        $comercio = DB::table('vendedores')->where('id_vendedor', $producto->id_vendedor)->first();

        return view('detalle-producto', [
            'producto' => $producto,
            'comercio' => $comercio,
        ]);
    }

    public function tienda(Request $request)
    {
        $id = $request->query('id');
        $tienda = DB::table('vendedores')->where('id_vendedor', $id)->first();
        if (!$tienda) {
            abort(404);
        }

        $productos = DB::table('productos')->where('id_vendedor', $tienda->id_vendedor)->get();

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
        $user = session('user');
        if (!$user) {
            return redirect('/registro');
        }

        return view('micuenta', [
            'user' => $user,
        ]);
    }

    public function misComercios()
    {
        $user = session('user');
        if (!$user || $user['tipo_usuario'] !== 'vendedor') {
            return redirect('/registro');
        }

        $productos = DB::table('productos')->where('id_vendedor', $user['id_usuario'])->get();

        return view('mis-comercios', [
            'user' => $user,
            'productos' => $productos,
        ]);
    }

    public function comercios()
    {
        $comercios = DB::table('vendedores')->get();

        return view('comercios', [
            'comercios' => $comercios,
        ]);
    }
}
