<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $comercios = DB::table('vendedores')->get();

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
            ->when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('p.nombre', 'ilike', "%{$search}%")
                        ->orWhere('p.descripcion', 'ilike', "%{$search}%")
                        ->orWhere('v.nombre_negocio', 'ilike', "%{$search}%");
                });
            })
            ->get();

        return view('productos', compact('productos', 'comercios'));
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
