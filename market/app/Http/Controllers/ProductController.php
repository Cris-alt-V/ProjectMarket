<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $productos = DB::table('productos as p')
            ->join('vendedores as v', 'p.id_vendedor', '=', 'v.id_vendedor')
            ->select(
                'p.id_producto',
                'p.nombre',
                'p.descripcion',
                'p.precio',
                'p.stock',
                'p.imagen_url',
                'p.id_vendedor',
                'v.nombre_negocio',
                'v.ubicacion as tienda_ubicacion',
                'v.descripcion as tienda_descripcion'
            )
            ->get();

        return view('productos', compact('productos'));
    }

    public function show($id)
    {
        $producto = DB::table('productos as p')
            ->join('vendedores as v', 'p.id_vendedor', '=', 'v.id_vendedor')
            ->select(
                'p.id_producto',
                'p.nombre',
                'p.descripcion',
                'p.precio',
                'p.stock',
                'p.imagen_url',
                'p.id_vendedor',
                'v.nombre_negocio',
                'v.ubicacion as tienda_ubicacion',
                'v.descripcion as tienda_descripcion'
            )
            ->where('p.id_producto', $id)
            ->first();

        if (!$producto) {
            return redirect('/productos')->with('error', 'Producto no encontrado');
        }

        return view('detalle-producto', compact('producto'));
    }
}
