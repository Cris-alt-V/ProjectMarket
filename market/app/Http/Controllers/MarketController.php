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
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('p.nombre', 'like', "%$search%")
                    ->orWhere('p.descripcion', 'like', "%$search%")
                    ->orWhere('p.categoria', 'like', "%$search%");
            });
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

        $reviews = DB::table('reviews')->where('producto_id', $id)->orderBy('created_at', 'desc')->get();

        return view('detalle-producto', [
            'producto' => $producto,
            'comercio' => $comercio,
            'reviews' => $reviews,
        ]);
    }

    public function tienda(Request $request)
    {
        $id = $request->query('id', $request->query('comercio_id'));
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

        $type = $user['tipo_usuario'] === 'vendedor' ? 'vendedor' : 'user';
        $notifications = DB::table('notifications')
            ->where('notifiable_type', $type)
            ->where('notifiable_id', $user['id_usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $unreadCount = DB::table('notifications')
            ->where('notifiable_type', $type)
            ->where('notifiable_id', $user['id_usuario'])
            ->whereNull('read_at')
            ->count();

        return view('micuenta', [
            'user' => $user,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
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
